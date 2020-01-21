<?php

namespace Ingenico\Connect\OroCommerce\Ingenico\Request;

/**
 * Registry for collection all available requests.
 */
class RequestRegistry
{
    /** @var RequestInterface[]|array */
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
     */
    public function addRequest(RequestInterface $request): void
    {
        $this->requests[$request->getTransactionType()] = $request;
    }

    /**
     * @param string $transactionType
     *
     * @return RequestInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getRequest(string $transactionType): RequestInterface
    {
        if (!array_key_exists($transactionType, $this->requests)) {
            throw new \InvalidArgumentException('not such transaction');
        }

        return $this->requests[$transactionType];
    }
}
