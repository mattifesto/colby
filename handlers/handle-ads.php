<?php

header("Content-Type: text/plain");

echo CBSitePreferences::getAdsTxtContent(
    CBSitePreferences::model()
);
