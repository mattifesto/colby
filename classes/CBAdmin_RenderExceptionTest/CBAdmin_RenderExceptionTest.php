<?php

final class CBAdmin_RenderExceptionTest {

    /* -- CBAdmin interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'test',
        ];
    }
    /* CBAdmin_menuNamePath() */


    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        $message = <<<EOT

            This is the message for a test exception thrown by the
            CBAdmin_render() function in the CBAdmin_RenderExceptionTest to
            allow developers to see what an admin page looks like if its
            rendering code throws an exception.

EOT;

        throw new CBException(
            'test exception',
            $message,
            '4820e2b88a99a956e4da692c84173b44564c507e'
        );
    }
    /* CBAdmin_render() */


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'interactive',
                'name' => 'exception',
                'title' => 'Exception during CBAdmin_render()',
                'description' => implode(
                    ' ',
                    [
                        'Opens an admin page in a new tab that throws an',
                        'exception while the page is being rendered by the',
                        'server so the developer can see how an admin page is',
                        'displayed when an exception occurs.',
                    ]
                ),
            ],
        ];
    }
    /* CBTest_getTests() */
}
/* CBAdmin_RenderExceptionTest */
