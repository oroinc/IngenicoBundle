<?php
namespace Ingenico\Connect\OroCommerce\Method;

use Ingenico\Connect\OroCommerce\Method\Handler\PaymentProductGroupHandlerRegistry;
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
    /**
     * @var PaymentConfigInterface
     */
    private $config;

    /**
     * @var PaymentProductGroupHandlerRegistry
     */
    private $paymentProductGroupHandlersRegistry;

    /**
     * @param PaymentConfigInterface $config
     * @param PaymentProductGroupHandlerRegistry $paymentProductHandlersRegistry
     */
    public function __construct(
        PaymentConfigInterface $config,
        PaymentProductGroupHandlerRegistry $paymentProductHandlersRegistry
    ) {
        $this->config = $config;
        $this->paymentProductGroupHandlersRegistry = $paymentProductHandlersRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($action, PaymentTransaction $paymentTransaction): array
    {
        $paymentProductGroupHandler =
            $this->paymentProductGroupHandlersRegistry->getPaymentProductGroupHandler($paymentTransaction);

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
     * @inheritdoc
     */
    public function getSourceAction(): string
    {
        return self::PENDING;
    }

    /**
     * @inheritDoc
     */
    public function useSourcePaymentTransaction(): bool
    {
        return true;
    }
}
