<?php

declare(strict_types=1);

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
