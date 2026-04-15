<?php

declare(strict_types=1);

use Kandoh\Lottie\Backend\DisplayConditions;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2025
 */

call_user_func(function () {
    $LLL_locallang_db = 'LLL:EXT:lottie/Resources/Private/Language/locallang_db.xlf:';

    $columns = [
        'tx_lottie_is_lottie_animation' => [
            'exclude' => true,
            'label' => $LLL_locallang_db . 'sys_file_metadata.tx_lottie_is_lottie_animation',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
            'displayCond' => 'USER:' . DisplayConditions::class . '->checkIfIsJsonFile',
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns(
        'sys_file_metadata',
        $columns
    );

    ExtensionManagementUtility::addToAllTCAtypes(
        'sys_file_metadata',
        implode(',', array_keys($columns)),
        implode(',', [
            FileType::TEXT->value,
            FileType::APPLICATION->value,
        ])
    );
});
