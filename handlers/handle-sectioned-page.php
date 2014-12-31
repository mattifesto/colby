<?php

/**
 * Expected variables: $dataStoreID
 */

$page = CBViewPage::initWithID($dataStoreID);
$page->renderHTML();
