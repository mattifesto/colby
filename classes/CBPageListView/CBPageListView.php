<?php

final class CBPageListView {

    /**
     * @param stdClass $model->classNameForKind
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        return $model->classNameForKind;
    }

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        echo "<p>{$model->classNameForKind}";
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'classNameForKind' => CBModel::value($spec, 'classNameForKind', null, 'trim'),
            'themeID' => CBModel::value($spec, 'themeID'),
        ];
    }
}
