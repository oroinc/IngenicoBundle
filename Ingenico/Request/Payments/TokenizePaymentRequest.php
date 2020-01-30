<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request\Payments;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\ActionParams\PaymentId;
use Ingenico\Connect\OroCommerce\Ingenico\Request\ActionParamsAwareInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\Sdk\DataObject;

/**
 * Handle request for creating token based on some payment.
 */
class TokenizePaymentRequest implements RequestInterface, ActionParamsAwareInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_TOKEN;
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
        return 'tokenize';
    }

    /**
     * {@inheritdoc}
     */
    public function createOriginalRequest(): DataObject
    {
        return new \Ingenico\Connect\Sdk\Domain\Payment\TokenizePaymentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function configureActionParamsOptions(OptionsResolver $resolver): void
    {
        $resolver->addOption(new PaymentId());
    }
}
