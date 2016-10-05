<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\CoreBundle\Theme\Model\ThemeInterface;
use SWP\Bundle\CoreBundle\Theme\Provider\ThemeProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ThemeNameChoiceType extends AbstractType
{
    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(ThemeProviderInterface $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setNormalizer('choices', function () {
                /** @var ThemeInterface[] $themes */
                $themes = $this->themeProvider->getCurrentTenantAvailableThemes();
                $choices = [];

                foreach ($themes as $theme) {
                    $choices[(string) $theme] = $theme->getName();
                }

                return $choices;
            })
            ->setDefaults([
                'invalid_message' => 'The selected theme does not exist',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
