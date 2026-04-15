<?php

declare(strict_types=1);

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2025
 */

namespace Kandoh\Lottie\Resource\Rendering;

use Kandoh\Lottie\Events\ManipulateOutputBeforeRenderEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class LottieRenderer implements FileRendererInterface
{
    private LoggerInterface $logger;
    private EventDispatcherInterface $eventDispatcher;

    /** @var string[] List of options that will be passed to the HTML output */
    protected static array $keepOptionsAsAttributes = [
        'class',
        'dir',
        'id',
        'lang',
        'style',
        'title',
        'accesskey',
        'tabindex',
        'onclick',
        'alt',
    ];

    public function injectLogger(LogManager $loggerManager): void
    {
        $this->logger = $loggerManager->getLogger(static::class);
    }

    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns the priority of the renderer.
     * This way it is possible to define/overrule a renderer
     * for a specific file type/context.
     *
     * For example create a video renderer for a certain storage/driver type.
     *
     * Should be between 1 and 100, 100 is more important than 1.
     */
    #[\Override]
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * Check if given File(Reference) can be rendered.
     *
     * @param FileInterface $file File or FileReference to render
     */
    #[\Override]
    public function canRender(FileInterface $file): bool
    {
        $file = $file instanceof FileReference
            ? $file->getOriginalFile()
            : $file
        ;
        return
            $file->getExtension() === 'json'
            && $file->getProperty('tx_lottie_is_lottie_animation')
        ;
    }

    /**
     * Render for given File(Reference) HTML output.
     *
     * @param int|string $width TYPO3 known format; examples: 220, 200m or 200c
     * @param int|string $height TYPO3 known format; examples: 220, 200m or 200c
     * @param array<string, string|\Traversable<mixed>|array<mixed>|null> $options
     */
    #[\Override]
    public function render(
        FileInterface $file,
        $width,
        $height,
        array $options = []
    ): string {
        $containerTag = new TagBuilder('div');
        $containerTag->forceClosingTag(true);
        $lottieTag = new TagBuilder('div');
        $lottieTag->forceClosingTag(true);

        // It may be useful to know if $file was a File or FileReference.
        $instanceType = '';
        if ($file::class == File::class) {
            $instanceType = 'File';
        } elseif ($file::class == FileReference::class) {
            $instanceType = 'FileReference';
        }

        $width = (int)$width;
        $height = (int)$height;

        // If no valid dimensions were provided
        // let's try to read the dimensions from the Lottie animation itself.
        if ($width === 0 || $height === 0) {
            $localProcessingFile = file_get_contents($file->getForLocalProcessing(false));
            if ($localProcessingFile === false) {
                $message = 'An error occurred while trying to read the Lottie animation file.';
                $this->logger->critical($message, [
                    'file' => $file->getIdentifier(),
                ]);
                return $message;
            }
            $fileData = json_decode($localProcessingFile, true);
            if (! is_array($fileData)) {
                $message = 'An error occurred while trying to parse the Lottie animation file.';
                $this->logger->critical($message, [
                    'file' => $file->getIdentifier(),
                    'json_last_error' => json_last_error_msg(),
                ]);
                return $message;
            }

            $width = (int)($fileData['width'] ?? $width);
            $height = (int)($fileData['hei$height'] ?? $height);

            unset($fileData);
            unset($localProcessingFile);
        }

        foreach ($options as $attributeName => $attributeValue) {
            if (in_array($attributeName, static::$keepOptionsAsAttributes) && ! empty($attributeValue)) {
                $lottieTag->addAttribute($attributeName, $attributeValue);
            }
        }

        // Make sure that the required "lottie" class will be added
        $class = $lottieTag->getAttribute('class') ?? '';
        $lottieTag->addAttribute('class', trim($class . ' lottie'));

        $containerClass = 'lottie-container';
        if (isset($options['containerClass']) && ! empty($options['containerClass'])) {
            $containerClass = trim($options['containerClass'] . ' ' . $containerClass);
        }
        $containerTag->addAttribute('class', $containerClass);

        // If the width and height could be properly determined, add some
        // inline styling to preserve the required space to prevent
        // visual content shifts.
        // This could be rewritten more nicely for TYPO3 CMS v10,
        // but to maintain backwards compatibility let's keep it like
        // this for now.
        if ($width > 0 && $height > 0) {
            $containerTag->addAttribute(
                'style',
                sprintf(
                    'position:relative;overflow:hidden;padding-top:%g%%',
                    ($height / $width) * 100
                )
            );
            $lottieTag->addAttribute(
                'style',
                'position:absolute;top:0;left:0;width:100%;height:100%'
            );
        }

        // If the public URL is not an absolute URL or not starting with a slash
        // let's put a slash in front of the URL.
        $publicUrl = $file->getPublicUrl();
        if ($publicUrl === null) {
            $message = 'Unable to determine the public URL of the Lottie animation file.';
            $this->logger->critical($message, [
                'file' => $file->getIdentifier(),
            ]);
            return $message;
        }
        $publicUrl = PathUtility::getAbsoluteWebPath($publicUrl);

        $identifier = $file instanceof File
            ? $file->getUid()
            : uniqid()
        ;

        $autoplay = 'false';
        if (isset($options['autoplay'])) {
            $autoplay = (bool)$options['autoplay'] ? 'true' : 'false';
        }

        $loop = 'true';
        if (isset($options['loop'])) {
            if (is_numeric($options['loop'])) {
                $loop = (int)$options['loop'];
            } elseif (is_string($options['loop'])) {
                $loop = strtolower($options['loop']) === 'false' ? 'false' : 'true';
            } else {
                $loop = (bool)$options['loop'] ? 'true' : 'false';
            }
        }

        $renderer = 'svg';
        $validRenderers = ['svg', 'canvas', 'html'];
        if (
            isset($options['renderer'])
            && in_array($options['renderer'], $validRenderers, true)
        ) {
            $renderer = $options['renderer'];
        }

        $dataAttributesFromOptions = (array)($options['data'] ?? []);
        $dataAttributes = array_merge(
            [
                'name' => 'lottie' . $instanceType . $identifier,
                'animation-path' => $publicUrl,
                'anim-autoplay' => $autoplay,
                'anim-loop' => $loop,
                'bm-renderer' => $renderer,
            ],
            $dataAttributesFromOptions
        );
        $lottieTag->addAttribute('data', $dataAttributes);

        $event = new ManipulateOutputBeforeRenderEvent(
            $this,
            $file,
            $width,
            $height,
            $options,
            $containerTag,
            $lottieTag
        );
        $this->eventDispatcher->dispatch($event);

        $containerTag = $event->getContainerTag();
        $lottieTag = $event->getLottieTag();

        $containerTag->setContent(
            $lottieTag->render()
        );
        return $containerTag->render();
    }
}
