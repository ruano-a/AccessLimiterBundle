<?php

namespace ruano-a\AccessLimiterBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
	public function __construct()
	{

	}
	
	protected function isAllowed()
	{

	}

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() || $this->isAllowed()) {
            // don't do anything if it's not the master request
            return;
        }

        // ...
    }
}