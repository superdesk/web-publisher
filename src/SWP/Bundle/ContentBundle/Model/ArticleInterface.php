<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Model;

use SWP\Component\Common\Model\SoftDeletableInterface;
use SWP\Component\Common\Model\TimestampableInterface;
use SWP\Component\Common\Model\TranslatableInterface;
use SWP\Component\Storage\Model\PersistableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;

interface ArticleInterface extends TimestampableInterface, TranslatableInterface, PersistableInterface, SoftDeletableInterface, PublishableInterface, PublishTimePeriodInterface, MetadataAwareInterface
{
    const STATUS_NEW = 'new';
    const STATUS_PUBLISHED = 'published';
    const STATUS_UNPUBLISHED = 'unpublished';
    const STATUS_CANCELED = 'canceled';

    const KEY_FEATURE_MEDIA = 'featuremedia';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * @return \DateTime
     */
    public function getPublishedAt();

    /**
     * @param string $publishedAt
     *
     * @return \DateTime
     */
    public function setPublishedAt(\DateTime $publishedAt);

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * @param string $status
     *
     * @return string
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @param string $templateName
     */
    public function setTemplateName($templateName);

    /**
     * @param RouteInterface|void $route
     */
    public function setRoute(RouteInterface $route = null);

    /**
     * @return RouteInterface
     */
    public function getRoute();

    /**
     * @return string
     */
    public function getLead();

    /**
     * @param string $lead
     */
    public function setLead($lead);

    /**
     * @return array
     */
    public function getKeywords(): array;

    /**
     * @param array $keywords
     */
    public function setKeywords(array $keywords);

    /**
     * @return null|ArticleMediaInterface
     */
    public function getFeatureMedia();

    /**
     * @param ArticleMediaInterface $featureMedia
     */
    public function setFeatureMedia(ArticleMediaInterface $featureMedia = null);

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * @param string $code
     */
    public function setCode(string $code);

    /**
     * @return mixed
     */
    public function getSource();

    /**
     * @param mixed $source
     */
    public function setSource($source);
}
