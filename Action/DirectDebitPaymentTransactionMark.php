<?php

namespace Ingenico\Connect\OroCommerce\Action;

use Oro\Bundle\PaymentBundle\Action\AbstractPaymentMethodAction;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Action to mark Ingenico related pending payment transaction as paid or declined.
 * It's a direct debit payment products related flow.
 */
class DirectDebitPaymentTransactionMark extends AbstractPaymentMethodAction
{
    public const OPTION_PAYMENT_TRANSACTION = 'paymentTransaction';
    public const OPTION_MARK_AS = 'markAs';

    private const MARK_AS_PAID = 'paid';
    private const MARK_AS_DECLINED = 'declined';

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        parent::configureOptionsResolver($resolver);

        $resolver
            ->remove(['object', 'amount', 'currency', 'paymentMethod'])
            ->setRequired([self::OPTION_PAYMENT_TRANSACTION, self::OPTION_MARK_AS])
            ->addAllowedTypes(
                self::OPTION_PAYMENT_TRANSACTION,
                [PaymentTransaction::class, PropertyPathInterface::class]
            )
            ->addAllowedValues(self::OPTION_MARK_AS, [self::MARK_AS_PAID, self::MARK_AS_DECLINED]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureValuesResolver(OptionsResolver $resolver)
    {
        parent::configureValuesResolver($resolver);
        $resolver
            ->remove(['object', 'amount', 'currency', 'paymentMethod'])
            ->setRequired([self::OPTION_PAYMENT_TRANSACTION, self::OPTION_MARK_AS])
            ->addAllowedTypes(
                self::OPTION_PAYMENT_TRANSACTION,
                [PaymentTransaction::class]
            )
            ->addAllowedValues(self::OPTION_MARK_AS, [self::MARK_AS_PAID, self::MARK_AS_DECLINED]);
    }

    /**
     * {@inheritDoc}
     */
    protected function executeAction($context)
    {
        $options = $this->getOptions($context);

        $basePaymentTransaction = $this->extractPaymentTransactionFromOptions($options);
        if (!$this->paymentMethodProvider->hasPaymentMethod($basePaymentTransaction->getPaymentMethod())) {
            $this->setAttributeValue(
                $context,
                [
                    'transaction' => $basePaymentTransaction->getId(),
                    'successful' => false,
                    'message' => 'oro.payment.message.error',
                ]
            );

            return;
        }

        $markedPaymentTransaction = $this->paymentTransactionProvider->createPaymentTransactionByParentTransaction(
            PaymentMethodInterface::PURCHASE,
            $basePaymentTransaction
        );

        $isSuccessful = $this->isSuccessful($options);

        $basePaymentTransaction
            ->setActive(false)
            ->setSuccessful($isSuccessful);

        $markedPaymentTransaction
            ->setActive(false)
            ->setSuccessful($isSuccessful);

        $this->paymentTransactionProvider->savePaymentTransaction($markedPaymentTransaction);
        $this->paymentTransactionProvider->savePaymentTransaction($basePaymentTransaction);

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
     * @return bool
     */
    private function isSuccessful(array $options): bool
    {
        $markAs = $this->extractMarkAsFromOptions($options);

        return $markAs === self::MARK_AS_PAID;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function extractMarkAsFromOptions(array $options): string
    {
        return $options[self::OPTION_MARK_AS];
    }
}
