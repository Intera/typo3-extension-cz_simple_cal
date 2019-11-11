<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'cz_simple_cal',
    'Pi1',
    'Simple calendar using Extbase'
);

ExtensionUtility::registerPlugin(
    'cz_simple_cal',
    'Pi2',
    'Calendar event submission for users'
);

$extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class);
$flexFormType = $extensionConfig->get('cz_simple_cal', 'flexFormType') ?: 'advanced';

// Init flexform for plugin
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['czsimplecal_pi1'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['czsimplecal_pi1'] = 'layout,select_key';
ExtensionManagementUtility::addPiFlexFormValue(
    'czsimplecal_pi1',
    sprintf('FILE:EXT:cz_simple_cal/Configuration/FlexForms/flexform_%s.xml', $flexFormType)
);
