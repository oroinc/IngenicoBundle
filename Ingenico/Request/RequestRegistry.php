<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request;

/**
 * Registry for collecting all Ingenico requests
 */
class RequestRegistry
{
    /** @var RequestInterface[] */
    private $requests = [];

    /**
     * @param iterable $requests
     */
    public function __construct(iterable $requests)
    {
        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * @param RequestInterface $request
     * @throws \InvalidArgumentException
     */
    public function addRequest(RequestInterface $request): void
    {
        $transactionType = $request->getTransactionType();
        if (array_key_exists($transactionType, $this->requests)) {
            throw new \InvalidArgumentException(
                sprintf('Transaction type "%s" already exists in the request registry', $transactionType)
            );
        }

        $this->requests[$request->getTransactionType()] = $request;
    }

    /**
     * @param string $transactionType
     * @return RequestInterface
     * @throws \InvalidArgumentException
     */
    public function getRequest(string $transactionType): RequestInterface
    {
        if (!array_key_exists($transactionType, $this->requests)) {
            throw new \InvalidArgumentException(
                sprintf('Transaction type "%s" does not exist in the request registry', $transactionType)
            );
        }

        return $this->requests[$transactionType];
    }
}
