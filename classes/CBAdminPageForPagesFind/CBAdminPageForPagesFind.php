<?php

final class CBAdminPageForPagesFind {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['pages', 'find'];
    }

    /**
     * @return stdClass
     */
    public static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Find Pages');
        CBHTMLOutput::setDescriptionHTML('Find pages to edit, copy, or delete.');
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {

        /**
         * @deprecated This class holds an older implementation of this page
         * and the functions that belong in this class should move to this
         * class and an associated class JavaScript file.
         */

        return ['CBPagesAdministrationView'];
    }
}
