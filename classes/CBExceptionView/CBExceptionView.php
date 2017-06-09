<?php

/**
 * This class represents a special kind of view that can't be serialized because
 * it contains an exception instance. This model for this view is created in
 * response to an exception.
 */
final class CBExceptionView {

    /**
     * @param Exception? $model->exception
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        if (empty($model->exception)) {
            return;
        }

        $stackTrace = Colby::exceptionStackTrace($model->exception);

        ?>

        <div class="CBExceptionView">
            <pre><?= cbhtml($stackTrace) ?></pre>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }
}
