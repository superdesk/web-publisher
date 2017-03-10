<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\TemplatesSystem\Gimme\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Container Interface.
 */
interface ContainerInterface
{
    /**
     * Get container Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Getcontainer css classes.
     *
     * @return string
     */
    public function getCssClass();

    /**
     * Sets the value of cssClass.
     *
     * @param string $cssClass
     *
     * @return self
     */
    public function setCssClass(string $cssClass);

    /**
     * Get container styles.
     *
     * @return string
     */
    public function getStyles();

    /**
     * Sets the value of styles.
     *
     * @param string $styles
     *
     * @return self
     */
    public function setStyles(string $styles);

    /**
     * Get container visibility.
     *
     * @return bool
     */
    public function getVisible();

    /**
     * Sets the value of visible.
     *
     * @param bool $visible
     *
     * @return self
     */
    public function setVisible(bool $visible);

    /**
     * Get container data.
     *
     * @return ArrayCollection
     */
    public function getData();

    /**
     * Sets the value of data.
     *
     * @param ArrayCollection $data
     *
     * @return self
     */
    public function setData(ArrayCollection $data);

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Add ContainerData to container.
     *
     * @param ContainerDataInterface $containerData
     *
     * @return self
     */
    public function addData(ContainerDataInterface $containerData);

    /**
     * Gets the value of type.
     *
     * @return int
     */
    public function getType();

    /**
     * Sets the value of type.
     *
     * @param int $type the type
     *
     * @return self
     */
    public function setType($type);

    /**
     * Gets the value of widgets.
     *
     * @return ArrayCollection
     */
    public function getWidgets();

    /**
     * Sets the value of widgets.
     *
     * @param ArrayCollection $widgets the widgets
     *
     * @return self
     */
    public function setWidgets(ArrayCollection $widgets);

    /**
     * Add widget to container.
     *
     * @param WidgetModelInterface $widget
     *
     * @return self
     */
    public function addWidget($widget);

    /**
     * Remove widget to container.
     *
     * @param WidgetModelInterface $widget
     *
     * @return self
     */
    public function removeWidget($widget);
}
