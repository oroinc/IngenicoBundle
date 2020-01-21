<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Response;

use Oro\Component\Config\Common\ConfigObject;

/**
 * Responsible for storing response data from Ingenico server
 */
class Response extends ConfigObject
{
    public const ERROR_ID = 'errorId';
    public const ERRORS = 'errors';

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return !$this->hasErrors();
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->offsetExists(self::ERROR_ID);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->offsetGetOr(self::ERRORS, []);
    }
}
