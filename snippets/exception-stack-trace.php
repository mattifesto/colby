<?php

echo CBRequest::requestInformation();
echo "\n\n";
echo CBConvert::throwableToStackTrace($exception);
