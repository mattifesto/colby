<?php

final class CBPageFrame {

    /**
     * @param ?string $frameClassName
     * @param callable $renderContent
     *
     * @return void
     */
    static function render(?string $frameClassName, callable $renderContent): void {
        if (is_callable($function = "{$frameClassName}::CBPageFrame_render")) {
            CBHTMLOutput::requireClassName($frameClassName);
            call_user_func($function, $renderContent);
        } else {
            call_user_func($renderContent);
        }
    }
}
