<?php

final class CBModelClassInfo {

    /**
     * @param {string} $className
     *
     * @return {stdClass}
     */
    public static function classNameToInfo($className) {
        if (is_callable($function = "{$className}::info")) {
            return call_user_func($function);
        } else {
            return CBModelClassInfo::specToModel();
        }
    }

    /**
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec = null) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->pluralTitle = isset($spec->pluralTitle) ? trim($spec->pluralTitle) : '';
        $model->pluralTitleAsHTML = cbhtml($model->pluralTitle);
        $model->singularTitle = isset($spec->singularTitle) ? trim($spec->singularTitle) : '';
        $model->singularTitleAsHTML = cbhtml($model->singularTitle);
        $model->userGroup = isset($spec->userGroup) ? trim($spec->userGroup) : 'Developers';

        return $model;
    }
}
