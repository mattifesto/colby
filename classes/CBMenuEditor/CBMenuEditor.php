<?php

final class CBMenuEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBMenuItemEditor', 'CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBMenuEditor::URL('CBMenuEditor.js')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
