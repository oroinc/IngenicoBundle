<?php

namespace Ingenico\Connect\OroCommerce\Controller\Frontend;

use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Ingenico\Connect\OroCommerce\Method\Provider\IngenicoPaymentMethodProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
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
     *     "/create-session/{paymentIdentifier}",
     *     name="ingenico.create-session",
     *     requirements={"paymentIdentifier"="[\w\:-]+"}
     * )
     *
     * @return JsonResponse
     */
    public function actionCreateSession($paymentIdentifier): JsonResponse
    {
        $responseData = ['success' => true, 'errorMessage' => ''];
        $payment = $this->get(IngenicoPaymentMethodProvider::class)->getPaymentMethod($paymentIdentifier);

        try {
            if ($payment) {
                $responseData['sessionInfo'] = $payment->execute(
                    IngenicoPaymentMethod::CREATE_SESSION_ACTION,
                    new PaymentTransaction()
                );
            } else {
                throw new \Exception('Wrong payment identifier is given');
            }
        } catch (\Exception $e) {
            $responseData['success'] = false;
            $responseData['errorMessage'] = $e->getMessage();
        }

        return new JsonResponse($responseData);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                IngenicoPaymentMethodProvider::class,
            ]
        );
    }
}
