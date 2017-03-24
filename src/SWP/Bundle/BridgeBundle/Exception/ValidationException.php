<?php

namespace SWP\Bundle\BridgeBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationException extends \RuntimeException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $constraintViolationList;

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface $constraintViolationList
     * @param string                           $message
     * @param int                              $code
     * @param \Exception|null                  $previous
     */
    public function __construct(ConstraintViolationListInterface $constraintViolationList, $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->constraintViolationList = $constraintViolationList;
    }

    /**
     * Gets constraint violations as a list.
     *
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList()
    {
        return $this->constraintViolationList;
    }
}
