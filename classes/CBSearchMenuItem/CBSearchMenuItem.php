<?php

final class CBSearchMenuItem {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v485.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- CBMenuItem interfaces -- -- -- -- -- */

    /**
     * @param object $model
     *
     * @return void
     */
    static function CBMenuItem_render(stdClass $model): void {
        ?>

        <span class="CBSearchMenuItem">
            <span>Search</span>
        </span>

        <?php
    }
    /* CBMenuItem_render() */


    /* -- CBModel interfaces -- -- -- -- -- */

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[];
    }
    /* CBModel_build() */
}
/* CBSearchMenuItem */
