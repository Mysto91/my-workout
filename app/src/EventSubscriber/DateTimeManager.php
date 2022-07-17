<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\InvalidDateTimeException;
use App\Exception\ProductNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

final class DateTimeManager implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['checkDateTimeFormat', EventPriorities::PRE_VALIDATE],
        ];
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function checkDateTimeFormat(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (! $exception instanceof NotNormalizableValueException) {
            return;
        }

        if (str_contains(strtolower($exception->getMessage()), 'datetime')) {
            $response = new JsonResponse(
                [
                    '@context' => '/api/contexts/Error',
                    '@type' => 'hydra:Error',
                    'hydra:title' => 'An error occurred',
                    'hydra:description' => 'The date time is invalid format.',
                ],
                400
            );
            $event->setResponse($response);
        }
    }
}
