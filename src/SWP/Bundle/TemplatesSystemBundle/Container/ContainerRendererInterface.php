<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Container;

/**
 * Interface ContainerRendererInterface.
 */
interface ContainerRendererInterface
{
    const OPEN_TAG_TEMPLATE = '<div id="swp_container_{{ id }}" class="swp_container {{ class }}"{% if styles %} style="{{styles}}"{% endif %}{% for value in data %} data-{{value.getKey()}}="{{value.getValue()}}"{% endfor %}>';
    const CLOSE_TAG_TEMPLATE = '</div>';

    /**
     * Set Widgets.
     *
     * @param array $widgets
     *
     * @return self
     */
    public function setWidgets($widgets);

    /**
     * Render open tag for container.
     *
     * @return string
     */
    public function renderOpenTag();

    /**
     * Check if container has items.
     *
     * @return bool
     */
    public function hasWidgets();

    /**
     * Go through widgets render them and collect output of rendering.
     *
     * @return string
     */
    public function renderWidgets();

    /**
     * Check if container is visible.
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Render close tag for container.
     *
     * @return string
     */
    public function renderCloseTag();
}
