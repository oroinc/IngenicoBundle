<?php
namespace Ingenico\Connect\OroCommerce\Method;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Groups;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict\Products;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
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
    /** @var PaymentConfigInterface */
    private $config;

    /** @var PaymentProductGroupHandlerRegistry */
    private $paymentProductGroupHandlersRegistry;

    /** @var Gateway */
    private $gateway;

    /**
     * @param PaymentConfigInterface $config
     * @param PaymentProductGroupHandlerRegistry $paymentProductHandlersRegistry
     * @param Gateway $gateway
     */
    public function __construct(
        PaymentConfigInterface $config,
        PaymentProductGroupHandlerRegistry $paymentProductHandlersRegistry,
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
        // should be reviewed in scope of INGA-25
        if ('createSession' === $action) {
            return $this->createSession();
        }

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
     * Just basic method for creating session, should be reviewed in scope of INGA-25
     *
     * @return array
     */
    public function createSession(): array
    {
        $response = $this->gateway->request(
            $this->config,
            Transaction::CREATE_SESSION,
            [
                Groups::NAME => ['cards'],
                Products::NAME => [730, 770]
            ]
        );

        return $response->toArray();
    }
}
