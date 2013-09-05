<?php

include_once COLBY_DIRECTORY . '/classes/ColbyNestedDictionary.php';

define('COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

$nestedDictionary = ColbyNestedDictionary::nestedDictionaryWithTitle('Documents Administration Menu');

$nestedDictionary->addValue('main', 'titleHTML', 'Main');
$nestedDictionary->addValue('main', 'uri', '/admin/documents/');
