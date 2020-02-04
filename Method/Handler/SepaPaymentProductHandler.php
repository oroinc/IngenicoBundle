<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Ingenico\Gateway\Gateway;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\PaymenProducttId;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\DirectDebitText;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Customer\BillingAddress\CountryCode;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban\AccountHolderName;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\BankAccountIban\Iban;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\DebtorSurname;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\SepaPayment\Token\Mandate\MandateApproval;
use Ingenico\Connect\OroCommerce\Ingenico\Response\TokenResponse;
use Ingenico\Connect\OroCommerce\Ingenico\Transaction;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\OrderBundle\Entity\OrderAddress;
use Oro\Bundle\PaymentBundle\Entity\PaymentTransaction;
use Oro\Bundle\PaymentBundle\Method\PaymentMethodInterface;

/**
 * 'SEPA' payment product handler
 */
class SepaPaymentProductHandler extends AbstractPaymentProductHandler
{
    private const IBAN_OPTION_KEY = 'ingenicoSepaDetails:iban';
    private const ACCOUNT_HOLDER_NAME_OPTION_KEY = 'ingenicoSepaDetails:accountHolderName';

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var TokenResponse
     */
    private $lastTokenResponse;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway, DoctrineHelper $doctrineHelper)
    {
        parent::__construct($gateway);
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @throws \JsonException
     */
    public function purchase(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ) {
        $paymentTransaction->setSuccessful(false);

        $tokenResponse = $this->createToken($paymentTransaction, $config);
        if ($tokenResponse->isSuccessful()) {
            $paymentResponse = $this->requestCreatePayment($paymentTransaction, $config);

            $paymentTransaction
                ->setSuccessful($paymentResponse->isSuccessful())
                ->setActive($paymentResponse->isSuccessful())
                ->setAction(PaymentMethodInterface::PENDING)
                ->setReference($paymentResponse->getReference())
                ->setResponse($paymentResponse->toArray());
        } else {
            $paymentTransaction->setActive(false)
                ->setResponse($tokenResponse->toArray());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreatePaymentOptions(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        return [
            Token::NAME => $this->lastTokenResponse ? $this->lastTokenResponse->getToken() : null,
            // hardcoded value to be replaced with a value from payment integration's settings
            // @INGA-45 related.
            DirectDebitText::NAME => 'COMPANYNAME 123-123-1234 ZIP CODE UK'
        ];
    }

    /**
     * @return string
     */
    protected function getCreatePaymentTransactionType(): string
    {
        return Transaction::CREATE_SEPA_DIRECT_DEBIT_PAYMENT;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(): string
    {
        return EnabledProductsDataProvider::SEPA;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return TokenResponse
     * @throws \JsonException
     */
    private function createToken(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): TokenResponse {
        /** @var Order $order */
        $order = $this->doctrineHelper->getEntity(
            $paymentTransaction->getEntityClass(),
            $paymentTransaction->getEntityIdentifier()
        );

        if (!$order instanceof Order) {
            return TokenResponse::create([TokenResponse::ERROR_ID => -1]);
        }

        /** @var OrderAddress $billingAddress */
        $billingAddress = $order->getBillingAddress();

        /** @var TokenResponse $response */
        $response = $this->gateway->request(
            $config,
            Transaction::CREATE_SEPA_DIRECT_DEBIT_PAYMENT_TOKEN,
            [
                CountryCode::NAME => $billingAddress->getCountryIso2(),
                DebtorSurname::NAME => $billingAddress->getLastName(),
                AccountHolderName::NAME => $this->getTransactionOption(
                    $paymentTransaction,
                    self::ACCOUNT_HOLDER_NAME_OPTION_KEY
                ),
                MandateApproval\MandateSignaturePlace::NAME => substr($billingAddress->getCity(), 0, 51),
                Iban::NAME => $this->getTransactionOption($paymentTransaction, self::IBAN_OPTION_KEY),
                PaymenProducttId::NAME => EnabledProductsDataProvider::SEPA_ID
            ],
        );
        $this->lastTokenResponse = TokenResponse::create($response->toArray());

        return $this->lastTokenResponse;
    }
}
