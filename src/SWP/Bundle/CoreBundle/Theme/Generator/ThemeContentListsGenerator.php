<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2018 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2018 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Theme\Generator;

use SWP\Bundle\ContentListBundle\Form\Type\ContentListType;
use SWP\Component\ContentList\ContentListEvents;
use SWP\Component\ContentList\Repository\ContentListRepositoryInterface;
use SWP\Component\Storage\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;

class ThemeContentListsGenerator implements GeneratorInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FactoryInterface
     */
    protected $contentListFactory;

    /**
     * @var ContentListRepositoryInterface
     */
    protected $contentListRepository;

    /**
     * @var FakeArticlesGeneratorInterface
     */
    protected $fakeArticlesGenerator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ThemeContentListsGenerator constructor.
     *
     * @param FormFactoryInterface           $formFactory
     * @param FactoryInterface               $contentListFactory
     * @param ContentListRepositoryInterface $contentListRepository
     * @param FakeArticlesGeneratorInterface $fakeArticlesGenerator
     * @param EventDispatcherInterface       $eventDispatcher
     */
    public function __construct(FormFactoryInterface $formFactory, FactoryInterface $contentListFactory, ContentListRepositoryInterface $contentListRepository, FakeArticlesGeneratorInterface $fakeArticlesGenerator, EventDispatcherInterface $eventDispatcher)
    {
        $this->formFactory = $formFactory;
        $this->contentListFactory = $contentListFactory;
        $this->contentListRepository = $contentListRepository;
        $this->fakeArticlesGenerator = $fakeArticlesGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(array $contentLists, bool $applyOptionalData): void
    {
        foreach ($contentLists as $contentListData) {
            if (null !== $this->contentListRepository->findOneByName($contentListData['name'])) {
                continue;
            }

            $contentList = $this->createContentList($contentListData);
            $this->contentListRepository->add($contentList);

            if (null !== $contentListData['filters']) {
                $this->eventDispatcher->dispatch(
                    new GenericEvent($contentList, ['filters' => $contentListData['filters']]),
                    ContentListEvents::LIST_CRITERIA_CHANGE,
                );
                $this->contentListRepository->flush();
            }
        }
    }

    /**
     * @param array $contentListData
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function createContentList(array $contentListData)
    {
        $contentList = $this->contentListFactory->create();
        $form = $this->formFactory->create(ContentListType::class, $contentList);
        $form->submit($contentListData, false);
        if (!$form->isValid()) {
            throw new \Exception('Invalid content list definition');
        }

        return $contentList;
    }
}
