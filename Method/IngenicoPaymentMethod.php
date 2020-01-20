<?php
namespace Ingenico\Connect\OroCommerce\Method;

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
    private $config;

    /**
     * @param PaymentConfigInterface $config
     */
    public function __construct(PaymentConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($action, PaymentTransaction $paymentTransaction): array
    {
        if (!method_exists($this, $action)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" payment method "%s" action is not supported', $this->getIdentifier(), $action)
            );
        }

        return $this->$action($paymentTransaction);
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return array
     */
    public function purchase(PaymentTransaction $paymentTransaction): array
    {
        $paymentTransaction->setSuccessful(true);

        return [];
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @return array
     */
    public function capture(PaymentTransaction $paymentTransaction): array
    {
        $paymentTransaction->setSuccessful(true);

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }

    /**
     * @param PaymentContextInterface $context
     * @return bool
     */
    public function isApplicable(PaymentContextInterface $context)
    {
        return true;
    }

    /**
     * @param string $actionName
     * @return bool
     */
    public function supports($actionName)
    {
        return in_array($actionName, [self::PURCHASE, self::CAPTURE], true);
    }

    /**
     * @inheritDoc
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
