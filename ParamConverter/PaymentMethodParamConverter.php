<?php

namespace Ingenico\Connect\OroCommerce\ParamConverter;

use Ingenico\Connect\OroCommerce\Method\IngenicoPaymentMethod;
use Oro\Bundle\PaymentBundle\Method\Provider\PaymentMethodProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Param converter to get PaymentMethod based on the payment method identifier
 */
class PaymentMethodParamConverter implements ParamConverterInterface
{
    /**
     * @var PaymentMethodProviderInterface
     */
    protected $paymentMethodProvider;

    /**
     * @param PaymentMethodProviderInterface $paymentMethodProvider
     */
    public function __construct(PaymentMethodProviderInterface $paymentMethodProvider)
    {
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $paymentMethodIdentifier = $request->attributes->get($param);

        if ($this->paymentMethodProvider->hasPaymentMethod($paymentMethodIdentifier)) {
            $request->attributes->set($param, $this->paymentMethodProvider->getPaymentMethod($paymentMethodIdentifier));

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        return IngenicoPaymentMethod::class === $configuration->getClass();
    }
}
