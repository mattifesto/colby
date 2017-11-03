<?php

/**
 * 2014.03.27 This file should be included during installations and upgrades.
 */

CBInstall::install();
CBDataStoreAdmin::install(); /* deprecated */
CBDataStores::install();
CBLog::install();
CBTasks::install(); /* deprecated */
CBTasks2::install();
CBUsers::install();
CBModels::install();
CBModelAssociations::install();
CBModelsPreferences::install();
CBPages::install();
CBSitePreferences::install();
CBThemedTextView::install(); /* deprecated */
CBImages::install();
CBWellKnownMenuForMain::install();
CBWellKnownPageForTestingCBTextView2::install();
CBWellKnownPageForTestingPageTitleAndBodyText::install();

// 2015.10.26
CBUpgradesForVersion172::run();

// 2015.12.28
CBUpgradesForVersion178::run();

// 2016.02.10
CBUpgradesForVersion183::run();

// 2016.03.15
CBUpgradesForVersion188::run();

// 2016.04.28
CBUpgradesForVersion191::run();

// 2017.06.25
CBUpgradesForVersion279::run();

// 2017.10.20
CBUpgradesForVersion346::run();

// 2017.10.27
CBUpgradesForVersion351::run();

/**
 * Tasks that can only be run after tables are created properly.
 */

CBDataStoresFinderTask::install();
CBLogMaintenanceTask::install();

/**
 * These functions are also called from CBRemoteAdministration::ping()
 */
CBImageVerificationTask::startForNewImages();
CBPageVerificationTask::startForNewPages();
