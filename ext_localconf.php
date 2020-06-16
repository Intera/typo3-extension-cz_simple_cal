<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpMissingStrictTypesDeclarationInspection */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::listAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::countEventsAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::showAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Pi1',
    [
        'EventIndex' => 'list,countEvents,show',
        'Event' => 'show',
        'Category' => 'show',
    ],
    []
);

/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::listAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Slider',
    ['EventIndex' => 'slider'],
    []
);

/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::listAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::newAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::createAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::editAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::updateAction() */
/** @uses \Tx\CzSimpleCal\Controller\EventAdministrationController::deleteAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'Pi2',
    ['EventAdministration' => 'list,new,create,edit,update,delete'],
    ['EventAdministration' => 'list,new,create,edit,update,delete']
);

/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::rssUpcomingAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'RssUpcoming',
    ['EventIndex' => 'rssUpcoming'],
    []
);

/** @uses \Tx\CzSimpleCal\Controller\EventIndexController::rssLatestAction() */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Tx.CzSimpleCal',
    'RssLatest',
    ['EventIndex' => 'rssLatest'],
    []
);

// Add default pageTSConfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    file_get_contents(
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(
            'cz_simple_cal'
        ) . 'Configuration/TSconfig/Page/main.pagets'
    )
);

// Register the hook that filters inline addresses from the record list.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][]
    = Tx\CzSimpleCal\Hook\DatabaseRecordListHook::class;

// Hook into the post storing process to update the index of recurring events
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
    = Tx\CzSimpleCal\Hook\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]
    = Tx\CzSimpleCal\Hook\DataHandlerHook::class;

$iconFactory = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconFactory->registerIcon(
    'extension-czsimplecal-content-calendar',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:cz_simple_cal/Resources/Public/Icons/content_calendar.svg']
);
$iconFactory->registerIcon(
    'extension-czsimplecal-content-calendar-slider',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:cz_simple_cal/Resources/Public/Icons/content_calendar_slider.svg']
);
unset($iconFactory);

$isUsingSingleCategory = (bool)TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    Tx\CzSimpleCal\Utility\ExtensionConfiguration::class
)->isUsingSingleCategory();
if ($isUsingSingleCategory) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tx-czsimplecal-multiple-to-single-category']
        = Tx\CzSimpleCal\Updates\MultipleToSingleCategoryMigrator::class;
}
unset($isUsingSingleCategory);
