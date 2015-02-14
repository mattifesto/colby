<?php

class CPView {

    /**
     * @return bool
     */
    public static function autoload($className) {
        $filepath = __DIR__ . "/views/{$className}/{$className}.php";

        if (is_file($filepath))
        {
            include_once $filepath;

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return stdClass
     */
    public static function compile($spec) {
        $className = $spec->viewClassName;
        return $className::compile($spec);
    }

    /**
     * @return void
     */
    public static function renderAsHTML($model) {
        $className = $model->viewClassName;
        $className::renderAsHTML($model);
    }

    /**
     * @return stdClass
     */
    public static function specForClassName($className) {
        $spec = new stdClass();
        $spec->viewClassName = $className;

        return $spec;
    }
}

spl_autoload_register('CPView::autoload');
