<?php

namespace Ingenico\Connect\OroCommerce\Controller\Frontend;

use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Ingenico\Connect\Sdk\ResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ingenico payment method's controller to process Client API data during checkout
 */
class IngenicoController extends AbstractController
{
    /**
     * @Route(
     *     "/create-session/{paymentMethod}",
     *     name="ingenico_create_session",
     *     options={"expose"=true},
     *     requirements={"paymentMethod"="[\w\:-]+"}
     * )
     *
     * @param IngenicoPaymentMethod $paymentMethod
     * @return JsonResponse
     */
    public function actionCreateSession(IngenicoPaymentMethod $paymentMethod): JsonResponse
    {
        $responseData = ['success' => true];

        try {
            $responseData['sessionInfo'] = $paymentMethod->createSession();
        } catch (\Throwable $e) {
            $context = ['paymentMethod' => $paymentMethod->getIdentifier(), 'exception' => $e];
            if ($e instanceof ResponseException) {
                $context['response'] = $e->getResponse();
            }

            $this->get(LoggerInterface::class)->error(
                'Cannot create client session for "{paymentMethod}" Ingenico payment integration',
                $context
            );
            $responseData['success'] = false;
        }

        return new JsonResponse($responseData);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                LoggerInterface::class,
            ]
        );
    }
}
