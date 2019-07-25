<?php

final class CBUserSettingsManagerCatalog {

    /**
     * This variable will be set to a substitute ID to be used by
     * CBUserSettingsManagerCatalog while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBUserSettingsManagerCatalog::ID()
                );
            }
        );
    }


    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
        ];
    }


    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $managers = CBModel::valueToArray($spec, 'managers');

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


    /**
     * @return ID
     */
    static function ID(): string {
        return CBUserSettingsManagerCatalog::$testID ??
            '4e31f4b1aa8039c1195b55f15bee9e84e991ddbb';
    }


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
        $originalSpec = CBModels::fetchSpecByID(
            CBUserSettingsManagerCatalog::ID()
        );

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBUserSettingsManagerCatalog::ID(),
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $spec->className = 'CBUserSettingsManagerCatalog';

        $managers = CBModel::valueToArray($spec, 'managers');

        array_push($managers, (object)[
            'className' => $managerClassName,
            'sort' => $sort,
        ]);

        $spec->managers = $managers;

        if ($spec != $originalSpec) {
            CBDB::transaction(
                function () use ($spec) {
                    CBModels::save($spec);
                }
            );
        }
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

        $managers = CBModel::valueToArray($catalogModel, 'managers');

        foreach ($managers as $manager) {
            $managerClassName = CBModel::valueToString($manager, 'className');

            CBUserSettingsManager::render($managerClassName, $targetUserID);
        }

        echo '</div>';
    }
    /* renderUserSettingsManagerViews() */
}
