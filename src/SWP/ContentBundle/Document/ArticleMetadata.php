<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\ContentBundle\Document;

class ArticleMetadata extends BaseDocument implements
    TranslatableDocumentInterface,
    VersionableDocumentInterface
{
    use TranslatableDocumentTrait, VersionableDocumentTrait;

    /**
     * Article to which the metadata belong
     *
     * @var Article
     */
    protected $article;

    /**
     * Key name of the metadata entry
     *
     * @var string
     */
    protected $key;

    /**
     * Value of the metadata entry
     *
     * @var mixed
     */
    protected $value;
}
