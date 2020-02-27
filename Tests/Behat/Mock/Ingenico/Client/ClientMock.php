<?php

namespace Ingenico\Connect\OroCommerce\Tests\Behat\Mock\Ingenico\Client;

use Ingenico\Connect\OroCommerce\Ingenico\Client\Client;
use Ingenico\Connect\OroCommerce\Method\Config\IngenicoConfig;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ClientMock extends Client
{
    /**
     * @var string
     */
    private $responseDirectory = __DIR__ . '/../../../DataFixtures/response';

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * Do not use any parent dependencies.
     */
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function send(IngenicoConfig $paymentConfig, string $resource, string $action, array $body = [])
    {
        $fileName = $this->getResponseFileName($resource, $action);
        $filePath = $this->getFilePath($body);
        $fullFilePath = $filePath . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($fullFilePath)) {
            return [];
        }

        return json_decode(file_get_contents($fullFilePath), true);
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return string
     */
    private function getResponseFileName(string $resource, string $action): string
    {
        return sprintf('%s-%s.json', $resource, $action);
    }

    /**
     * @param array $body
     *
     * @return string
     */
    private function getFilePath(array $body): string
    {
        $dataObject = end($body);

        foreach ($this->getFilePathMatchRulesForPayload() as $directory => $matchRule) {
            $matched = true;
            foreach ($matchRule as $propertyPath => $expectedValue) {
                $value = $this->getObjectPropertyValue($dataObject, $propertyPath);
                $matched = $matched && $value !== null && ($value === $expectedValue || $expectedValue === null);
            }

            if ($matched) {
                return $this->responseDirectory . DIRECTORY_SEPARATOR . $directory;
            }
        }

        return $this->responseDirectory;
    }

    /**
     * @return array
     */
    private function getFilePathMatchRulesForPayload(): array
    {
        return [
            EnabledProductsDataProvider::SEPA => [
                'sepaDirectDebitPaymentMethodSpecificInput.token' => null,
            ],
            EnabledProductsDataProvider::CREDIT_CARDS . DIRECTORY_SEPARATOR . 'preauth' => [
                'encryptedCustomerInput' => EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID,
                'cardPaymentMethodSpecificInput.authorizationMode' => PaymentActionDataProvider::PRE_AUTHORIZATION,
            ],
            EnabledProductsDataProvider::CREDIT_CARDS . DIRECTORY_SEPARATOR . 'finalauth' => [
                'encryptedCustomerInput' => EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID,
                'cardPaymentMethodSpecificInput.authorizationMode' => PaymentActionDataProvider::FINAL_AUTHORIZATION,
            ],
            EnabledProductsDataProvider::CREDIT_CARDS . DIRECTORY_SEPARATOR . 'sale' => [
                'encryptedCustomerInput' => EnabledProductsDataProvider::CREDIT_CARDS_GROUP_ID,
                'cardPaymentMethodSpecificInput.authorizationMode' => PaymentActionDataProvider::SALE,
            ],
            EnabledProductsDataProvider::ACH => [
                'encryptedCustomerInput' => EnabledProductsDataProvider::ACH_ID,
            ],
        ];
    }

    /**
     * @param $object
     * @param $propertyPath
     * @return mixed
     */
    private function getObjectPropertyValue($object, $propertyPath)
    {
        try {
            return $this->propertyAccessor->getValue($object, $propertyPath);
        } catch (\Exception $e) {
            return null;
        }
    }
}
