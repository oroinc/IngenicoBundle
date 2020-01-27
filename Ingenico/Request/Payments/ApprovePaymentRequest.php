<?php


namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\Capture\Amount;
use Ingenico\Connect\OroCommerce\Ingenico\Request\ActionParamsAwareInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\Sdk\DataObject;

/**
 * Handle approve(capture) payment request.
 */
class ApprovePaymentRequest implements RequestInterface, ActionParamsAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addOption(new Amount());
    }

    /**
     * {@inheritdoc}
     */
    public function configureActionParamsOptions(OptionsResolver $resolver): void
    {
        $resolver->addOption(new PaymentId());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::APPROVE_PAYMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource(): string
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'approve';
    }

    /**
     * {@inheritdoc}
     */
    public function createOriginalRequest(): DataObject
    {
        return new \Ingenico\Connect\Sdk\Domain\Payment\ApprovePaymentRequest();
    }
}
