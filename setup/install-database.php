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
CBDataStoresFinderTask::install();
CBLogMaintenanceTask::install();
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

/**
 * These functions are also called from CBRemoteAdministration::ping()
 */
CBImageVerificationTask::startForNewImages();
CBPageVerificationTask::startForNewPages();

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
