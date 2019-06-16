<?php

namespace ruano_a\AccessLimiterBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class RequestListener
{
    private $templating;
    const VIEW_PATH = 'gate.html.twig';

	public function __construct(EngineInterface $templating)
	{
        $this->templating = $templating;
	}
	
	protected function isAllowed()
	{
        return (false);
	}

    protected function getResponse(string $errorMessage = null)
    {
        return $this->templating->renderResponse(self::VIEW_PATH, ['error' -> $errorMessage]);
    }

    protected function checkPassword(string $password)
    {
        return (false);
    }

    protected function handleNotAllowedRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        dump($request);
        exit();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() || $this->isAllowed()) {
            // don't do anything if it's not the master request
            return;
        }
        $this->handleNotAllowedRequest($event);
    }
}