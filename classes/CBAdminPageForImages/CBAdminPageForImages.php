<?php

class CBAdminPageForImages {

    /**
     * @return void
     */
    public static function renderAsHTML() {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        CBHTMLOutput::setTitleHTML('Images Administration');
        CBHTMLOutput::setDescriptionHTML('Tools to administer website images.');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        include __DIR__ . '/CBAdminPageForImagesHTML.php';


        CBHTMLOutput::render();
    }
}
