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
interface WidgetModelInterface
{
    /**
     * Get widget Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get widget name.
     *
     * @return int
     */
    public function getName();

    /**
     * Get widget type.
     *
     * @return int
     */
    public function getType();

    /**
     * Get widget visibility.
     *
     * @return bool
     */
    public function getVisible();

    /**
     * Get widget data.
     *
     * @return array
     */
    public function getParameters();
}
