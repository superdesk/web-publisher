<?php

/**
 * This file is part of the Superdesk Web Publisher Storage Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Component\Storage\Bundle;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface BundleInterface
{
    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yaml';
    const MAPPING_ANNOTATION = 'annotation';

    /**
     * Returns a vector of supported drivers by the bundle.
     *
     * @return array
     */
    public function getSupportedDrivers();
}
