<?php

final class CBAdministratorsUserGroup {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUsers::installUserGroup('Administrators');
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUsers'];
    }
}
