<?php

final class CBPage_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'render',
                'title' => 'CBPage::render()',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    static function CBTest_render(): stdClass {
        $actualSourceCBID = null;

        try {
            CBPage::render(
                (object)[
                    'className' => 'CBPage_Tests',
                ]
            );
        } catch (CBException $exception) {
            $actualSourceCBID = $exception->getSourceCBID();
        }

        $expectedSourceCBID = '5b1831c54024accfe5ed6a4c4eb2d724a5d19845';

        if ($actualSourceCBID !== $expectedSourceCBID) {
            return CBTest::resultMismatchFailure(
                'source CBID',
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_render() */

}
