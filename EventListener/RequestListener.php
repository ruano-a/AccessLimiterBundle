<?php

namespace ruano_a\AccessLimiterBundle\EventListener;

use ruano_a\AccessLimiterBundle\Service\FailAccessAttemptService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RequestListener
{
    private $templating;
    private $failAccessAttemptService;
    private $passwords;
    private $active;
    private $templatePath;

    const SESSION_VAR = 'AccessLimiter_ALLOWED';
    const PASSWORD_INPUT_NAME = 'password';

    public function __construct(Environment $templating, FailAccessAttemptService $failAccessAttemptService,
                                 array $passwords, bool $active, string $templatePath)
    {
        $this->templating = $templating;
        $this->failAccessAttemptService = $failAccessAttemptService;
        $this->passwords = $passwords;
        $this->active = $active;
        $this->templatePath = $templatePath;
    }

    protected function isAllowed(Request $request): bool
    {
        return ($request->getSession()->get(self::SESSION_VAR) == true);
    }

    protected function setAllowed(Request $request): void
    {
        $request->getSession()->set(self::SESSION_VAR, true);
    }

    protected function getResponse(string $errorMessage = null): Response
    {
        return new Response($this->templating->render($this->templatePath, ['error' => $errorMessage]));
    }

    protected function checkPassword(string $password): bool
    {
        return (in_array($password, $this->passwords));
    }

    protected function containsFormData(Request $request): bool
    {
        // todo, see the dump of the request
        return ($request->request->get(self::PASSWORD_INPUT_NAME) != null);
    }

    protected function getPassword(Request $request): ?string
    {
        return ($request->request->get(self::PASSWORD_INPUT_NAME));
    }

    protected function isActive(): bool
    {
        return ($this->active);
    }

    // for now, let's say it's only by password
    protected function handleNotAllowedRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $ip = $request->getClientIp();

        if ($this->containsFormData($request))
        {
            if ($this->failAccessAttemptService->hasFailedTooManyTimes($ip))
            {
                $event->setResponse($this->getResponse('Too many attempts'));
            }
            else if ($this->checkPassword($this->getPassword($request)))
            {
                $this->setAllowed($request);
                $this->failAccessAttemptService->clearFails($ip);
            }
            else
            {
                $this->failAccessAttemptService->noteFail($ip);
                $this->failAccessAttemptService->logFail($request, $this->getPassword($request));
                $event->setResponse($this->getResponse('Wrong password'));
            }
        }
        else
        {
            $event->setResponse($this->getResponse());
        }
    }
    
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->isActive() || !$event->isMasterRequest() || $this->isAllowed($event->getRequest())) {
            // don't do anything if it's not the master request
            return;
        }
        $this->handleNotAllowedRequest($event);
    }
}