<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Converter;

use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\Sdk\DataObject;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Converts request options to Ingenico request body.
 */
class RequestBodyConverter
{
    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /**
     * @param RequestInterface $request
     * @param array $body
     *
     * @return DataObject
     *
     * @throws \JsonException
     */
    public function convert(RequestInterface $request, array $body): DataObject
    {
        $jsonBody = $this->prepareBodyJson($body);

        return $request->createOriginalRequest()->fromJson($jsonBody);
    }

    /**
     * @param array $body
     *
     * @return string
     *
     * @throws \JsonException
     */
    protected function prepareBodyJson(array $body)
    {
        $data = [];
        foreach ($body as $key => $value) {
            $this->getPropertyAccessor()->setValue($data, $key, $value);
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @return PropertyAccessor
     */
    private function getPropertyAccessor(): PropertyAccessor
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
