<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @param list<string> $supportedLocales
     */
    public function __construct(
        private readonly string $defaultLocale,
        private readonly array $supportedLocales,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $locale = $this->defaultLocale;

        if ($request->hasSession()) {
            $sessionLocale = $request->getSession()->get('_locale');
            if (is_string($sessionLocale) && in_array($sessionLocale, $this->supportedLocales, true)) {
                $locale = $sessionLocale;
            }
        }

        $requestLocale = $request->query->get('locale');
        if (is_string($requestLocale) && in_array($requestLocale, $this->supportedLocales, true)) {
            $locale = $requestLocale;

            if ($request->hasSession()) {
                $request->getSession()->set('_locale', $locale);
            }
        }

        $request->setLocale($locale);
    }
}