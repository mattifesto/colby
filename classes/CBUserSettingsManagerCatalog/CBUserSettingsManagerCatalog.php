<?php

final class CBUserSettingsManagerCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBUserSettingsManagerCatalog while tests are running.
     */
    static $testID = null;



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBUserSettingsManagerCatalog::ID()
                );

                CBModels::save(
                    (object)[
                        'ID' => CBUserSettingsManagerCatalog::ID(),
                        'className' => 'CBUserSettingsManagerCatalog',
                    ]
                );
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */




    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $managers = CBModel::valueToArray(
            $spec,
            'managers'
        );

        usort(
            $managers,
            function ($a, $b) {
                $asort = CBModel::valueAsInt($a, 'sort') ?? 100;
                $bsort = CBModel::valueAsInt($b, 'sort') ?? 100;

                return $asort <=> $bsort;
            }
        );

        return (object)[
            'managers' => array_values($managers),
        ];
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return ID
     */
    static function ID(): string {
        return (
            CBUserSettingsManagerCatalog::$testID ??
            '4e31f4b1aa8039c1195b55f15bee9e84e991ddbb'
        );
    }



    /**
     * @return [string]
     *
     *      This function returns a list of user settings manager class names
     *      that can be viewed by the current user for the target user.
     */
    static function getListOfClassNames(
        string $targetUserCBID
    ): array {
        $catalog = CBModels::fetchModelByIDNullable(
            CBUserSettingsManagerCatalog::ID()
        );

        $managers = CBModel::valueToArray(
            $catalog,
            'managers'
        );

        $classNames = array_map(
            function ($manager) {
                return $manager->className;
            },
            $managers
        );

        $classNames = array_filter(
            $classNames,
            function ($className) use ($targetUserCBID) {
                return CBUserSettingsManager::currentUserCanViewForTargetUser(
                    $className,
                    $targetUserCBID
                );
            }
        );

        return array_values(
            $classNames
        );
    }
    /* getListOfClassNames() */



    /**
     * @param string $userSettingsClassName
     * @param int $sort
     *
     * @return void
     */
    static function installUserSettingsManager(
        string $managerClassName,
        int $sort = 100
    ): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBUserSettingsManagerCatalog::ID(),
            ]
        );

        $managers = CBModel::valueToArray(
            $updater->working,
            'managers'
        );

        array_push(
            $managers,
            (object)[
                'className' => $managerClassName,
                'sort' => $sort,
            ]
        );

        $updater->working->managers = $managers;

        CBModelUpdater::save($updater);
    }
    /* installUserSettingsManager() */



    /**
     * This function is called by the code that renders the admin page for user
     * settings for a specific user.
     *
     * @param string $targetUserID
     *
     * @return void
     */
    static function renderUserSettingsManagerViews(
        string $targetUserID
    ): void {
        echo (
            '<div class="' .
            'CBUserSettingsManagerCatalog_userSettingsManagerViews' .
            '">'
        );

        $catalogModel = CBModels::fetchModelByID(
            CBUserSettingsManagerCatalog::ID()
        );

        $managers = CBModel::valueToArray(
            $catalogModel,
            'managers'
        );

        foreach ($managers as $manager) {
            $managerClassName = CBModel::valueToString(
                $manager,
                'className'
            );

            CBUserSettingsManager::render(
                $managerClassName,
                $targetUserID
            );
        }

        echo '</div>';
    }
    /* renderUserSettingsManagerViews() */

}
