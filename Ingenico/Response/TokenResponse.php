<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Response;

/**
 * Responsible for storing create token request's data from Ingenico server
 */
class TokenResponse extends Response
{
    public const TOKEN = 'token';

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->offsetGetByPath(self::TOKEN);
    }
}
