<?php

final class CBPageFrame {

    /**
     * @param ?string $pageFrameClassName
     * @param function $renderContent
     *
     * @return void
     */
    static function render(?string $pageFrameClassName, callable $renderContent): void {
        if (is_callable($function = "{$pageFrameClassName}::CBPageFrame_render")) {
            CBHTMLOutput::requireClassName($pageFrameClassName);
            call_user_func($function, $renderContent);
        } else {
            call_user_func($renderContent);
        }
    }
}
