<?php

/**
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\TemplateEngineBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerBranch;
use SWP\Bundle\TemplateEngineBundle\Model\ContainerWidget;
use SWP\Bundle\TemplateEngineBundle\Model\WidgetModelBranch;
use SWP\Component\TemplatesSystem\Tests\Gimme\Model\WidgetModel;
use Doctrine\Common\Collections\ArrayCollection;

class BranchService
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getBranchedContainer($containerId)
    {
        $branch = $this->getContainerBranchBySourceId($containerId);

        return null === $branch ? null : $branch->getTarget();
    }

    public function getSourcedContainer($branchedContainerId)
    {
        $branch = $this->getContainerBranchByTargetId($branchedContainerId);

        return null === $branch ? null : $branch->getSource();
    }

    public function createBranchedContainer($container, $targetName = null)
    {
        /** @var Container $target */
        $target = clone $container;
        if (null === $targetName) {
            $targetName = $container->getName().'_'.uniqid();
        }
        $target->setName($targetName);
        $this->objectManager->persist($target);

        $containerBranch = new ContainerBranch();
        $containerBranch->setSource($container);
        $containerBranch->setTarget($target);
        $this->objectManager->persist($containerBranch);
        $this->objectManager->flush();

        return $target;
    }

    public function publishBranchedContainer($containerId)
    {
        /** @var ContainerBranch $branch */
        $branch = $this->getContainerBranchByTargetId($containerId);
        if (null === $branch) {
            throw new EntityNotFoundException('No container branch found');
        }

        $source = $branch->getSource();
        $target = $branch->getTarget();

        $source->setType($target->getType());
        $source->setWidth($target->getWidth());
        $source->setHeight($target->getHeight());
        $source->setStyles($target->getStyles());
        $source->setCssClass($target->getCssClass());
        $source->setVisible($target->getVisible());

        // Remove data from source
        foreach ($source->getData() as $datum) {
            $datum->setContainer(null);
            $this->objectManager->remove($datum);
        }

        // Remove widgets from source
        foreach ($source->getWidgets() as $widget) {
            $widget->setContainer(null);
            $this->objectManager->remove($widget);
        }

        // Flush required here
        $this->objectManager->flush();

        // Add data
        $data = new ArrayCollection($target->getData()->toArray());
        $source->setData($data);

        // More complicated because of sortable field in ContainerWidget - new widgets must be added in order of their position,
        // Reassigning instances of ContainerWidget from target to source oes not work, as SortableListener will use id's of the entities to set their position
        $widgets = $target->getWidgets()->toArray();
        usort($widgets, function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });
        //$source->setWidgets(new ArrayCollection($widgets));
        foreach ($widgets as $widget) {
            $source->addWidget(new ContainerWidget($source, $widget->getWidget()));
            $this->objectManager->remove($widget);
        }

        // Remove branch
        $this->objectManager->remove($branch);
        $this->objectManager->remove($target);
        $this->objectManager->flush();

        return $source;
    }

    public function createBranchedWidgetModel($widgetModel, $targetName = null)
    {
        /* @var WidgetModel $widgetModel */
        $target = clone $widgetModel;
        if (null === $targetName) {
            $targetName = $widgetModel->getName().'_'.uniqid();
        }
        $target->setName($targetName);
        $this->objectManager->persist($target);
        $this->objectManager->flush();

        $widgetModelBranch = new WidgetModelBranch();
        $widgetModelBranch->setSource($widgetModel);
        $widgetModelBranch->setTarget($target);
        $this->objectManager->persist($widgetModelBranch);
        $this->objectManager->flush();

        return $target;
    }

    public function publishBranchedWidgetModel($widgetId)
    {
        /** @var WidgetModelBranch $branch */
        $branch = $this->getWidgetBranchByTargetId($widgetId);
        if (null === $branch) {
            throw new EntityNotFoundException('No widget branch found');
        }

        $source = $branch->getSource();
        $target = $branch->getTarget();

        $source->setType($target->getType());
        $source->setVisible($target->getVisible());
        $source->setParameters($target->getParameters());

        // Remove branch and target
        $this->objectManager->remove($branch);
        $this->objectManager->remove($target);
        $this->objectManager->flush();

        return $source;
    }

    public function getBranchedWidget($widgetId)
    {
        $branch = $this->getWidgetBranchBySourceId($widgetId);

        return null === $branch ? null : $branch->getTarget();
    }

    public function getSourcedWidget($branchedWidgetId)
    {
        $branch = $this->getWidgetBranchByTargetId($branchedWidgetId);

        return null === $branch ? null : $branch->getSource();
    }

    private function getContainerBranchByTargetId($targetId)
    {
        return $this->getContainerBranchRepository()
            ->findOneBy(['target' => $targetId]);
    }

    private function getContainerBranchBySourceId($sourceId)
    {
        return $this->getContainerBranchRepository()
            ->findOneBy(['source' => $sourceId]);
    }

    private function getContainerBranchRepository()
    {
        return $this->objectManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\ContainerBranch');
    }

    private function getWidgetBranchByTargetId($targetId)
    {
        return $this->getWidgetBranchRepository()
            ->findOneBy(['target' => $targetId]);
    }

    private function getWidgetBranchBySourceId($sourceId)
    {
        return $this->getWidgetBranchRepository()
            ->findOneBy(['source' => $sourceId]);
    }

    private function getWidgetBranchRepository()
    {
        return $this->objectManager->getRepository('SWP\Bundle\TemplateEngineBundle\Model\WidgetModelBranch');
    }
}
