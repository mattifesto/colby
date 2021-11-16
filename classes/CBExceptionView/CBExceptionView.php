<?php

/**
 * This class represents a special kind of view that can't be serialized because
 * it contains an exception instance. This model for this view is created in
 * response to an exception.
 */
final class
CBExceptionView {

    private static $throwableStack = [];



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     *      {
     *          exception: Throwable
     *      }
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $model
    ): void {
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

            if (
                CBUserGroup::userIsMemberOfUserGroup(
                    ColbyUser::getCurrentUserCBID(),
                    'CBDevelopersUserGroup'
                )
            ) {
                $cbmessage = <<<EOT

                    This content is being rendered by CBExceptionView because
                    you are a member of  CBDevelopersUserGroup.

                EOT;

                $cbmessage .= CBException::throwableToCBMessage(
                    $throwable
                );

                $stackTraceAsMessage = CBMessageMarkup::stringToMessage(
                    CBErrorHandler::throwableToPlainTextIteratedStackTrace(
                        $throwable
                    )
                );

                $cbmessage .= <<<EOT

                    --- dl
                        --- dt
                        Stack
                        ---
                        --- dd
                            --- trace\n{$stackTraceAsMessage}
                            ---
                        ---
                    ---

                EOT;

                if ($cbmessage !== '') {
                    CBView::renderSpec(
                        (object)[
                            'className' => 'CBMessageView',
                            'markup' => $cbmessage,
                        ]
                    );
                }
            } else {
                $message = <<<EOT

                    --- h1
                    Sorry, something has gone wrong.
                    ---

                    An error occurred on this page and our administrators have
                    been notified.

                EOT;

                CBView::renderSpec(
                    (object)[
                        'className' => 'CBMessageView',
                        'markup' => $message,
                    ]
                );
            }

            ?>

        </div>

        <?php
    }
    /* CBView_render() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v385.css', cbsysurl()),
        ];
    }



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[
            'className' => __CLASS__,
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return void
     */
    static function popThrowable(): void {
        array_pop(
            CBExceptionView::$throwableStack
        );
    }



    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    static function pushThrowable(Throwable $throwable): void {
        array_push(
            CBExceptionView::$throwableStack,
            $throwable
        );
    }

}
