<?php

/**
 * This class represents a special kind of view that can't be serialized because
 * it contains an exception instance. This model for this view is created in
 * response to an exception.
 */
final class CBExceptionView {

    private static $throwableStack = [];

    /**
     * @param Exception? $model->exception
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (!empty($model->exception)) {
            $throwable = $model->exception;
        } else {
            $throwable = end(CBExceptionView::$throwableStack);
        }

        if (empty($throwable)) {
            return;
        }

        ?>

        <div class="CBExceptionView">

            <?php

            if (ColbyUser::currentUserIsMemberOfGroup('Developers')) {
                $stackTrace = CBConvert::throwableToStackTrace($throwable);

                ?>

                <div class="trace"><?= cbhtml($stackTrace) ?></div>

                <?php
            } else {
                $md = <<<EOT

# Sorry, something has gone wrong.

An error occurred on this page and our administrators have been notified.

EOT;

                CBView::renderSpec((object)[
                    'className' => 'CBTextView2',
                    'contentAsCommonMark' => $md,
                ]);
            }

            ?>

        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
        ];
    }

    /**
     * @return null
     */
    static function popThrowable() {
        array_pop(CBExceptionView::$throwableStack);
    }

    /**
     * @param Throwable $throwable
     *
     * @return null
     */
    static function pushThrowable(Throwable $throwable) {
        array_push(CBExceptionView::$throwableStack, $throwable);
    }
}
