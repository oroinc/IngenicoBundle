<?php

namespace Ingenico\Connect\OroCommerce\Method\Handler;

use Ingenico\Connect\OroCommerce\Exception\InsufficientDataException;
use Ingenico\Connect\OroCommerce\Ingenico\Option\Payment\PaymentProductId;
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
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
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
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return array
     */
    public function purchase(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): array {
        $paymentTransaction->setSuccessful(false);

        $tokenResponse = $this->createToken($paymentTransaction, $config);
        if ($tokenResponse->isSuccessful()) {
            $paymentResponse = $this->requestCreatePayment(
                $paymentTransaction,
                $config,
                $this->getCreatePaymentAdditionalOptions($config, $tokenResponse)
            );

            $paymentTransaction
                ->setSuccessful($paymentResponse->isSuccessful())
                ->setActive($paymentResponse->isSuccessful())
                ->setAction(PaymentMethodInterface::PENDING)
                ->setReference($paymentResponse->getReference())
                ->setResponse($paymentResponse->toArray());
        } else {
            $paymentTransaction
                ->setActive(false)
                ->setResponse($tokenResponse->toArray());
        }

        return [
            'purchaseSuccessful' => $paymentTransaction->isSuccessful(),
        ];
    }

    /**
     * @param IngenicoConfig $config
     * @param TokenResponse $tokenResponse
     * @return array
     */
    private function getCreatePaymentAdditionalOptions(IngenicoConfig $config, TokenResponse $tokenResponse): array
    {
        return [
            Token::NAME => $tokenResponse->getToken(),
            DirectDebitText::NAME => $config->getDirectDebitText(),
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
        return EnabledProductsDataProvider::SEPA_ID;
    }

    /**
     * @param PaymentTransaction $paymentTransaction
     * @param IngenicoConfig $config
     * @return TokenResponse
     */
    private function createToken(
        PaymentTransaction $paymentTransaction,
        IngenicoConfig $config
    ): TokenResponse {

        $billingAddress = $this->checkoutInformationProvider->getBillingAddress($paymentTransaction);

        if (!$billingAddress) {
            throw new InsufficientDataException('Can not extract billing address from the payment transaction');
        }

        $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        /** @var TokenResponse $response */
        $response = $this->gateway->request(
            $config,
            Transaction::CREATE_SEPA_DIRECT_DEBIT_PAYMENT_TOKEN,
            [
                CountryCode::NAME => $billingAddress->getCountryIso2(),
                DebtorSurname::NAME => $this->getDebtorSurname($billingAddress),
                AccountHolderName::NAME => $this->getAdditionalDataFieldByKey(
                    $paymentTransaction,
                    self::ACCOUNT_HOLDER_NAME_OPTION_KEY
                ),
                MandateApproval\MandateSignaturePlace::NAME => $billingAddress->getCity(),
                Iban::NAME => $this->getAdditionalDataFieldByKey($paymentTransaction, self::IBAN_OPTION_KEY),
                PaymentProductId::NAME => EnabledProductsDataProvider::SEPA_ID,
                MandateApproval\MandateSignatureDate::NAME => $currentDateTime->format('Ymd'),
            ],
        );

        return TokenResponse::create($response->toArray());
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionSupported(string $actionName): bool
    {
        return $actionName === PaymentMethodInterface::PURCHASE;
    }

    /**
     * @param AbstractAddress $billingAddress
     * @return string
     */
    private function getDebtorSurname(AbstractAddress $billingAddress)
    {
        if ($billingAddress->getLastName()) {
            return $billingAddress->getLastName();
        }

        throw new InsufficientDataException('Can not extract debtor surname from the billing address.');
    }
}
