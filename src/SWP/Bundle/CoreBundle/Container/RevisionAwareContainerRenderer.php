<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Container;

use SWP\Bundle\TemplatesSystemBundle\Container\ContainerRenderer;
use SWP\Component\Revision\RevisionAwareInterface;

class RevisionAwareContainerRenderer extends ContainerRenderer
{
    /**
     * Render open tag for container.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function renderOpenTag()
    {
        $id = $this->containerEntity->getId();
        if ($this->containerEntity instanceof RevisionAwareInterface) {
            $id = $this->containerEntity->getUuid();
        }

        return $this->renderer->render('open_tag', [
            'id' => $id,
            'class' => $this->containerEntity->getCssClass(),
            'styles' => $this->containerEntity->getStyles(),
            'visible' => $this->containerEntity->getVisible(),
            'data' => $this->containerEntity->getData(),
        ]);
    }

    protected function getContainerId(): string
    {
        if ($this->containerEntity instanceof RevisionAwareInterface) {
            return (string) $this->containerEntity->getUuid();
        }

        return parent::getContainerId();
    }
}
