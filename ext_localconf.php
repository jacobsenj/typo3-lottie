<?php

declare(strict_types=1);

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2025
 */

use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Kandoh\Lottie\Resource\Rendering\LottieRenderer;

defined('TYPO3') || die();

(function () {
    /** @var RendererRegistry $rendererRegistry */
    $rendererRegistry = GeneralUtility::makeInstance(RendererRegistry::class);
    $rendererRegistry->registerRendererClass(LottieRenderer::class);
    unset($rendererRegistry);

    // Allow JSON files to be added to `tt_content.assets`,
    // which is only present in `textmedia` CType by default
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'] .= ',json';
})();
