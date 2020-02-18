<?php

namespace Ingenico\Connect\OroCommerce\Method\View;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;

/**
 * View for Ingenico payment method
 */
class IngenicoView implements PaymentMethodViewInterface
{
    /** @var IngenicoConfig */
    private $config;

    /** @var string */
    private $currentLocalizationCode;

    /** @var AmountNormalizer */
    private $amountNormalizer;

    /** @var PaymentTransactionProvider */
    private $paymentTransactionProvider;

    /**
     * @param IngenicoConfig $config
     * @param string $currentLocalizationCode
     * @param AmountNormalizer $amountNormalizer
     * @param PaymentTransactionProvider $paymentTransactionProvider
     */
    public function __construct(
        IngenicoConfig $config,
        string $currentLocalizationCode,
        AmountNormalizer $amountNormalizer,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        $this->config = $config;
        $this->currentLocalizationCode = $currentLocalizationCode;
        $this->amountNormalizer = $amountNormalizer;
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context): array
    {
        return [
            'saveForLaterUseEnabled' => $this->config->isTokenizationEnabled(),
            'savedCreditCardList' => $this->getSavedCardList(),
            'paymentDetails' => [
                'totalAmount' => $this->amountNormalizer->normalize($context->getTotal()),
                'currency' => $context->getCurrency(),
                'countryCode' => $context->getBillingAddress()->getCountryIso2(),
                'isRecurring' => false,
                'locale' => $this->currentLocalizationCode,
                'debtorSurname' => $context->getBillingAddress()->getLastName()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlock(): string
    {
        return '_payment_methods_ingenico_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->config->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getShortLabel(): string
    {
        return $this->config->getShortLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminLabel()
    {
        return $this->config->getAdminLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodIdentifier()
    {
        return $this->config->getPaymentMethodIdentifier();
    }

    /**
     * @return array
     */
    protected function getSavedCardList(): array
    {
        if ($this->config->isTokenizationEnabled()) {
            $cardList = [];
            $tokens = [];
            $paymentTransactions = $this->paymentTransactionProvider->getActiveTokenizePaymentTransactions(
                $this->config->getPaymentMethodIdentifier()
            );
            foreach ($paymentTransactions as $paymentTransaction) {
                $options = $paymentTransaction->getTransactionOptions();
                if (isset($options['cardNumber'], $options['token'], $options['paymentProduct']) &&
                    !in_array($options['token'], $tokens, true)
                ) {
                    $cardList[$options['paymentProduct']][$paymentTransaction->getId()] = $options['cardNumber'];
                    $tokens[] = $options['token'];
                }
            }

            return $cardList;
        }

        return [];
    }
}
