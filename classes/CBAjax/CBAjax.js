"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */

(function () {

    window.CBAjax = {
        call,
    };



    /**
     * @param string className
     * @param string functionName
     * @param object args (optional)
     * @param File file (optional)
     *
     *      A File usually retrieved from an input element.
     *
     *      https://developer.mozilla.org/en-US/docs/Web/API/File
     *
     * @return Promise
     */
    function call(
        functionClassName,
        functionName,
        functionArguments,
        file
    ) {
        var formData = new FormData();

        if (functionArguments === undefined) {
            functionArguments = {};
        }

        formData.append(
            "ajaxArgumentsAsJSON",
            JSON.stringify(
                {
                    functionClassName: functionClassName,
                    functionName: functionName,
                    functionArguments: functionArguments,
                }
            )
        );

        if (file !== undefined) {
            formData.append("file", file);
        }

        return fetchResponse(
            "/",
            formData
        ).then(
            function (response) {
                return response.value;
            }
        );
    }
    /* call() */



    /**
     * This function is the recommended way to make an Ajax request for Colby.
     * To alleviate error notifications in situations where customers have less
     * stable internet service, this function will attempt the Ajax request
     * up to three times if errors occur.
     *
     * @param string URL
     * @param any? data
     *
     *      The data can be of any form accepted by the XMLHttpRequest.send()
     *      function. Most commonly, if used, it will be a FormData instance.
     *
     * @return Promise
     *
     *      Returns a promise that passes an 'ajax response' object (created by
     *      this class) to resolve handlers. If an error occurs for any reason a
     *      JavaScript Error object is passed to reject handlers with an 'ajax
     *      response' object set to the Error's `ajaxResponse` propery.
     */
    function fetchResponse(
        URL,
        data
    ) {
        if (
            typeof URL !== "string" ||
            URL === ""
        ) {
            throw TypeError(
                "fetchResponse() was called with an invalid URL " +
                "parameter value of: " +
                JSON.stringify(URL)
            );
        }

        return new Promise(executor);



        /**
         * @param function resolve
         * @param function reject
         *
         * @return undefined
         */
        function executor(
            resolve,
            reject
        ) {
            let fetchCount = 0;
            let xhr;

            fetch();


            /**
             * @return undefined
             */
            function fetch() {
                xhr = new XMLHttpRequest();
                xhr.onloadend = handleLoadEnd;
                xhr.open("POST", URL);
                xhr.send(data);

                fetchCount += 1;
            }
            /* fetch() */



            /**
             * @return undefined
             */
            function handleLoadEnd() {
                if (xhr.status === 0 && fetchCount < 3) {
                    fetch();
                    return;
                }

                let ajaxResponse = responseFromXMLHttpRequest(
                    xhr
                );

                if (ajaxResponse.wasSuccessful) {
                    resolve(ajaxResponse);
                } else {
                    let error = new Error(ajaxResponse.message);
                    error.ajaxResponse = ajaxResponse;

                    reject(error);
                }
            }
            /* handleLoadEnd() */

        }
        /* executor() */

    }
    /* fetchResponse() */



    /**
     * @param object xhr
     *
     * @return object
     *
     *      {
     *          message: string,
     *          wasSuccessful: bool,
     *          xhr: XMLHttpRequest,
     *      }
     */
    function responseFromXMLHttpRequest(
        xhr
    ) {
        let response;

        switch (xhr.status) {
            case 0:

                response = {
                    className: "CBAjaxResponse",
                    message: "An error occured when making an Ajax request " +
                              "to the server. This was most likely caused by " +
                              "a network issue or less likely caused by the " +
                              "server domain name in the request URL being " +
                              "incorrect.",
                    wasSuccessful: false,
                };

                break;

            case 200:

                try {
                    response = JSON.parse(xhr.responseText);
                } catch (error) {
                    response = {
                        className: "CBAjaxResponse",
                        message: "An Ajax request to the server returned " +
                                 "without error but the xhr.responseText is " +
                                 "not valid JSON.",
                        wasSuccessful: false,
                    };
                }

                break;

            case 404:

                response = {
                    className: "CBAjaxResponse",
                    message: (
                        "An Ajax request to the server ended with a status " +
                        "of 404, meaning the request URL was not found. " +
                        "The request URL was: " +
                        (
                            xhr.responseURL ||
                            "(not available in this browser)"
                        )
                    ),
                    wasSuccessful: false,
                };

                break;

            default:

                response = {
                    className: "CBAjaxResponse",
                    message: "An Ajax request to the server returned an " +
                              "unexpected response with the status code " +
                              xhr.status + " and the status text: \"" +
                              xhr.statusText + "\".",
                    wasSuccessful: false,
                };

                break;
        }

        response.xhr = xhr;

        return response;
    }
    /* responseFromXMLHttpRequest() */

})();
