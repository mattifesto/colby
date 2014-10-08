<?php

/**
 * Create a subclass of this class to create an API that can be called using
 * the URL:
 *
 *  http://site.com/api/?className=CBAPIDoSomethingInteresting
 *
 * Perform your APIs function and add values to the `$response` in your class's
 * `process` function override.
 */
class CBAPI
{
    protected $response;

    private function __construct() { }

    /**
     * Override this function to implement the initialation of your API.
     * Gathering and validating POST or GET variables should happen here.
     *
     * @return void
     */
    protected function init() { }

    /**
     * @return void
     */
    final public static function call() {

        $api            = new static();
        $api->response  = new CBAjaxResponse();

        if ($api->userIsAuthorized()) {

            $api->init();

            $api->response->wasSuccessful = $api->process();
        }

        $api->response->send();
    }

    /**
     * Override this function to implement the logic of your API.
     *
     * @return bool
     *  Return `true` if the API succeeded and `false` if not.
     */
    protected function process() {

        return true;
    }

    /**
     * Override this function to allow users to use your API. Processing will
     * not occur if this function returns `false`. When returning `false` also
     * set the response message to a string explaining that the user is not
     * authorized to use the API or call this function for the standard
     * authorization denied messages.
     *
     * Because it's easy to forget to authorize users this function denies
     * authorization by default and a subclass must explicitly allow it.
     *
     * @return bool
     */
    protected function userIsAuthorized() {

        if (ColbyUser::current()->isLoggedIn()) {

            $message = 'You are not authorized to use this API.';

        } else {

            $message = 'You are not authorized to use this API. ' .
                       'This may be because you are not currently not logged in.';
        }

        $this->response->message = $message;

        return false;
    }
}
