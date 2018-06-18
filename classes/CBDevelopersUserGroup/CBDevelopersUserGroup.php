<?php

final class CBDevelopersUserGroup {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUsers::installUserGroup('Developers');
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUsers'];
    }
}
