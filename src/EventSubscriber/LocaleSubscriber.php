<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sets the request locale from the "_locale" query parameter or the session.
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var array<int, string> */
    private const SUPPORTED_LOCALES = ['pl', 'en'];

    /**
     * On kernel request.
     *
     * @param RequestEvent $event Event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $locale = $request->query->get('_locale');
        if (is_string($locale) && in_array($locale, self::SUPPORTED_LOCALES, true)) {
            if ($request->hasSession()) {
                $request->getSession()->set('_locale', $locale);
            }
            $request->setLocale($locale);

            return;
        }

        if ($request->hasSession() && $request->getSession()->has('_locale')) {
            $request->setLocale((string) $request->getSession()->get('_locale'));
        }
    }

    /**
     * @return array<string, array<int, array<int, int|string>>>
     */
    public static function getSubscribedEvents(): array
    {
        // Run before Symfony's default LocaleListener (priority 16).
        return [KernelEvents::REQUEST => [['onKernelRequest', 20]]];
    }
}
