<?php

final class
CBPageFrame {

    /* -- functions -- */



    /**
     * @param ?string $pageFrameClassName
     * @param callable $renderContent
     *
     * @return void
     */
    static function
    render(
        ?string $pageFrameClassName,
        callable $renderContent
    ): void {

        /**
         * If a page frame class is deprecated it can implement the
         * CBPageFrame_replacementPageFrameClassName() interface to provide the
         * replacement page fram class name.
         */

        $functionName = (
            $pageFrameClassName .
            '::CBPageFrame_replacementPageFrameClassName'
        );

        if (
            is_callable($functionName)
        ) {
            $pageFrameClassName = call_user_func(
                $functionName
            );
        }

        $functionName = (
            $pageFrameClassName .
            '::CBPageFrame_render'
        );

        if (
            is_callable($functionName)
        ) {
            CBHTMLOutput::requireClassName(
                $pageFrameClassName
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
