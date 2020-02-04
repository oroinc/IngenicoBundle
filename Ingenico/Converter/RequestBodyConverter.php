<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Converter;

use Ingenico\Connect\OroCommerce\Ingenico\Request\RequestInterface;
use Ingenico\Connect\Sdk\DataObject;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Converts request options to Ingenico request body.
 */
class RequestBodyConverter
{
    /** @var PropertyAccessor */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param RequestInterface $request
     * @param array $body
     *
     * @return DataObject
     */
    public function convert(RequestInterface $request, array $body): DataObject
    {
        $originalRequest = $request->createOriginalRequest();
        if (!empty($body)) {
            $jsonBody = $this->prepareBodyJson($body);
            $originalRequest->fromJson($jsonBody);
        }

        return $originalRequest;
    }

    /**
     * @param array $body
     *
     * @return string
     */
    private function prepareBodyJson(array $body)
    {
        $data = [];
        foreach ($body as $key => $value) {
            // Set values based on the property path in $key
            $this->propertyAccessor->setValue($data, $key, $value);
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
