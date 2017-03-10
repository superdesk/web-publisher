<?php

/*
 * This file is part of the Superdesk Web Publisher Template Engine Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\TemplatesSystemBundle\Widget;

use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use SWP\Component\TemplatesSystem\Gimme\Widget\AbstractWidgetHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

abstract class TemplatingWidgetHandler extends AbstractWidgetHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TemplatingWidgetHandler constructor.
     *
     * @param WidgetModelInterface $widgetModel
     * @param ContainerInterface   $container
     */
    public function __construct(WidgetModelInterface $widgetModel, ContainerInterface $container)
    {
        parent::__construct($widgetModel);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return EngineInterface
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * Render given template with given parameters.
     *
     * @param string $templateName
     * @param array  $parameters
     *
     * @return string
     */
    protected function renderTemplate($templateName, $parameters = null)
    {
        if (null === $parameters) {
            $parameters = $this->getAllParametersWithValue();
        }

        return $this->getTemplating()->render(sprintf('widgets/%s', $templateName), $parameters);
    }
}
