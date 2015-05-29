<?php

final class CBPagesMaintenance {

    /**
     * This function is presented by the CBPage admin pages when there are
     * unpublished pages that have URIs.
     *
     * Unpublished pages should always have a URI of NULL. When an unpublished
     * page is found with a URI it represents a bug in the code. Find it and
     * fix it before running this function.
     *
     * @return null
     */
    public static function cleanUnpublishedPagesForAjax() {
        $response   = new CBAjaxResponse();
        $SQL        = <<<EOT

            UPDATE `ColbyPages`
            SET     `URI` = NULL
            WHERE   `published` IS NULL AND
                    `URI` IS NOT NULL

EOT;

        Colby::query($SQL);

        $response->wasSuccessful    = true;
        $response->message          = 'The unpublished pages were cleaned.';
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function cleanUnpublishedPagesForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }
}
