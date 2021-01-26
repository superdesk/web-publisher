<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Form\Type;

use SWP\Bundle\ContentBundle\Doctrine\ArticleAuthorRepositoryInterface;
use SWP\Bundle\CoreBundle\Form\DataTransformer\AuthorToIdDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorSelectorType extends AbstractType
{
    private $articleAuthorRepository;

    public function __construct(ArticleAuthorRepositoryInterface $articleAuthorRepository)
    {
        $this->articleAuthorRepository = $articleAuthorRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new AuthorToIdDataTransformer($this->articleAuthorRepository);

        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected author does not exist!',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
