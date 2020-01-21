<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionsResolver;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Session\PaymentProductFilter\Restrict;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\Sdk\DataObject;
use Ingenico\Connect\Sdk\Domain\Sessions\SessionRequest;

/**
 * Handle create session request.
 */
class CreateSessionRequest implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->addOption(new Restrict\Groups())
            ->addOption(new Restrict\Products());
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType(): string
    {
        return Transaction::CREATE_SESSION;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource(): string
    {
        return 'sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'create';
    }

    /**
     * {@inheritdoc}
     */
    public function createOriginalRequest(): DataObject
    {
        return new SessionRequest();
    }
}
