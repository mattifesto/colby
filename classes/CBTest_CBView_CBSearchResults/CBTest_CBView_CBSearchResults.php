<?php

final class
CBTest_CBView_CBSearchResults {

    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'render',
                'type' => 'CBTest_type_server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_render(
    ): stdClass {

        /**
         * 2021_02_17
         *
         *      The was a bug that searching for a single letter produce an
         *      exception. This test is to prevent regressions.
         */

        $viewSpec = CBModel::createSpec(
            'CBView_CBSearchResults'
        );

        CBView_CBSearchResults::setSearchQuery(
            $viewSpec,
            'a'
        );

        ob_start();

        try {
            CBView::renderSpec(
                $viewSpec
            );
        } catch (Throwable $error) {
            throw $error;
        } finally {
            ob_end_clean();
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_render() */

}
