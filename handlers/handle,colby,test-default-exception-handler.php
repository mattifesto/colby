<?php

/**
 * @TODO 2019_08_04
 *
 *      This test is actually broken. It's mean to show the default exception
 *      report page but now just shows a blank page.
 *      ColbyRequest::handleRequest() sets up a try block that prevents it from
 *      working. This is not necessarily a bad thing because we are moving away
 *      from exception handling functions.
 *
 *      However, we need to have test for the default exception handling
 *      functionality, whatever it should be, so the reason for this test still
 *      exists. It will have to be implemented on another day.
 *
 * This page exists to test the default exception handler that is set up in
 * Colby::initialize(). Most exceptions won't use this exception handler because
 * CBHTMLOutput registers its own exception handler.
 *
 * We only throw an exception when an Administrator views the page.
 */

 $isAdministrator = CBUserGroup::userIsMemberOfUserGroup(
     ColbyUser::getCurrentUserCBID(),
     'CBAdministratorsUserGroup'
 );

 if ($isAdministrator) {
    throw new Exception(
        'The /colby/test-default-exception-handler/ page was viewed. ' .
        'This is a test of the Colby default exception handler.'
    );
}
