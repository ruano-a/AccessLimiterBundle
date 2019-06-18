<?php

namespace ruano_a\AccessLimiterBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RequestListener
{
    private $templating;
    const VIEW_PATH = '@AccessLimiter/gate.html.twig';
    const SESSION_VAR = 'AccessLimiter_ALLOWED';

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    protected function isAllowed()
    {
       $session = null;

       //return ($session->get(self::SESSION_VAR));
       return (false);
    }

    protected function setAllowed()
    {
        $session = truc;
        $session->set(self::SESSION_VAR, true);
    }

    protected function getResponse(string $errorMessage = null)
    {
        return new Response($this->templating->render(self::VIEW_PATH, ['error' => $errorMessage]));
    }

    protected function checkPassword(string $password)
    {
        $passwords = STUFF;

        return (in_array($password, $passwords));
        return (false);
    }

    protected function containsFormData($request)
    {
        // todo, see the dump of the request
        return (false);
    }

    protected function isActive()
    {
        //return ($activeParam);
        return (true);
    }

    // for now, let's say it's only by password
    protected function handleNotAllowedRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($this->containsFormData($request))
        {
            if ($this->checkPassword($request->getPassword()))
            {
                $this->setAllowed();
            }
            else
            {
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
        if (!$this->isActive() || !$event->isMasterRequest() || $this->isAllowed()) {
            // don't do anything if it's not the master request
            return;
        }
        $this->handleNotAllowedRequest($event);
    }
}