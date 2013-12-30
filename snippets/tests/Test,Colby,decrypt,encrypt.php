<?php

$originalText = 'Hello, world!';

$cipherOutput = Colby::encrypt($originalText);

$decryptedText = Colby::decrypt($cipherOutput);

CBCompareAnActualTestResultToAnExpectedTestResult($decryptedText, $originalText);
