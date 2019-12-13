<?php

class CBImagesAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



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
        return [
            'develop',
            'images'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render() {
        CBHTMLOutput::pageInformation()->title = 'Images Administration';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



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
     */
    static function CBAjax_fetchImages() {

        /**
         * @NOTE 2018_01_28
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
    /* CBAjax_fetchImages() */



    /**
     * @return string
     */
    static function CBAjax_fetchImages_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */




    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v374.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBUI',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUIStringsPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBDevelopAdminMenu::ID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'images',
            'text' => 'Images',
            'URL' => '/admin/?c=CBImagesAdmin',
        ];

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBDevelopAdminMenu'
        ];
    }
    /* CBInstall_requiredClassNames() */

}
/* CBImagesAdmin */
