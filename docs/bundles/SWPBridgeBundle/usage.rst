Usage
=====

Using Validator Chain Service
-----------------------------

The Validator Chain service is used to register all validators with a tag ``validator.http_push_validator``.

Usage:

.. code-block:: php

    // ...
    use Symfony\Component\HttpFoundation\Response;

    public function indexAction()
    {
        $value = 'some value';
        $result = $this->get('swp_bridge.http_push.validator_chain')->isValid($value);

        return new Response($result);
    }

How to Create and Register Custom Validators
--------------------------------------------

Validators are used to process incoming request content and validate it against the specific schema.
Read more about it in the Bridge component documentation (in the :doc:`/components/Bridge/usage` section).

Creating the Validator Class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A new Validator has to implement the ``SWP\Component\Bridge\Validator\ValidatorInterface`` and
``SWP\Component\Bridge\Validator\ValidatorOptionsInterface`` interfaces.

``CustomValidator`` class example:

.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Validator;

    use SWP\Component\Bridge\Validator\ValidatorInterface;
    use SWP\Component\Bridge\Validator\ValidatorOptionsInterface

    final class CustomValidator implement ValidatorInterface, ValidatorOptionsInterface
    {
        /**
         * @var string
         */
        private $schema = 'custom schema';

        /**
         * {@inheritdoc}
         */
        public function isValid($data)
        {
            // custom validation here
        }

        /**
         * {@inheritdoc}
         */
        public function getSchema()
        {
            return $this->schema;
        }

        /**
         * {@inheritdoc}
         */
        public function getFormat()
        {
            return 'custom';
        }
    }


Configuring the Validator
~~~~~~~~~~~~~~~~~~~~~~~~~

To register your new validator, simply add a definition to your services file and tag it with a special name: ``validator.http_push_validator``:

.. code-block:: yaml

    # Resources/config/services.yml
    acme_my_custom_validator:
        class: 'Acme\DemoBundle\Validator\CustomValidator'
        tags:
            - { name: validator.http_push_validator, alias: http_push.custom }

.. note::

    You can use the ``SWP\Component\Bridge\Validator\JsonValidator`` abstract class if you wish to create custom JSON validator.

.. _bridge_bundle_transformers:

Using Transformer Chain Service
-------------------------------

Transformer Chain service is used to register all transformers with a tag ``transformer.http_push_transformer``.

Usage:

.. code-block:: php

    // ...

    public function indexAction()
    {
        $value = 'some value';
        $result = $this->get('swp_bridge.http_push.transformer_chain')->transform($value);
        $result = $this->get('swp_bridge.http_push.transformer_chain')->reverseTransform($value);
    }

How to Create and Register Custom Data Transformers
---------------------------------------------------

Data transformers are used to transform one value/object into another.
Read more about it in the Bridge component documentation (in the :doc:`/components/Bridge/usage` section).

Creating the Data Transformer Class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a new Data Transformer, your new class should implement the ``SWP\Component\Bridge\Transformer\DataTransformerInterface`` interface.

``CustomValidator`` class example:

.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Transformer;

    use Acme\DemoBundle\Model\Custom;
    use SWP\Component\Bridge\Exception\MethodNotSupportedException;
    use SWP\Component\Bridge\Exception\TransformationFailedException;
    use SWP\Component\Bridge\Validator\ValidatorInterface;
    use SWP\Component\Common\Serializer\SerializerInterface;

    final class JsonToObjectTransformer implements DataTransformerInterface
    {
        /**
         * @var SerializerInterface
         */
        private $serializer;

        /**
         * @var ValidatorInterface
         */
        private $validatorChain;

        /**
         * JsonToPackageTransformer constructor.
         *
         * @param SerializerInterface $serializer
         * @param ValidatorInterface  $validatorChain
         */
        public function __construct(SerializerInterface $serializer, ValidatorInterface $validatorChain)
        {
            $this->serializer = $serializer;
            $this->validatorChain = $validatorChain;
        }

        /**
         * {@inheritdoc}
         */
        public function transform($json)
        {
            if (!$this->validatorChain->isValid($json)) {
                throw new TransformationFailedException('None of the chained validators were able to validate the data!');
            }

            return $this->serializer->deserialize($json, Custom::class, 'json');
        }

        /**
         * {@inheritdoc}
         */
        public function reverseTransform($value)
        {
            throw new MethodNotSupportedException('reverseTransform');
        }
    }

Configuring the Data Transformer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To register your new Data Transformer, simply add a definition to your services file and tag it with a special name: ``transformer.http_push_transformer``:

.. code-block:: yaml

    # Resources/config/services.yml
    acme_my_custom_transformer:
        class: 'Acme\DemoBundle\Transformer\CustomTransformer'
        arguments:
            - '@swp.serializer'
            - '@swp_bridge.http_push.validator_chain'
        tags:
            - { name: transformer.http_push_transformer, alias: transformer.json_to_object }


Enabling a separate Monolog channel for Validators
--------------------------------------------------

It is possible to enable a separate Monolog channel to which all validators related logs will be forwarded. An example log entry might be logged when the incoming payload can not be validated properly.. You could have then a separate log file for which will be usually saved under the directory ``app/logs/`` in your application and will be named, for example: ``swp_validators_<env>.log``. By default, a separate channel is disabled. You can enable it by adding an extra channel in your Monolog settings (in one of your configuration files):

.. code-block:: yaml

    # app/config/config.yml
    monolog:
        handlers:
            swp_validators:
                level:    debug
                type:     stream
                path:     '%kernel.logs_dir%/swp_validators_%kernel.environment%.log'
                channels: swp_validators

For more details see the `Monolog documentation`_.

.. _Monolog documentation: http://symfony.com/doc/current/logging/channels_handlers.html