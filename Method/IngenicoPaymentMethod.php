<?php
namespace Ingenico\Connect\OroCommerce\Method;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Groups;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Products;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Handler\PaymentProductHandlerRegistry;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\Action\CaptureActionInterface;
use Oro\Bundle\PaymentBundle\Method\Action\PurchaseActionInterface;
use Oro\Bundle\PaymentBundle\Method\Config\PaymentConfigInterface;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * Payment method class that describes top level business logic of Ingenico payment method
 */
class IngenicoPaymentMethod implements PaymentMethodInterface, CaptureActionInterface, PurchaseActionInterface
{
    public const CREATE_SESSION_ACTION = 'createSession';

    /** @var PaymentConfigInterface */
    private $config;

    /** @var PaymentProductHandlerRegistry */
    private $paymentProductGroupHandlersRegistry;

    /** @var Gateway */
    private $gateway;

    /**
     * @param PaymentConfigInterface $config
     * @param PaymentProductHandlerRegistry $paymentProductHandlersRegistry
     * @param Gateway $gateway
     */
    public function __construct(
        PaymentConfigInterface $config,
        PaymentProductHandlerRegistry $paymentProductHandlersRegistry,
        Gateway $gateway
    ) {
        $this->config = $config;
        $this->paymentProductGroupHandlersRegistry = $paymentProductHandlersRegistry;
        $this->gateway = $gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($action, PaymentTransaction $paymentTransaction)
    {
        if (self::CREATE_SESSION_ACTION === $action) {
            return $this->createSession();
        }

        $paymentProductGroupHandler =
            $this->paymentProductGroupHandlersRegistry->getPaymentProductHandler($paymentTransaction);

        if (null === $paymentProductGroupHandler || !$this->supports($action)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" payment method "%s" action is not supported', $this->getIdentifier(), $action)
            );
        }

        return $paymentProductGroupHandler->execute($action, $paymentTransaction, $this->config);
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
    public function purchase(PaymentTransaction $paymentTransaction): array
    {
        return [];
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
        return self::PENDING;
    }

    /**
     * {@inheritdoc}
     */
    public function useSourcePaymentTransaction(): bool
    {
        return true;
    }

    /**
     * @return array
     * @throws \JsonException
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
        $allowedMethods = $this->config->getEnabledProducts();
        $requestBody = [Products::NAME => []];
        foreach ($allowedMethods as $method) {
            switch ($method) {
                case $method === EnabledProductsDataProvider::CREDIT_CARDS:
                    $requestBody[Groups::NAME] = [EnabledProductsDataProvider::CREDIT_CARDS];
                    break;
                case $method === EnabledProductsDataProvider::SEPA:
                    $requestBody[Products::NAME][] = EnabledProductsDataProvider::SEPA_ID;
                    break;
                case $method === EnabledProductsDataProvider::ACH:
                    $requestBody[Products::NAME][] = EnabledProductsDataProvider::ACH_ID;
                    break;
            }
        }

        if (!count($requestBody[Products::NAME])) {
            unset($requestBody[Products::NAME]);
        }

        return $requestBody;
    }
}
