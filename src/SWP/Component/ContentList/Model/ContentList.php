<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Model;

use SWP\Component\Common\ArrayHelper;
use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class ContentList implements ContentListInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;
    use EnableableTrait;
    use ContentListItemsUpdateAtTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $cacheLifeTime;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * ContentList constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifeTime()
    {
        return $this->cacheLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheLifeTime(int $cacheLifeTime = 0)
    {
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit(int $limit = 0)
    {
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilters(array $filters)
    {
        if (isset($filters['metadata'])) {
            $filters['metadata'] = ArrayHelper::sortNestedArrayAssocAlphabeticallyByKey($filters['metadata']);
        }
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter(string $key)
    {
        $filters = $this->getFilters();

        if (isset($filters[$key])) {
            return $filters[$key];
        }
    }
}
