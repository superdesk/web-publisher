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

class ComponentTextStyle
{
    private $backgroundColor;

    private $fontName;

    private $fontColor;

    private $fontSize;

    private $lineHeight;

    private $linkStyle;

    /** @var int|null */
    private $paragraphSpacingBefore;

    /** @var int|null */
    private $paragraphSpacingAfter;

    /** @var string|null */
    private $textColor;

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function getFontName(): ?string
    {
        return $this->fontName;
    }

    public function getFontColor(): ?string
    {
        return $this->fontColor;
    }

    public function getFontSize(): ?int
    {
        return $this->fontSize;
    }

    public function getLineHeight(): ?int
    {
        return $this->lineHeight;
    }

    public function getLinkStyle(): ?TextStyle
    {
        return $this->linkStyle;
    }

    public function setBackgroundColor($backgroundColor): void
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function setFontName($fontName): void
    {
        $this->fontName = $fontName;
    }

    public function setFontColor($fontColor): void
    {
        $this->fontColor = $fontColor;
    }

    public function setFontSize($fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    public function setLineHeight($lineHeight): void
    {
        $this->lineHeight = $lineHeight;
    }

    public function setLinkStyle($linkStyle): void
    {
        $this->linkStyle = $linkStyle;
    }

    public function getParagraphSpacingBefore(): ?int
    {
        return $this->paragraphSpacingBefore;
    }

    public function setParagraphSpacingBefore(?int $paragraphSpacingBefore): void
    {
        $this->paragraphSpacingBefore = $paragraphSpacingBefore;
    }

    public function getParagraphSpacingAfter(): ?int
    {
        return $this->paragraphSpacingAfter;
    }

    public function setParagraphSpacingAfter(?int $paragraphSpacingAfter): void
    {
        $this->paragraphSpacingAfter = $paragraphSpacingAfter;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): void
    {
        $this->textColor = $textColor;
    }
}
