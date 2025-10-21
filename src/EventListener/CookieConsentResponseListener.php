<?php

namespace Stefpe\SpConsentBundle\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::RESPONSE)]
final class CookieConsentResponseListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        
        if (!$request->hasSession()) {
            return;
        }
        
        $session = $request->getSession();
        
        // Check for cookie set by the live component
        $cookie = $session->get('sp_consent_cookie');
        if ($cookie) {
            $response->headers->setCookie($cookie);
            $session->remove('sp_consent_cookie');
        }
    }
}
