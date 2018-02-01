<?php

final class CBMenuItem {

    /**
     * @param object $spec
     *
     *      {
     *          name: string?
     *          submenuID: hex160?
     *          text: string?
     *          URL: string?
     *      }
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        $model = (object)[
            'name' => CBModel::value($spec, 'name', '', 'ColbyConvert::textToStub'),
            'submenuID' => CBConvert::valueAsHex160(CBModel::value($spec, 'submenuID')),
            'text' => CBModel::value($spec, 'text', '', 'strval'),
            'URL' => CBModel::value($spec, 'URL', '', 'trim'),
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
