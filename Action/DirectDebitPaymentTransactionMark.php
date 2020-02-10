<?php

namespace Ingenico\Connect\OroCommerce\Action;

use Oro\Bundle\PaymentBundle\Action\AbstractPaymentMethodAction;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Action to mark ingenico related pending payment transaction as paid or declined.
 * It's a direct debit payment products related flow.
 */
class DirectDebitPaymentTransactionMark extends AbstractPaymentMethodAction
{
    public const OPTION_PAYMENT_TRANSACTION = 'paymentTransaction';
    public const OPTION_PAYMENT_TRANSACTION_MARK_AS_PAID = 'markAsPaid';

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        parent::configureOptionsResolver($resolver);
        $this->commonConfigureOptionsResolver($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureValuesResolver(OptionsResolver $resolver)
    {
        parent::configureValuesResolver($resolver);
        $this->commonConfigureOptionsResolver($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function commonConfigureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver
            ->remove(['object', 'amount', 'currency', 'paymentMethod'])
            ->setRequired(
                [self::OPTION_PAYMENT_TRANSACTION, self::OPTION_PAYMENT_TRANSACTION_MARK_AS_PAID]
            )->addAllowedTypes(
                self::OPTION_PAYMENT_TRANSACTION,
                [PaymentTransaction::class, PropertyPathInterface::class]
            )->addAllowedTypes(self::OPTION_PAYMENT_TRANSACTION_MARK_AS_PAID, 'bool');
    }

    /**
     * {@inheritDoc}
     */
    protected function executeAction($context)
    {
        $options = $this->getOptions($context);

        $pendingPaymentTransaction = $this->extractPaymentTransactionFromOptions($options);
        if (!$this->paymentMethodProvider->hasPaymentMethod($pendingPaymentTransaction->getPaymentMethod())) {
            $this->setAttributeValue(
                $context,
                [
                    'transaction' => $pendingPaymentTransaction->getId(),
                    'successful' => false,
                    'message' => 'oro.payment.message.error',
                ]
            );

            return;
        }

        $markedPaymentTransaction = $this->paymentTransactionProvider->createPaymentTransactionByParentTransaction(
            PaymentMethodInterface::PURCHASE,
            $pendingPaymentTransaction
        );

        $markAsPaid = $this->extractPaymentTransactionMarkAsPaidFromOptions($options);
        $pendingPaymentTransaction->setActive(false)
            ->setSuccessful($markAsPaid);
        $markedPaymentTransaction->setActive(false)
            ->setSuccessful($markAsPaid)
            ->setReference($pendingPaymentTransaction->getReference());

        $this->paymentTransactionProvider->savePaymentTransaction($markedPaymentTransaction);
        $this->paymentTransactionProvider->savePaymentTransaction($pendingPaymentTransaction);

        $this->setAttributeValue(
            $context,
            [
                'transaction' => $markedPaymentTransaction->getId(),
                'successful' => true,
                'message' => null,
            ]
        );
    }

    /**
     * @param array $options
     *
     * @return PaymentTransaction
     */
    private function extractPaymentTransactionFromOptions(array $options): PaymentTransaction
    {
        return $options[self::OPTION_PAYMENT_TRANSACTION];
    }

    /**
     * @param array $options
     *
     * @return bool
     */
    private function extractPaymentTransactionMarkAsPaidFromOptions(array $options): bool
    {
        return $options[self::OPTION_PAYMENT_TRANSACTION_MARK_AS_PAID];
    }
}
