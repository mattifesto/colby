<?php

final class CBFrontPageKind {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBPageKindCatalog::install(__CLASS__);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBPageKindCatalog'];
    }
}
