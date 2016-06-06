<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Component\Bridge\Validator;

use JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

class JsonValidator implements ValidatorInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $schema = '';

    /**
     * JsonValidator constructor.
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(Request $request)
    {
        $this->validator->check($request->getContent(), $this->getSchema());

        if ($this->validator->isValid()) {
            return true;
        }

        return false;
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
    public function setSchema($schema = '')
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'json';
    }
}
