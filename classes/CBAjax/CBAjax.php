<?php

final class
CBAjax
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.54.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CB_AjaxRequest',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- */



    /**
     * @param string $ajaxClassName
     * @param object $args
     *
     * @return object
     */
    private static function
    executeForAjaxClassName(
        $ajaxClassName,
        $args
    ): stdClass
    {
        $response = (object)[
            'className' =>
            'CBAjaxResponse',

            'wasSuccessful' =>
            false,

            'message' =>
            CBConvert::stringToCleanLine(<<<EOT

                A call was made to CBAjax::executeForAjaxClassName() which was
                not able to complete its duties, one of which is to either clear
                or change this message.

            EOT),
        ];

        $currentUserModelCBID =
        ColbyUser::getCurrentUserCBID();

        $functionName =
        "${ajaxClassName}::CBAjax_userModelCBIDCanExecute";

        $canExecute =
        call_user_func(
            $functionName,
            $currentUserModelCBID
        );

        if (
            $canExecute !== true
        ) {
            $response->message =
            CBConvert::stringToCleanLine(<<<EOT

                The current user is not allowed to execute the function
                implemented by the ${ajaxClassName} class name.

            EOT);

            $response->wasSuccessful =
            false;

            return $response;
        }



        $functionName =
        "${ajaxClassName}::CBAjax_execute";

        $response->value =
        call_user_func(
            $functionName,
            $args
        );

        $response->wasSuccessful =
        true;

        $response->message =
        '';

        return $response;
    }
    /* executeForAjaxClassName() */



    /**
     * This function should only be called by the ColbyRequest class.
     *
     * This function creates a CBAjaxResponse model as as its response to the
     * request. See the CBAjaxResponse class for more information.
     *
     * @return void
     */
    static function
    handleCallAjaxFunctionRequest(
    ): void
    {
        header(
            'Content-type: application/json'
        );

        set_exception_handler(
            'CBAjax::handleCallAjaxFunctionRequest_handleError'
        );

        $ajaxRequestModelAsJSON =
        trim(
            cb_post_value(
                'CB_AjaxRequest_model_form_key'
            )
        );

        $ajaxRequestModel =
        json_decode(
            $ajaxRequestModelAsJSON
        );

        CBAjax::validateAjaxArguments(
            $ajaxRequestModel
        );


        /* executor class name requests */

        $executorClassName =
        CB_AjaxRequest::getExecutorClassName(
            $ajaxRequestModel
        );

        if (
            $executorClassName !== null
        ) {
            $executorArguments =
            CB_AjaxRequest::getExecutorArguments(
                $ajaxRequestModel
            );

            $response =
            CBAjax::executeForAjaxClassName(
                $executorClassName,
                $executorArguments
            );

            goto done;
        }


        /* deprecated style ajax requests */

        $response =
        (object)[
            'className' =>
            'CBAjaxResponse',

            'message'
            => '',

            'wasSuccessful'
            => false,
        ];

        $executorFunctionClassName =
        CB_AjaxRequest::getExecutorFunctionClassName(
            $ajaxRequestModel
        );

        $executorFunctionName =
        CB_AjaxRequest::getExecutorFunctionName(
            $ajaxRequestModel
        );

        $executorArguments =
        CB_AjaxRequest::getExecutorArguments(
            $ajaxRequestModel
        );

        $interfaceName =
        "CBAjax_{$executorFunctionName}";

        $callable =
        "{$executorFunctionClassName}::{$interfaceName}";

        if (
            !is_callable($callable)
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The Ajax interface {$interfacename}() has not been
                    implemented on the {$executorFunctionClassName} class to
                    implement the requested Ajax function call.

                EOT),
                $ajaxRequestModel,
                '35ea28899f1335170ec7ec9b42a134c875037a5f'
            );
        }

        $userIsMemberOfUserGroup =
        false;

        $getUserGroupClassNameFunctionName =
        (
            $executorFunctionClassName .
            "::CBAjax_{$executorFunctionName}_getUserGroupClassName"
        );

        if (
            is_callable($getUserGroupClassNameFunctionName)
        ) {
            $userGroupClassName =
            call_user_func(
                $getUserGroupClassNameFunctionName
            );

            $userIsMemberOfUserGroup =
            CBUserGroup::userIsMemberOfUserGroup(
                ColbyUser::getCurrentUserCBID(),
                $userGroupClassName
            );
        }

        else {
            throw new CBExceptionWithValue(
                (
                    'The CBAjax_function_getUserGroupClassName() ' .
                    'interface was not implemented for this Ajax ' .
                    'function call.'
                ),
                $ajaxRequestModel,
                'bc0310f24170ebee3dfc6bf4d47ce284a5408646'
            );
        }


        if (
            !$userIsMemberOfUserGroup
        ) {
            if (
                ColbyUser::getCurrentUserCBID() === null
            ) {
                $response->message =
                (
                    'The requested Ajax function cannot be called ' .
                    'because you are not currently logged in, possibly ' .
                    'because your session has timed out. Reloading ' .
                    'the current page will usually remedy this.'
                );

                $response->sourceCBID =
                'e7041967a2b8b1643aff0009c961265fcf1a5453';

                $response->userMustLogIn =
                true;

                echo
                json_encode(
                    CBModel::build(
                        $response
                    )
                );

                return;
            }

            else {
                $isDeveloper =
                CBUserGroup::currentUserIsMemberOfUserGroup(
                    'CBDevelopersUserGroup'
                );

                if (
                    $isDeveloper
                ) {
                    $response->message =
                    CBConvert::stringToCleanLine(<<<EOT

                        You do not have permission to call the Ajax function
                        with the class "{$executorFunctionClassName}" and the
                        name "{$executorFunctionName}".

                    EOT);
                }

                else {
                    $response->message =
                    'You do not have permission to call a requested ' .
                    'Ajax function.';
                }

                $response->sourceCBID =
                '596da90f52ad2590f67d65e885d6d5e85ca590dd';

                echo
                json_encode(
                    CBModel::build(
                        $response
                    )
                );

                return;
            }
        }

        $response->value =
        call_user_func(
            $callable,
            $executorArguments
        );

        $response->wasSuccessful =
        true;

        done:

        echo
        json_encode(
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
    static function
    handleCallAjaxFunctionRequest_handleError(
        Throwable $error
    ): void
    {
        CBErrorHandler::report(
            $error
        );

        $response =
        (object)[
            'className' =>
            'CBAjaxResponse',

            'wasSuccessful' =>
            false,

            'sourceCBID' =>
            CBException::throwableToSourceCBID(
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

        $response->message =
        $error->getMessage();

        $isDeveloper =
        CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBDevelopersUserGroup'
        );

        if (
            $isDeveloper
        ) {
            $response->stackTrace =
            CBErrorHandler::throwableToPlainTextIteratedStackTrace(
                $error
            );
        }

        echo
        json_encode(
            CBModel::build(
                $response
            )
        );
    }
    /* handleCallAjaxFunctionRequest_handleError() */



    /**
     * @return bool
     */
    static function
    requestIsToCallAnAjaxFunction(
    ): bool
    {
        $ajaxRequestModelAsJSON =
        trim(
            cb_post_value(
                'CB_AjaxRequest_model_form_key'
            )
        );

        return
        !empty(
            $ajaxRequestModelAsJSON
        );
    }
    /* requestIsToCallAnAjaxFunction() */



    /**
     * This function validates an CB_AjaxRequest model.
     *
     * @TODO 2022_02_06
     *
     *      This function should be renamed and moved to the CB_AjaxRequest
     *      class.
     *
     * @param object $ajaxArguments
     *
     * @return void
     */
    static function
    validateAjaxArguments(
        stdClass $ajaxRequestModel
    ): void
    {
        $executorClassName =
        CB_AjaxRequest::getExecutorClassName(
            $ajaxRequestModel
        );

        if (
            $executorClassName === null
        ) {
            $executorFunctionClassName =
            CB_AjaxRequest::getExecutorFunctionClassName(
                $ajaxRequestModel
            );

            if (
                $executorFunctionClassName === null
            ) {
                throw
                new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        A CB_AjaxRequest model has an invalid executor function
                        class name.

                    EOT),
                    $ajaxRequestModel,
                    '09e390eef9781c3a42a0c030547ff75ee48f1240'
                );
            }



            $executorFunctionName =
            CB_AjaxRequest::getExecutorFunctionName(
                $ajaxRequestModel
            );

            if (
                $executorFunctionName === null
            ) {
                throw
                new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        A CB_AjaxRequest model has an invalid executor function
                        name.

                    EOT),
                    $ajaxRequestModel,
                    '5b220e8410fe7e3a68176a860724b65892be0847'
                );
            }
        }



        $executorArguments =
        CB_AjaxRequest::getExecutorArguments(
            $ajaxRequestModel
        );

        if (
            $executorArguments === null
        ) {
            throw
            new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    A CB_AjaxRequest model has invalid executor arguments.

                EOT),
                $ajaxRequestModel,
                '102a3f06edc442ff265a29d463da8b725db73416'
            );
        }
    }
    /* validateAjaxArguments() */

}
/* CBAjax */
