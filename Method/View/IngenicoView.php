<?php

namespace Ingenico\Connect\OroCommerce\Method\View;

use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Method\Handler\CreditCardPaymentProductHandler;
use Ingenico\Connect\OroCommerce\Normalizer\AmountNormalizer;
use Ingenico\Connect\OroCommerce\Provider\PaymentTransactionProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\CustomerBundle\Security\Token\AnonymousCustomerUserToken;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\PaymentBundle\Method\View\PaymentMethodViewInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param IngenicoConfig $config
     * @param string $currentLocalizationCode
     * @param TokenStorageInterface $tokenStorage
     * @param AmountNormalizer $amountNormalizer
     * @param PaymentTransactionProvider $paymentTransactionProvider
     */
    public function __construct(
        IngenicoConfig $config,
        string $currentLocalizationCode,
        TokenStorageInterface $tokenStorage,
        AmountNormalizer $amountNormalizer,
        PaymentTransactionProvider $paymentTransactionProvider
    ) {
        $this->config = $config;
        $this->currentLocalizationCode = $currentLocalizationCode;
        $this->tokenStorage = $tokenStorage;
        $this->amountNormalizer = $amountNormalizer;
        $this->paymentTransactionProvider = $paymentTransactionProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(PaymentContextInterface $context): array
    {
        return [
            'saveForLaterUseEnabled' => $this->config->isTokenizationEnabled() && !$this->isGuestCustomerUser(),
            'savedCreditCardList' => $this->getSavedCardList(),
            'paymentDetails' => [
                'totalAmount' => $this->amountNormalizer->normalize($context->getTotal()),
                'currency' => $context->getCurrency(),
                'countryCode' => $context->getBillingAddress()->getCountryIso2(),
                'isRecurring' => false,
                'locale' => $this->currentLocalizationCode,
                'debtorSurname' => $context->getBillingAddress()->getLastName(),
            ],
            'paymentProducts' => [
                'sepaId' => EnabledProductsDataProvider::SEPA_ID,
                'cardsGroup' => EnabledProductsDataProvider::CREDIT_CARDS,
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
        if (!$this->config->isTokenizationEnabled() || $this->isGuestCustomerUser()) {
            return [];
        }

        $cardList = [];
        $tokens = [];
        $paymentTransactions = $this->paymentTransactionProvider->getActiveTokenizePaymentTransactions(
            $this->config->getPaymentMethodIdentifier()
        );
        foreach ($paymentTransactions as $paymentTransaction) {
            $options = $paymentTransaction->getTransactionOptions();
            $hasAllRequiredData = isset(
                $options[CreditCardPaymentProductHandler::CREDIT_CARD_KEY],
                $options[CreditCardPaymentProductHandler::TOKEN_KEY],
                $options[CreditCardPaymentProductHandler::PAYMENT_PRODUCT_KEY]
            );
            if ($hasAllRequiredData && !in_array($options[CreditCardPaymentProductHandler::TOKEN_KEY], $tokens, true)
            ) {
                $paymentProduct = $options[CreditCardPaymentProductHandler::PAYMENT_PRODUCT_KEY];
                $creditCardMaskedNumber = $options[CreditCardPaymentProductHandler::CREDIT_CARD_KEY];

                $cardList[$paymentProduct][$paymentTransaction->getId()] = $creditCardMaskedNumber;
                $tokens[] = $options['token'];
            }
        }

        return $cardList;
    }

    /**
     * @return bool
     */
    private function isGuestCustomerUser()
    {
        return $this->tokenStorage->getToken() instanceof AnonymousCustomerUserToken;
    }
}
