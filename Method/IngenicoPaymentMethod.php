<?php

namespace Ingenico\Connect\OroCommerce\Method;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Groups;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Products;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\Handler\PaymentProductHandlerRegistry;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Action\CaptureActionInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * Payment method class that describes top level business logic of Ingenico payment method
 */
class IngenicoPaymentMethod implements PaymentMethodInterface, CaptureActionInterface
{
    /** @var IngenicoConfig */
    private $config;

    /** @var PaymentProductHandlerRegistry */
    private $paymentProductHandlerRegistry;

    /** @var Gateway */
    private $gateway;

    /**
     * @param IngenicoConfig $config
     * @param PaymentProductHandlerRegistry $paymentProductHandlerRegistry
     * @param Gateway $gateway
     */
    public function __construct(
        IngenicoConfig $config,
        PaymentProductHandlerRegistry $paymentProductHandlerRegistry,
        Gateway $gateway
    ) {
        $this->config = $config;
        $this->paymentProductHandlerRegistry = $paymentProductHandlerRegistry;
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($action, PaymentTransaction $paymentTransaction)
    {
        $paymentProductHandler =
            $this->paymentProductHandlerRegistry->getPaymentProductHandler($paymentTransaction);

        if (null === $paymentProductHandler) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Cannot find payment product handler for transaction, with id="%d", action="%s"',
                    $paymentTransaction->getId(),
                    $paymentTransaction->getAction()
                )
            );
        }
        if (!$this->supports($action)) {
            throw new \InvalidArgumentException(
                sprintf('Payment handler "%s" doesn\'t support "%s" action', get_class($paymentProductHandler), $action)
            );
        }

        return $paymentProductHandler->execute($action, $paymentTransaction, $this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(PaymentContextInterface $context)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($actionName)
    {
        return in_array($actionName, [self::PURCHASE, self::CAPTURE], true);
    }

    /**
     * {@inheritdoc}
     */
    public function capture(PaymentTransaction $paymentTransaction): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceAction(): string
    {
        return self::AUTHORIZE;
    }

    /**
     * {@inheritdoc}
     */
    public function useSourcePaymentTransaction(): bool
    {
        return false;
    }

    /**
     * Create client session to allow interacting from Client JS SDK
     *
     * @return array
     */
    public function createSession(): array
    {
        $response = $this->gateway->request(
            $this->config,
            Transaction::CREATE_SESSION,
            $this->prepareCreateSessionRequestBody()
        );

        return $response->toArray();
    }

    /**
     * @return array
     */
    private function prepareCreateSessionRequestBody(): array
    {
        $enabledPaymentProducts = $this->config->getEnabledProducts();
        $requestBody = [];
        foreach ($enabledPaymentProducts as $paymentProduct) {
            switch ($paymentProduct) {
                case $paymentProduct === EnabledProductsDataProvider::CREDIT_CARDS:
                    $requestBody[Groups::NAME] = [EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID];
                    break;
                case $paymentProduct === EnabledProductsDataProvider::SEPA:
                    $requestBody[Products::NAME][] = EnabledProductsDataProvider::SEPA_ID;
                    break;
                case $paymentProduct === EnabledProductsDataProvider::ACH:
                    $requestBody[Products::NAME][] = EnabledProductsDataProvider::ACH_ID;
                    break;
            }
        }

        return $requestBody;
    }
}
