<?php

namespace SWP\Component\Bridge\Validator;

use JsonSchema\Validator;

class NinjsValidator extends JsonValidator
{
    const SCHEMA_PATH = '/schema/ninjs-schema_1.1.json';

    /**
     * {@inheritdoc}
     */
    public function __construct(Validator $validator)
    {
        parent::__construct($validator);

        $this->setSchema(json_decode(file_get_contents(__DIR__.self::SCHEMA_PATH)));
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'ninjs';
    }
}
