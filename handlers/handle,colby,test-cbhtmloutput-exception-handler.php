<?php

/**
 * This page exists to test the CBHTMLOutput custom exception handler that is
 * set up in CBHTMLOutput::begin().
 *
 * We only throw an exception when an Administrator views the page.
 */

 $isAdministrator = CBUserGroup::userIsMemberOfUserGroup(
     ColbyUser::getCurrentUserCBID(),
     'CBAdministratorsUserGroup'
 );

 if ($isAdministrator) {
    CBHTMLOutput::begin();

    ?>

    This is some test content that should not show up on the exception report
    page.

    <?php

    throw new Exception(
        "The /colby/test-cbhtmloutput-exception-handler/ page was " .
        "viewed. This is a test of the CBHTMLOutput custom exception handler."
    );

    CBHTMLOutput::render();
}
