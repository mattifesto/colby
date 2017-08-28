<?php

final class CBModelClassInfo {

    /**
     * @param string $className
     *
     * @return object
     */
    static function classNameToInfo($className) {
        if (is_callable($function = "{$className}::CBModelClassInfo_spec")) {
            $spec = call_user_func($function);
            $spec = clone $spec;
            $spec->className = __CLASS__;
            return CBModel::toModel($spec);
        } else if (is_callable($function = "{$className}::info")) { /* deprecated */
            return call_user_func($function);
        } else {
            return CBModel::toModel((object)[
                'className' => __CLASS__,
            ]);
        }
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function specToModel(stdClass $spec = null) {
        $model = (object)[
            'className' => __CLASS__
        ];

        $model->pluralTitle = isset($spec->pluralTitle) ? trim($spec->pluralTitle) : '';
        $model->pluralTitleAsHTML = cbhtml($model->pluralTitle);
        $model->singularTitle = isset($spec->singularTitle) ? trim($spec->singularTitle) : '';
        $model->singularTitleAsHTML = cbhtml($model->singularTitle);
        $model->userGroup = isset($spec->userGroup) ? trim($spec->userGroup) : 'Developers';

        return $model;
    }
}
