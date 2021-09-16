<?php

final class
CBPageFrame {

    /* -- functions -- */



    /**
     * @param ?string $frameClassName
     * @param callable $renderContent
     *
     * @return void
     */
    static function
    render(
        ?string $frameClassName,
        callable $renderContent
    ): void {
        $functionName = "{$frameClassName}::CBPageFrame_render";

        if (
            is_callable($functionName)
        ) {
            CBHTMLOutput::requireClassName(
                $frameClassName
            );

            call_user_func(
                $functionName,
                $renderContent
            );
        } else {
            call_user_func(
                $renderContent
            );
        }
    }
    /* render() */

}
