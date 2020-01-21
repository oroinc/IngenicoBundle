<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request;

use Ingenico\Connect\OroCommerce\Ingenico\Option\OptionInterface;
use Ingenico\Connect\Sdk\DataObject;

/**
 * Interface for Request object.
 */
interface RequestInterface extends OptionInterface
{
    /**
     * @return string
     */
    public function getTransactionType(): string;

    /**
     * @return string
     */
    public function getResource(): string;

    /**
     * @return string
     */
    public function getAction(): string;

    /**
     * @return DataObject
     */
    public function createOriginalRequest(): DataObject;
}
