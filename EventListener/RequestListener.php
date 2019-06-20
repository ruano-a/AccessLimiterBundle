<?php

namespace ruano_a\AccessLimiterBundle\EventListener;

use ruano_a\AccessLimiterBundle\Service\FailAccessAttemptService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RequestListener
{
    private $templating;
    private $failAccessAttemptService;
    private $passwords;
    private $active;

    const VIEW_PATH = '@AccessLimiter/gate.html.twig';
    const SESSION_VAR = 'AccessLimiter_ALLOWED';
    const PASSWORD_VAR_NAME = 'password';

    public function __construct(Environment $templating, FailAccessAttemptService $failAccessAttemptService,
                                 array $passwords, bool $active)
    {
        $this->templating = $templating;
        $this->failAccessAttemptService = $failAccessAttemptService;
        $this->passwords = $passwords;
        $this->active = $active;
    }

    protected function isAllowed($request)
    {
        return ($request->getSession()->get(self::SESSION_VAR) == true);
    }

    protected function setAllowed($request)
    {
        $request->getSession()->set(self::SESSION_VAR, true);
    }

    protected function getResponse(string $errorMessage = null)
    {
        return new Response($this->templating->render(self::VIEW_PATH, ['error' => $errorMessage]));
    }

    protected function checkPassword(string $password)
    {
        return (in_array($password, $this->passwords));
    }

    protected function containsFormData($request)
    {
        // todo, see the dump of the request
        return ($request->request->get(self::PASSWORD_VAR_NAME) != null);
    }

    protected function getPassword($request)
    {
        return ($request->request->get(self::PASSWORD_VAR_NAME));
    }

    protected function isActive()
    {
        return ($this->active);
    }

    // for now, let's say it's only by password
    protected function handleNotAllowedRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $ip = $request->getClientIp();

        if ($this->containsFormData($request))
        {
            if ($failAccessAttemptService->hasFailedTooManyTimes($ip))
            {
                $event->setResponse($this->getResponse('Too many tries'));
            }
            else if ($this->checkPassword($this->getPassword($request)))
            {
                $this->setAllowed($request);
                $failAccessAttemptService->clearFails($ip);
            }
            else
            {
                $failAccessAttemptService->noteFail($ip);
                $failAccessAttemptService->logFail($request, $this->getPassword($request));
                $event->setResponse($this->getResponse('Wrong password'));
            }
        }
        else
        {
            $event->setResponse($this->getResponse());
        }
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->isActive() || !$event->isMasterRequest() || $this->isAllowed($event->getRequest())) {
            // don't do anything if it's not the master request
            return;
        }
        $this->handleNotAllowedRequest($event);
    }
}