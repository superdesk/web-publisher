<?php

/**
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\TemplatesSystem\Gimme\Model;

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
     * Get container height.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Get container width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get container styles.
     *
     * @return string
     */
    public function getStyles();

    /**
     * Get container visibility.
     *
     * @return bool
     */
    public function getVisible();

    /**
     * Get container data.
     *
     * @return array
     */
    public function getData();
}
