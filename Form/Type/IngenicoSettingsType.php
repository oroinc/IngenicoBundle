<?php

namespace Ingenico\Connect\OroCommerce\Form\Type;

use Ingenico\Connect\OroCommerce\Entity\IngenicoSettings;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\EnabledProductsDataProvider;
use Ingenico\Connect\OroCommerce\Settings\DataProvider\PaymentActionDataProvider;
use Oro\Bundle\FormBundle\Form\Type\CheckboxType;
use Oro\Bundle\FormBundle\Form\Type\OroPlaceholderPasswordType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for managing IngenicoSetting entity.
 */
class IngenicoSettingsType extends AbstractType
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var PaymentActionDataProvider */
    private $paymentActionDataProvider;

    /** @var EnabledProductsDataProvider */
    private $enabledProductsDataProvider;

    /**
     * @param TranslatorInterface $translator
     * @param PaymentActionDataProvider $paymentActionDataProvider
     * @param EnabledProductsDataProvider $enabledProductsDataProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        PaymentActionDataProvider $paymentActionDataProvider,
        EnabledProductsDataProvider $enabledProductsDataProvider
    ) {
        $this->translator = $translator;
        $this->paymentActionDataProvider = $paymentActionDataProvider;
        $this->enabledProductsDataProvider = $enabledProductsDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $paymentActionTooltip = nl2br($this->translator->trans('ingenico.settings.paymentAction.tooltip'));

        $builder
            ->add('labels', LocalizedFallbackValueCollectionType::class, [
                'required' => true,
                'entry_options' => ['constraints' => [new NotBlank()]],
            ])
            ->add('shortLabels', LocalizedFallbackValueCollectionType::class, [
                'required' => true,
                'entry_options' => ['constraints' => [new NotBlank()]],
            ])
            ->add('apiKeyId', TextType::class)
            ->add('apiSecret', OroPlaceholderPasswordType::class)
            ->add('apiEndpoint', TextType::class)
            ->add('merchantId', TextType::class)
            ->add('enabledProducts', ChoiceType::class, [
                'choices' => $this->enabledProductsDataProvider->getAvailableProducts(),
                'choice_label' => function (string $action) {
                    return $this->translator->trans(
                        sprintf('ingenico.settings.enabledProducts.choice.%s', $action)
                    );
                },
                'multiple' => true,
            ])
            ->add('paymentAction', ChoiceType::class, [
                'choices' => $this->paymentActionDataProvider->getPaymentActions(),
                'choice_label' => function (string $action) {
                    return $this->translator->trans(
                        sprintf('ingenico.settings.paymentAction.choice.%s', $action)
                    );
                },
                'tooltip' => $paymentActionTooltip,
            ])
            ->add('tokenizationEnabled', CheckboxType::class, [
                'tooltip' => 'ingenico.settings.tokenizationEnabled.tooltip',
                'required' => false,
            ])
            ->add('directDebitText', TextType::class, [
                'tooltip' => 'ingenico.settings.directDebitText.tooltip',
                'required' => false,
            ])
            ->add('softDescriptor', TextType::class, [
                'tooltip' => 'ingenico.settings.softDescriptor.tooltip',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IngenicoSettings::class,
            'label_format' => 'ingenico.settings.%name%.label',
        ]);
    }
}
