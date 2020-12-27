/* jshint
    esversion: 6,
    strict: true,
    undef: true,
    unused: true
*/
/* globals
    CBAjax,
    CBConvert,
    CBException,
    CBModel,
*/

(function () {
    "use strict";

    window.CBSpecSaver = {
        create: CBSpecSaver_create,
    };



    /**
     * @param object spec
     *
     * @return object
     *
     *      {
     *          CBSpecSaver_save() -> Promise -> undefined
     *      }
     */
    function
    CBSpecSaver_create(
        spec
    ) {
        let attemptActiveSaveTimeoutID;

        let savePromise;
        let savePromise_resolve;
        let savePromise_reject;

        let activeSavePromise;
        let activeSavePromise_resolve;
        let activeSavePromise_reject;

        return {
            CBSpecSaver_save,
        };



        /**
         * This function makes a request to save the spec. The save is
         * asynchronous and may be delayed for a short time period to reduce
         * server requests in times of high changes.
         *
         * You can call this function frequently and often with no ill effects.
         * Of course, it does no good call it if the spec hasn't actually
         * changed.
         *
         * @return Promise -> undefined
         */
        function
        CBSpecSaver_save(
        ) {
            if (attemptActiveSaveTimeoutID !== undefined) {
                clearTimeout(
                    attemptActiveSaveTimeoutID
                );

                attemptActiveSaveTimeoutID = undefined;
            }

            if (savePromise === undefined) {
                savePromise = new Promise(
                    function (
                        resolve,
                        reject
                    ) {
                        savePromise_resolve = resolve;
                        savePromise_reject = reject;
                    }
                );
            }

            attemptActiveSaveTimeoutID = setTimeout(
                CBSpecSaver_attemptActiveSave,
                1000
            );

            return savePromise;
        }
        /* CBSpecSaver_save() */



        /**
         * If a save is not currenty active, this function will begin an active
         * save. If a save is currenty active, and a timeout is not currently
         * active this function will set another timeout to attempt an active
         * save in one second.
         *
         * @return undefined
         */
        function
        CBSpecSaver_attemptActiveSave(
        ) {
            if (activeSavePromise !== undefined) {
                if (attemptActiveSaveTimeoutID === undefined) {
                    attemptActiveSaveTimeoutID = setTimeout(
                        CBSpecSaver_attemptActiveSave,
                        1000
                    );
                }
            } else {
                CBSpecSaver_beginActiveSave();
            }
        }
        /* CBSpecSaver_attemptActiveSave() */



        /**
         * This function will make an Ajax request to save the spec. It contains
         * a developer assertion that save is not currently active which should
         * never be the case.
         *
         * @return undefined
         */
        function
        CBSpecSaver_beginActiveSave(
        ) {
            if (activeSavePromise) {
                throw CBException.withError(
                    Error(
                        CBConvert.stringToCleanLine(`

                                CBSpecSaver_saveNow() was called while a save
                                was in progress.

                        `)
                    )
                );
            }

            activeSavePromise = savePromise;
            activeSavePromise_resolve = savePromise_resolve;
            activeSavePromise_reject = savePromise_reject;
            savePromise = undefined;
            savePromise_resolve = undefined;
            savePromise_reject = undefined;

            CBAjax.call(
                "CBModels",
                "save",
                {
                    spec,
                }
            ).then(
                function () {
                    let specVersion = CBModel.getVersion(
                        spec
                    );

                    if (specVersion === undefined) {
                        specVersion = 1;
                    } else {
                        specVersion += 1;
                    }

                    CBModel.setVersion(
                        spec,
                        specVersion
                    );

                    activeSavePromise_resolve();
                }
            ).catch(
                function (error) {
                    activeSavePromise_reject(
                        error
                    );
                }
            ).finally(
                function () {
                    activeSavePromise = undefined;
                    activeSavePromise_resolve = undefined;
                    activeSavePromise_reject = undefined;
                }
            );
        }
        /* CBSpecSaver_saveNow() */

    }
    /* CBSpecSaver_create() */

})();
