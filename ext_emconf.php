<?php

declare(strict_types=1);

/*
 * This file is part of the "lottie" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the LICENSE file
 * that was distributed with this source code.
 * (c) 2019-2024
 */

/** @var string $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Lottie',
    'description' => 'Extension to enable rendering of Lottie/Bodymovin animations in frontend.',
    'category' => 'fe',
    'version' => '1.4.0',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'theLine, Moritz Ngo',
    'author_email' => 'typo3@theline.uber.space, moritz.ngo@kandoh.de',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-11.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Kandoh\\Lottie\\' => 'Classes',
        ],
    ],
];
