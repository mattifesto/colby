<?php

final class CBPageFrames {

    /**
     * This variable will be set to a substitute ID to be used by CBPageFrames
     * while tests are running.
     */
    static $testID = null;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDB::transaction(function () {
            CBModels::deleteByID(CBPageFrames::ID());
        });
    }

    /**
     * @return array
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
    }

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'frameClassNames' => CBModel::valueToArray($spec, 'frameClassNames'),
        ];
    }

    /**
     * @return [string]
     */
    static function fetchFrameClassNames(): array {
        $model = CBModels::fetchModelByID(CBPageFrames::ID());

        return CBModel::valueToArray($model, 'frameClassNames');
    }


    /**
     * @return ID
     */
    static function ID(): string {
        return CBPageFrames::$testID ??
            'ba9966cc7bdc57747fbe07f684dd7097e04b1aa6';
    }

    /**
     * @param string $frameClassName
     *
     * @return void
     */
    static function installFrame(string $frameClassName): void {
        if (!is_callable("{$frameClassName}::CBPageFrame_render")) {
            return;
        }

        $originalSpec = CBModels::fetchSpecByID(CBPageFrames::ID());

        if (empty($originalSpec)) {
            $originalSpec = (object)[
                'ID' => CBPageFrames::ID(),
            ];
        }

        $spec = CBModel::clone($originalSpec);
        $spec->className = 'CBPageFrames';

        if (empty($spec->frameClassNames)) {
            $frameClassNames = [];
        } else {
            $frameClassNames = $spec->frameClassNames;
        }

        array_push($frameClassNames, $frameClassName);

        $frameClassNames = array_values(array_filter(array_unique(
            $frameClassNames
        )));

        $spec->frameClassNames = $frameClassNames;

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }
}
