<?php

namespace Ingenico\Connect\OroCommerce\Controller\Frontend;

use Ingenico\Connect\OroCommerce\Method\Provider\IngenicoPaymentMethodProvider;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Basic controller for getting session, should be reviewed in scope of INGA-25
 */
class IngenicoController extends AbstractController
{
    /**
     * @Route("/create-session", name="ingenico.create-session")
     *
     * @return string
     */
    public function actionCreateSession()
    {
        $payment = $this->get(IngenicoPaymentMethodProvider::class)->getPaymentMethod('ingenico_1');
        $response = $payment->execute('createSession', new PaymentTransaction());

        return $this->json($response);
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                IngenicoPaymentMethodProvider::class,
            ]
        );
    }
}
