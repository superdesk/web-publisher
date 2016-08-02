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
namespace SWP\Bundle\TemplateEngineBundle\Model;

use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;

/**
 * Class WidgetModelBranch.
 */
class WidgetModelBranch implements TimestampableInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var WidgetModelInterface
     */
    protected $source;

    /**
     * @var WidgetModelInterface
     */
    protected $target;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $condition;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ContainerBranch
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return ContainerBranch
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set condition.
     *
     * @param string $condition
     *
     * @return WidgetModelBranch
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition.
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set source.
     *
     * @param \SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $source
     *
     * @return WidgetModelBranch
     */
    public function setSource(\SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return \SWP\Bundle\TemplateEngineBundle\Model\WidgetModel
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set target.
     *
     * @param \SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $target
     *
     * @return WidgetModelBranch
     */
    public function setTarget(\SWP\Bundle\TemplateEngineBundle\Model\WidgetModel $target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target.
     *
     * @return \SWP\Bundle\TemplateEngineBundle\Model\WidgetModel
     */
    public function getTarget()
    {
        return $this->target;
    }
}
