<?php

final class CBMenuItem {

    /**
     * @param model $spec
     *
     *      {
     *          name: string?
     *          submenuID: ID?
     *          text: string?
     *          URL: string?
     *      }
     *
     * @return model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $model = (object)[
            'name' => CBModel::valueToString($spec, 'name'),
            'submenuID' => CBModel::valueAsID($spec, 'submenuID'),
            'text' => CBModel::valueToString($spec, 'text'),
            'URL' => CBModel::valueToString($spec, 'URL'),
        ];

        /**
         * These properties are deprecated. When they are confirmed to be
         * unused remove them.
         */
        $model->textAsHTML = cbhtml($model->text);
        $model->URLAsHTML = cbhtml($model->URL);

        return $model;
    }
}
