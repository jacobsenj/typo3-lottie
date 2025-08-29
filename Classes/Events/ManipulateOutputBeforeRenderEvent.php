<?php

declare(strict_types=1);

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2024
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
        protected readonly LottieRenderer $lottieRenderer,
        protected readonly FileInterface $file,
        protected readonly int|string $width,
        protected readonly int|string $height,
        protected readonly array $options,
        protected readonly bool $usedPathsRelativeToCurrentScript,
        protected TagBuilder $containerTag,
        protected TagBuilder $lottieTag
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
