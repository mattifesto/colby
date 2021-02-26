<?php

final class CBAjax {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v668.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /* -- functions -- -- -- -- -- */



    /**
     * This function should only be called by the ColbyRequest class.
     *
     * This function creates a CBAjaxResponse model as as its response to the
     * request. See the CBAjaxResponse class for more information.
     *
     * @return void
     */
    static function handleCallAjaxFunctionRequest(): void {
        header('Content-type: application/json');

        set_exception_handler(
            'CBAjax::handleCallAjaxFunctionRequest_handleError'
        );

        $response = (object)[
            'className' => 'CBAjaxResponse',
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

        $interfaceName = "CBAjax_{$functionName}";

        $callable = "{$functionClassName}::{$interfaceName}";

        if (!is_callable($callable)) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The Ajax interface {$interfaceName}() has not been
                    implemented on the {$functionClassName} class to implement
                    the requested Ajax function call.

                EOT),
                $ajaxArguments,
                '35ea28899f1335170ec7ec9b42a134c875037a5f'
            );
        }

        $userIsMemberOfUserGroup = false;

        $getUserGroupClassNameFunctionName = (
            $functionClassName .
            "::CBAjax_{$functionName}_getUserGroupClassName"
        );

        if (is_callable($getUserGroupClassNameFunctionName)) {
            $userGroupClassName = call_user_func(
                $getUserGroupClassNameFunctionName
            );

            $userIsMemberOfUserGroup = CBUserGroup::userIsMemberOfUserGroup(
                ColbyUser::getCurrentUserCBID(),
                $userGroupClassName
            );
        } else {
            throw new CBExceptionWithValue(
                (
                    'The CBAjax_function_getUserGroupClassName() ' .
                    'interface was not implemented for this Ajax ' .
                    'function call.'
                ),
                $ajaxArguments,
                'bc0310f24170ebee3dfc6bf4d47ce284a5408646'
            );
        }


        if (!$userIsMemberOfUserGroup) {
            if (ColbyUser::getCurrentUserCBID() === null) {
                $response->message = (
                    'The requested Ajax function cannot be called ' .
                    'because you are not currently logged in, possibly ' .
                    'because your session has timed out. Reloading ' .
                    'the current page will usually remedy this.'
                );

                $response->sourceCBID = (
                    'e7041967a2b8b1643aff0009c961265fcf1a5453'
                );

                $response->userMustLogIn = true;

                echo json_encode(
                    CBModel::build(
                        $response
                    )
                );

                return;
            } else {
                $isDeveloper = CBUserGroup::currentUserIsMemberOfUserGroup(
                    'CBDevelopersUserGroup'
                );

                if ($isDeveloper) {
                    $response->message = CBConvert::stringToCleanLine(<<<EOT

                        You do not have permission to call the Ajax function
                        with the class "{$functionClassName}" and the name
                        "{$functionName}".

                    EOT);
                } else {
                    $response->message = (
                        'You do not have permission to call a requested ' .
                        'Ajax function.'
                    );
                }

                $response->sourceCBID = (
                    '596da90f52ad2590f67d65e885d6d5e85ca590dd'
                );

                echo json_encode(
                    CBModel::build(
                        $response
                    )
                );

                return;
            }
        }

        $response->value = call_user_func(
            $callable,
            $functionArguments
        );

        $response->wasSuccessful = true;

        echo json_encode(
            CBModel::build(
                $response
            )
        );
    }
    /* handleCallAjaxFunctionRequest() */



    /**
     * This function is set as the exception handler in
     * CBAjax::handleCallAjaxFunctionRequest().
     *
     * @param Throwable $error
     *
     * @return void
     */
    static function handleCallAjaxFunctionRequest_handleError(
        Throwable $error
    ): void {
        CBErrorHandler::report($error);

        $response = (object)[
            'className' => 'CBAjaxResponse',

            'wasSuccessful' => false,

            'sourceCBID' => CBException::throwableToSourceCBID(
                $error
            ),
        ];

        /**
         * @TODO 2019_12_04
         *
         * It is absolutely wrong to set the message to the exception
         * message here because non-developer users should not be able to
         * see the exception message.
         *
         * To fix this, the shopping cart library needs to be updated so
         * that it doesn't throw exceptions with credit card messages.
         * (declined, bad card number, ...)
         *
         * We could also potentially implement a public/private exception
         * that has one message for developers and one for users.
         */

        $response->message = $error->getMessage();

        $isDeveloper = CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBDevelopersUserGroup'
        );

        if ($isDeveloper) {
            $response->stackTrace = (
                CBErrorHandler::throwableToPlainTextIteratedStackTrace(
                    $error
                )
            );
        }

        echo json_encode(
            CBModel::build(
                $response
            )
        );
    }
    /* handleCallAjaxFunctionRequest_handleError() */



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
