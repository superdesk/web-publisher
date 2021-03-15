Usage
=====

What is Output Channel Adapter?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Output Channel Adapter is a service which helps to communicate with the external system, for example, Wordpress.
Thanks to the concept of adapters it is possible to exchange data between 3rd party services. It is possible to send
the data from Publisher to an external system and also get the data from that system.


The Output Channel Adapters are strictly connected to the concept of Output Channels.

It is possible to choose the type of the external system (e.g. WordPress) if a new tenant is created.

It means that the content which is sent to Superdesk Publisher will not only be stored in the Publisher's storage, but it can also be transmitted to an external system.

In this case, thanks to the output channels, you can send content wherever you want.

Superdesk Publisher acts as a hub where you can control where the content goes.

Adding New Adapter to work with Output Channels
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A new adapter must implement ``SWP\Bundle\CoreBundle\Adapter\AdapterInterface`` interface.

1. Add a new const (``CUSTOM_TYPE``) to the ``SWP\Component\OutputChannel\Model\OutputChannelInterface`` interface.

2. Create a custom class:

.. code-block:: php

    <?php

    // CustomAdapter.php
    declare(strict_types=1);

    namespace SWP\Bundle\CoreBundle\Adapter;

    use GuzzleHttp\ClientInterface;
    use SWP\Bundle\CoreBundle\Model\ArticleInterface;
    use SWP\Bundle\CoreBundle\Model\OutputChannelInterface;

    final class CustomAdapter implements AdapterInterface
    {
        /**
         * @var ClientInterface
         */
        private $client;

        /**
         * WordpressAdapter constructor.
         *
         * @param ClientInterface $client
         */
        public function __construct(ClientInterface $client)
        {
            $this->client = $client;
        }

        /**
         * {@inheritdoc}
         */
        public function send(OutputChannelInterface $outputChannel, ArticleInterface $article): void
        {
            $url = $outputChannel->getConfig()['url'];

            $this->client->post($url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => $article->getBody(),
                'timeout' => 5,
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function supports(OutputChannelInterface $outputChannel): bool
        {
            return OutputChannelInterface::TYPE_CUSTOM === $outputChannel->getType();
        }
    }

3. Create Custom Adapter configuration form type

This form type will define which fields we can specify for the adapter. It can be credentials to connect to the 3rd party service etc.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace SWP\Bundle\OutputChannelBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Url;

    final class CustomOutputChannelConfigType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('url', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Url(),
                    ],
                ])
                ->add('key', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('secret', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
            ;
        }
    }

4. Include a new type and form type in the ``OutputChannelType``

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace SWP\Bundle\OutputChannelBundle\Form\Type;

    use SWP\Bundle\CoreBundle\Model\OutputChannel;
    use SWP\Component\OutputChannel\Model\OutputChannelInterface;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\FormEvent;
    use Symfony\Component\Form\FormEvents;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    final class OutputChannelType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Wordpress' => OutputChannelInterface::TYPE_WORDPRESS,
                        'Custom' => OutputChannelInterface::TYPE_CUSTOM,
                    ],
                ])
            ;

            $formModifier = function (FormInterface $form, ?string $type) {
                if (OutputChannelInterface::TYPE_WORDPRESS === $type) {
                    $form->add('config', WordpressOutputChannelConfigType::class);
                }

                if (OutputChannelInterface::TYPE_CUSTOM === $type) {
                    $form->add('config', CustomOutputChannelConfigType::class);
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

        /**
         * {@inheritdoc}
         */
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'csrf_protection' => false,
                'data_class' => OutputChannel::class,
            ]);
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix(): string
        {
            return 'swp_output_channel';
        }
    }

5. Register your new adapter

Your new adapter must be registered so it can be detected by the system and used by the Publisher.
It can be done by taggin a service with ``swp.output_channel_adapter`` tag.

.. code-block:: yaml

    services:
        # ..
        SWP\Bundle\CoreBundle\Adapter\CustomAdapter:
            public: true
            arguments:
                - '@GuzzleHttp\Client'
            tags:
                - { name: swp.output_channel_adapter, alias: custom_adapter }


6. Create a new tenant with output channel which will use the newly created adapter

Now, when you want to create a new tenant, it will be possible to choose your output channel type and define the configuration
which will use the newly created ``CustomAdapter``.

.. code-block:: bash

    curl -X POST \
      http://example.com/api/v1/tenants/ \
      -H 'Authorization: key' \
      -H 'Cache-Control: no-cache' \
      -H 'Content-Type: application/json' \
      -d '      {
              "domainName": "example.com",
              "name": "Custom tenant",
              "subdomain": "custom",
              "outputChannel": {
                "type": "custom",
                "config": {
                  "url": "https://api.custom.com",
                  "key": "private key",
                  "secret": "secret"
                }
              }
          }'


Using Wordpress Adapter
~~~~~~~~~~~~~~~~~~~~~~~

Usage:

.. code-block:: php

    // example.php
    // ..

    use SWP\Bundle\CoreBundle\Adapter\WordpressAdapter;
    use SWP\Bundle\CoreBundle\Model\Article;
    use SWP\Component\OutputChannel\Model\OutputChannel;
    // ..

    $article = new Article();

    $guzzle = new GuzzleHttp\Client();
    // ...

    $wordpressAdapter = new WordpressAdapter($guzzle);
    $outputChannel = new OutputChannel();
    $outputChannel->setType('wordpress');
    // ...

    if ($adapter->supports($outputChannel)) {
        $adapter->send($outputChannel, $article);
        // ...
    }

Using Composite Output Channel Adapter
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Composite Output Channel Adapter service loops for each of the registered adapter, checks if adapter supports
given output channel and executes appropriate adapter functions.

Usage:

.. code-block:: php

   <?php
    // example.php
    // ..

    use SWP\Bundle\CoreBundle\Adapter\CompositeOutputChannelAdapter;
    use SWP\Bundle\CoreBundle\Adapter\WordpressAdapter;
    use SWP\Bundle\CoreBundle\Model\Article;
    use SWP\Component\OutputChannel\Model\OutputChannel;
    // ..

    $article = new Article();
    $guzzle = new GuzzleHttp\Client();
    // ...

    $wordpressAdapter = new WordpressAdapter($guzzle);
    // ...

    $compositeAdapter = new CompositeOutputChannelAdapter();
    $compositeAdapter->addAdapter($wordpressAdapter);
    // ...
    $outputChannel = new OutputChannel();
    $outputChannel->setType('wordpress');
    // ...

    $compisiteAdapter->send($outputChannel, $article);
    // ...
