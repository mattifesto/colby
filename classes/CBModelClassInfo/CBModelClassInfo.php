<?php

final class CBModelClassInfo {

    /**
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->pluralTitle = isset($spec->pluralTitle) ? trim($spec->pluralTitle) : '';
        $model->pluralTitleAsHTML = cbhtml($model->pluralTitle);
        $model->singularTitle = isset($spec->singularTitle) ? trim($spec->singularTitle) : '';
        $model->singularTitleAsHTML = cbhtml($model->singularTitle);

        return $model;
    }
}
