<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2017 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2017 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Translation;

use Symfony\Component\Translation\Formatter\ChoiceMessageFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\MessageSelector;

class MessageFormatter implements MessageFormatterInterface, ChoiceMessageFormatterInterface
{
    private $selector;

    /**
     * @param MessageSelector|null $selector The message selector for pluralization
     */
    public function __construct(MessageSelector $selector = null)
    {
        $this->selector = $selector ?: new MessageSelector();
    }

    /**
     * {@inheritdoc}
     */
    public function format($message, $locale, array $parameters = array())
    {
        return strtr($message, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function choiceFormat($message, $number, $locale, array $parameters = array())
    {
        $parameters = array_merge(array('%count%' => $number), $parameters);
        $position = \strpos($locale, '@');
        if (false !== $position) {
            $locale = \substr($locale, 0, $position);
        }

        return $this->format($this->selector->choose($message, (int) $number, $locale), $locale, $parameters);
    }
}
