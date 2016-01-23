<?php

final class CBResponsiveImageView {

    /**
     * @param hex160? $model->image->ID
     * @param int $model->contentWidth2x
     *
     * @return string?
     */
    public static function modelToThemeCSS(stdClass $model) {
        $themeID = CBResponsiveImageView::modelToThemeID($model);
        $class = "T{$themeID}";

        return <<<EOT

.{$class} {
    background-color: red;
}

EOT;
    }

    /**
     * @param hex160? $model->image->ID
     * @param int $model->contentWidth2x
     *
     * @return string?
     */
    public static function modelToThemeFilepath(stdClass $model) {
        if (empty($model->image->ID)) {
            return null;
        } else {
            return CBDataStore::filepath([
                'ID' => $model->image->ID,
                'filename' => "CBResponsiveImageView{$model->contentWidth2x}.css"
            ]);
        }
    }

    /**
     * @param hex160? $model->image->ID
     * @param int $model->contentWidth2x
     *
     * @return hex160?
     */
    public static function modelToThemeID(stdClass $model) {
        if (empty($model->image->ID)) {
            return null;
        } else {
            return sha1("CBResponsiveImageView{$model->image->ID}{$model->contentWidth2x}");
        }
    }

    /**
     * @param hex160? $model->image->ID
     * @param int $model->contentWidth2x
     *
     * @return string?
     */
    public static function modelToThemeURL(stdClass $model) {
        if (empty($model->image->ID)) {
            return null;
        } else {
            return CBDataStore::toURL([
                'ID' => $model->image->ID,
                'filename' => "CBResponsiveImageView{$model->contentWidth2x}.css"
            ]);
        }
    }

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        CBHTMLOutput::addCSSURL(CBResponsiveImageView::modelToThemeURL($model));

        if ($themeID = CBResponsiveImageView::modelToThemeID($model)) {
            $class = "T{$themeID}";
        } else {
            $class = "";
        }

        ?><figure class="CBResponsiveImageView <?= $class ?>">CBResponsiveImageView</figure><?php
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        $model = (object)['className' => __CLASS__];
        $model->image = CBModel::value($spec, 'image'); // CBImageStruct::clone
        $model->contentWidth2x = CBModel::value($spec, 'contentWidth2x', null, function ($v) { return ($v > 0) ? max(640, (int)$v) : null; });

        if ($filepath = CBResponsiveImageView::modelToThemeFilepath($model)) {
            file_put_contents($filepath, CBResponsiveImageView::modelToThemeCSS($model));
        }

        return $model;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
