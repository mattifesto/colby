<?php

class CBAdminPageForImages {

    /**
     * @return void
     */
    public static function renderAsHTML() {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::begin();
        CBHTMLOutput::setTitleHTML('Images Administration');
        CBHTMLOutput::setDescriptionHTML('Tools to administer website images.');

        include __DIR__ . '/CBAdminPageForImagesHTML.php';

        CBHTMLOutput::render();
    }
}
