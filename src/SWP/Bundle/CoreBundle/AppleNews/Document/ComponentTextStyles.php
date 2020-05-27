<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2020 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2020 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\AppleNews\Document;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ComponentTextStyles
{
    /** @var ComponentTextStyle */
    private $default;

    /**
     * @SerializedName("default-body")
     *
     * @var ComponentTextStyle
     */
    private $defaultBody;

    /**
     * @SerializedName("default-title")
     *
     * @var ComponentTextStyle
     */
    private $defaultTitle;

    /**
     * @SerializedName("default-intro")
     *
     * @var ComponentTextStyle
     */
    private $defaultIntro;

    /**
     * @SerializedName("default-byline")
     *
     * @var ComponentTextStyle
     */
    private $defaultByline;

    /**
     * @SerializedName("default-quote")
     *
     * @var ComponentTextStyle|null
     */
    private $defaultQuote;

    public function getDefault(): ComponentTextStyle
    {
        return $this->default;
    }

    public function setDefault(ComponentTextStyle $default): void
    {
        $this->default = $default;
    }

    public function getDefaultBody(): ComponentTextStyle
    {
        return $this->defaultBody;
    }

    public function setDefaultBody(ComponentTextStyle $defaultBody): void
    {
        $this->defaultBody = $defaultBody;
    }

    public function getDefaultTitle(): ?ComponentTextStyle
    {
        return $this->defaultTitle;
    }

    public function setDefaultTitle(ComponentTextStyle $defaultTitle): void
    {
        $this->defaultTitle = $defaultTitle;
    }

    public function getDefaultIntro(): ?ComponentTextStyle
    {
        return $this->defaultIntro;
    }

    public function setDefaultIntro(ComponentTextStyle $defaultIntro): void
    {
        $this->defaultIntro = $defaultIntro;
    }

    public function getDefaultByline(): ?ComponentTextStyle
    {
        return $this->defaultByline;
    }

    public function setDefaultByline(ComponentTextStyle $defaultByline): void
    {
        $this->defaultByline = $defaultByline;
    }

    public function getDefaultQuote(): ?ComponentTextStyle
    {
        return $this->defaultQuote;
    }

    public function setDefaultQuote(?ComponentTextStyle $defaultQuote): void
    {
        $this->defaultQuote = $defaultQuote;
    }
}
