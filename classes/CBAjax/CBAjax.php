<?php

final class CBAjax {


    /**
     * This function should only be called by the ColbyRequest class.
     *
     * @return void
     */
    static function handleCallAjaxFunctionRequest(): void {
        header('Content-type: application/json');

        try {
            $response = (object)[
                'className' => 'CBAjax',
                'message' => '',
                'wasSuccessful' => false,
            ];

            $ajaxArgumentsAsJSON = trim(
                cb_post_value('ajaxArgumentsAsJSON')
            );

            $ajaxArguments = json_decode($ajaxArgumentsAsJSON);

            CBAjax::validateAjaxArguments($ajaxArguments);

            $functionClassName = $ajaxArguments->functionClassName;
            $functionName = $ajaxArguments->functionName;
            $functionArguments = $ajaxArguments->functionArguments;

            $function = "{$functionClassName}::CBAjax_{$functionName}";
            $getGroupFunction = "{$function}_group";

            if (!is_callable($function)) {
                $information = CBRequest::requestInformation();

                $simpleMessage = (
                    "A request was made to call the Ajax function " .
                    "\"{$functionName}\" on the \"{$functionClassName}\" " .
                    "class but the function \"{$function}()\" has not " .
                    "been implemented."
                );

                $message = <<<EOT

                    {$simpleMessage}

                    --- pre\n{$information}
                    ---

                EOT;

                CBLog::log(
                    (object)[
                        'className' => __CLASS__,
                        'message' => $message,
                        'severity' => 3,
                    ]
                );

                if (
                    CBUserGroup::currentUserIsMemberOfUserGroup(
                        'CBDevelopersUserGroup'
                    )
                ) {
                    $response->message = $simpleMessage;
                } else {
                    $response->message = (
                        'You do not have permission to call a requested ' .
                        'Ajax function.'
                    );
                }
            } else if (!is_callable($getGroupFunction)) {
                $information = CBRequest::requestInformation();

                $simpleMessage = (
                    "A request was made to call the Ajax function " .
                    "\"{$functionName}\" on the \"{$functionClassName}\" " .
                    "class but the group function \"{$getGroupFunction}()\" " .
                    "has not been implemented."
                );

                $message = <<<EOT

                    {$simpleMessage}

                    --- pre\n{$information}
                    ---

                EOT;

                CBLog::log(
                    (object)[
                        'className' => __CLASS__,
                        'message' => $message,
                        'severity' => 3,
                    ]
                );

                if (
                    CBUserGroup::currentUserIsMemberOfUserGroup(
                        'CBDevelopersUserGroup'
                    )
                ) {
                    $response->message = $simpleMessage;
                } else {
                    $response->message = (
                        'You do not have permission to call a requested ' .
                        'Ajax function.'
                    );
                }
            } else {
                $group = call_user_func($getGroupFunction);

                if (ColbyUser::currentUserIsMemberOfGroup($group)) {
                    $response->value = call_user_func(
                        $function,
                        $functionArguments
                    );

                    $response->wasSuccessful = true;
                } else if (ColbyUser::getCurrentUserCBID() === null) {
                    $response->message = (
                        'The requested Ajax function cannot be called ' .
                        'because you are not currently logged in, possibly ' .
                        'because your session has timed out. Reloading ' .
                        'the current page will usually remedy this.'
                    );

                    $response->userMustLogIn = true;
                } else {
                    $response->message = (
                        'You do not have permission to call a requested ' .
                        'Ajax function.'
                    );

                    $response->userMustLogIn = false;
                }
            }
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);

            $response->wasSuccessful = false;
            $response->message = $throwable->getMessage();

            if (
                CBUserGroup::currentUserIsMemberOfUserGroup(
                    'CBDevelopersUserGroup'
                )
            ) {
                $response->classNameForException = get_class($throwable);
                $response->stackTrace = Colby::exceptionStackTrace($throwable);
            }
        }

        echo json_encode($response);
    }
    /* handleCallAjaxFunctionRequest() */



    /**
     * @return bool
     */
    static function requestIsToCallAnAjaxFunction(): bool {
        $ajaxArgumentsAsJSON = trim(
            cb_post_value('ajaxArgumentsAsJSON')
        );

        return !empty($ajaxArgumentsAsJSON);
    }
    /* requestIsToCallAnAjaxFunction() */



    /**
     * This function validates Ajax arguments.
     *
     * @param object $ajaxArguments
     *
     *      {
     *          functionClassName: string
     *          functionName: string
     *          functionArguments: object
     *      }
     *
     * @return void
     */
    static function validateAjaxArguments(
        stdClass $ajaxArguments
    ): void {
        /* function class name */

        $functionClassName = CBModel::valueAsName(
            $ajaxArguments,
            'functionClassName'
        );

        if ($functionClassName === null) {
            throw CBExceptionWithValue(
                (
                    'A request to call an Ajax function has an invalid ' .
                    'functionClassName.'
                ),
                $ajaxArguments,
                '09e390eef9781c3a42a0c030547ff75ee48f1240'
            );
        }


        /* function name */

        $functionName = CBModel::valueAsName(
            $ajaxArguments,
            'functionName'
        );

        if ($functionName === null) {
            throw CBExceptionWithValue(
                (
                    'A request to call an Ajax function has an invalid ' .
                    'functionName.'
                ),
                $ajaxArguments,
                '5b220e8410fe7e3a68176a860724b65892be0847'
            );
        }


        /* function arguments */

        $functionArguments = CBModel::valueAsObject(
            $ajaxArguments,
            'functionArguments'
        );

        if ($functionArguments === null) {
            throw CBExceptionWithValue(
                (
                    'A request to call an Ajax function has ' .
                    'functionArguments that are not an object.'
                ),
                $ajaxArguments,
                '102a3f06edc442ff265a29d463da8b725db73416'
            );
        }
    }
    /* validateAjaxArguments() */

}
/* CBAjax */
