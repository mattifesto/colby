<?php

final class CBUINavigationView {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUISpecEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUINavigationView::URL('CBUINavigationView.js')];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
