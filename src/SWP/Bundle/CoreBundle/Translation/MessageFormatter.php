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
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageFormatter implements MessageFormatterInterface, ChoiceMessageFormatterInterface
{
    private $translator;

    public function __construct($translator = null)
    {
        if ($translator instanceof MessageSelector) {
            $translator = new IdentityTranslator($translator);
        } elseif (null !== $translator && !$translator instanceof TranslatorInterface && !$translator instanceof LegacyTranslatorInterface) {
            throw new \TypeError(sprintf('Argument 1 passed to %s() must be an instance of %s, %s given.', __METHOD__, TranslatorInterface::class, \is_object($translator) ? \get_class($translator) : \gettype($translator)));
        }

        $this->translator = $translator ?? new IdentityTranslator();
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

        if ($this->translator instanceof TranslatorInterface) {
            return $this->format($message, $locale, $parameters);
        }

        return $this->format($this->translator->transChoice($message, $number, [], null, $locale), $locale, $parameters);
    }
}
