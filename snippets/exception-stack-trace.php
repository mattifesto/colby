<?php

echo CBRequest::requestInformationAsMessage();
echo "\n\n";
echo CBConvert::throwableToStackTrace($exception);
