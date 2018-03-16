<?php

final class CBPageFrame {

    /**
     * @return ?string
     */
    static function defaultClassName(): ?string {
        if (is_callable($function = 'CBPageFrame_defaultClassName::get')) {
            return call_user_func($function);
        }

        return null;
    }

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
