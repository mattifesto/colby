<?php

final class CBPage {

    /**
     * @param object $model
     *
     * @return null
     */
    static function render(stdClass $model) {
        $className = CBModel::value($model, 'className', '');

        if (is_callable($function = "{$className}::CBPage_render")) {
            call_user_func($function, $model);
        } else {
            $ID = CBModel::value($model, 'ID', '(no ID)');
            $title = CBModel::value($model, 'title', '(no title)');

            throw new Exception("The page, {$title} ({$ID}), was unable to render.");
        }
    }

    /**
     * @param object $spec
     *
     * @return null
     */
    static function renderSpec(stdClass $spec) {
        CBPage::render(CBModel::toModel($spec));
    }
}
