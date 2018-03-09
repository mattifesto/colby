<?php

final class CBAjax {

    /**
     * @return bool
     *
     *      Returns true if this was an Ajax request; otherwise false.
     */
    static function call() {
        $modelAsJSON = cb_post_value('ajax');

        if (empty($modelAsJSON)) {
            return false;
        }

        $response = new CBAjaxResponse();
        $model = json_decode($modelAsJSON);
        $className = CBModel::value($model, 'functionClassName');
        $functionName = CBModel::value($model, 'functionName');
        $args = CBModel::valueToObject($model, 'args');

        $function = "{$className}::CBAjax_{$functionName}";
        $getGroupFunction = "{$function}_group";

        if (is_callable($function) && is_callable($getGroupFunction)) {
            $group = call_user_func($getGroupFunction);

            if (ColbyUser::currentUserIsMemberOfGroup($group)) {
                $response->value = call_user_func($function, $args);
                $response->wasSuccessful = true;
            } else if (ColbyUser::currentUserId() === null) {
                $response->message = "The requested Ajax function cannot be called because you are not currently logged in, possibly because your session has timed out. Reloading the current page will usually remedy this.";
                $response->userMustLogIn = true;
            } else {
                $response->message = "You do not have permission to call a requested Ajax function.";
                $response->userMustLogIn = false;
            }
        } else {
            $information = CBRequest::requestInformation();
            $message = <<<EOT

                A request was made to call the Ajax function "{$functionName}"
                on the "{$className}" class which is not implemented.

                --- pre\n{$information}
                ---

EOT;

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => $message,
                'severity' => 4,
            ]);

            $response->message = 'You do not have permission to call a requested Ajax function.';
        }

        $response->send();

        return true;
    }
}
