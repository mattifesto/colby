<?php

/**
 * This page exists to test the default exception handler that is set up in
 * Colby::initialize(). Most exceptions won't use this exception handler because
 * CBHTMLOutput and CBAjaxResponse register their own exception handlers.
 *
 * We only throw an exception when an Administrator views the page.
 */

if (ColbyUser::currentUserIsMemberOfGroup('Administrators')) {
    throw new Exception("The /colby/test-default-exception-handler/ page was viewed");
}
