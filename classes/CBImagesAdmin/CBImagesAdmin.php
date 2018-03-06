<?php

class CBImagesAdmin {

    /**
     * @return string
     */
    static function CBAdmin_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'images'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render() {
        CBHTMLOutput::pageInformation()->title = 'Images Administration';
    }

    /**
     * @return [object]
     *
     *      {
     *          ID: hex160
     *          created: int
     *          extension: string
     *          modified: int
     *          thumbnailURL: string
     *      }
     *
     */
    static function CBAjax_fetchImages() {

        /**
         * @NOTE 2018.01.28
         *
         * This SQL was copied from the CBModels class and customized. A query
         * like this belongs in the CBModels class but it is unclear how to
         * generalize it since "get models by class name" functionality isn't
         * needed very often.
         */

        $SQL = <<<EOT

            SELECT      v.modelAsJSON
            FROM        CBModels AS m
            JOIN        CBModelVersions AS v ON
                        m.ID = v.ID AND
                        m.version = v.version
            WHERE       m.className = 'CBImage'
            ORDER BY    m.modified DESC
            LIMIT       500

EOT;

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        return $images;
    }

    /**
     * @return string
     */
    static function CBAjax_fetchImages_group() {
        return 'Administrators';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUISectionItem4', 'CBUIStringsPart'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBDevelopAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'images',
            'text' => 'Images',
            'URL' => '/admin/?c=CBImagesAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBDevelopAdminMenu'];
    }
}
