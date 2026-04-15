<?php

declare(strict_types=1);

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2025
 */

namespace Kandoh\Lottie\Events;

use Kandoh\Lottie\Resource\Rendering\LottieRenderer;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

final class ManipulateOutputBeforeRenderEvent
{
    /**
     * @param array<mixed> $options
     */
    public function __construct(
        private readonly LottieRenderer $lottieRenderer,
        private readonly FileInterface $file,
        private readonly int|string $width,
        private readonly int|string $height,
        private readonly array $options,
        private TagBuilder $containerTag,
        private TagBuilder $lottieTag
    ) {}

    public function getLottieRenderer(): LottieRenderer
    {
        return $this->lottieRenderer;
    }

    public function getFile(): FileInterface
    {
        return $this->file;
    }

    public function getWidth(): int|string
    {
        return $this->width;
    }

    public function getHeight(): int|string
    {
        return $this->height;
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function isUsedPathsRelativeToCurrentScript(): bool
    {
        return $this->usedPathsRelativeToCurrentScript;
    }

    public function getContainerTag(): TagBuilder
    {
        return $this->containerTag;
    }

    public function getLottieTag(): TagBuilder
    {
        return $this->lottieTag;
    }

    public function setContainerTag(TagBuilder $containerTag): self
    {
        $this->containerTag = $containerTag;
        return $this;
    }

    public function setLottieTag(TagBuilder $lottieTag): self
    {
        $this->lottieTag = $lottieTag;
        return $this;
    }
}
