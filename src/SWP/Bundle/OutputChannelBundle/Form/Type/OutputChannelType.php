<?php

namespace SWP\Bundle\OutputChannelBundle\Form\Type;

use SWP\Bundle\CoreBundle\Model\OutputChannel;
use SWP\Component\OutputChannel\Model\OutputChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class OutputChannelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;

        $formModifier = function (FormInterface $form, ?string $type) {
            if (OutputChannelInterface::TYPE_WORDPRESS === $type) {
                $form->add('config', WordpressOutputChannelConfigType::class);
            }
        };

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                if (null !== $event->getData()) {
                    $formModifier($event->getForm(), $data->getType());
                }
            }
        );

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $type = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $type);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => OutputChannel::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'swp_output_channel';
    }
}
