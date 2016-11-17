<?php

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

/**
 * ContainerData Interface.
 */
interface ContainerDataInterface
{
    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId();

    /**
     * Gets the value of key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Gets the value of value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Gets the value of container.
     *
     * @return Container
     */
    public function getContainer();
}
