<?php

final class SCUpgradesForVersion129 {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDBA::dropTableColumn(
            'SCOrders',
            'keyValueData'
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'SCOrdersTable',
        ];
    }
    /* CBInstall_requiredClassNames() */

}
