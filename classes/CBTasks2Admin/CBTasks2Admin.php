<?php

final class CBTasks2Admin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['general', 'tasks'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::setTitleHTML('Tasks Administration');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v378.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI', 'CBUISectionItem4', 'CBUIStringsPart'];
    }
}
