<?php

namespace SWP\Component\MultiTenancy\Exception;

/**
 * Class SegmentNotFoundException.
 */
class SegmentNotFoundException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $alternatives;

    /**
     * SegmentNotFoundException constructor.
     *
     * @param string          $name
     * @param \Exception|null $previous
     * @param array           $alternatives
     */
    public function __construct($name, \Exception $previous = null, array $alternatives = [])
    {
        $this->name = $name;
        $this->alternatives = $alternatives;

        parent::__construct('', 0, $previous);
        $this->message = sprintf('You have requested a non-existent path segment "%s".', $this->name);

        if ($this->alternatives) {
            if (1 == count($this->alternatives)) {
                $this->message .= ' Did you mean this: "';
            } else {
                $this->message .= ' Did you mean one of these: "';
            }

            $this->message .= implode('", "', $this->alternatives).'"?';
        }
    }
}
