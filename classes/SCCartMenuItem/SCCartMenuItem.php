<?php

final class
SCCartMenuItem
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            Colby::flexpath(__CLASS__, 'v98.js', scliburl()),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'Colby',
            'SCCartItem',
            'SCShoppingCart',
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

        <span class="SCCartMenuItem">
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
/* SCCartMenuItem */
