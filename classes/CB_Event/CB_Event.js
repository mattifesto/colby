(function () {
    "use strict";

    window.CB_Event = {
        create: CB_Event_create,
    };

    /**
     * @return object
     *
     *      {
     *          CB_Event_addListener() -> undefined
     *          CB_Event_dispatch(value) -> undefined
     *          CB_Event_removeListener() -> undefined
     *      }
     */
    function
    CB_Event_create(
    ) {
        let dispatchIsHappening = false;
        let listeners = [];



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CB_Event_addListener(
            listener
        ) {
            if (
                typeof listener !== "function"
            ) {
                throw new TypeError(
                    "The parameter to CBEvent.addListener() must be a function."
                );
            }

            if (
                !listeners.includes(
                    listener
                )
            ) {
                listeners.push(
                    listener
                );
            }
        }
        /* CB_Event_addListener() */



        /**
         * A call to dispatch() on an event while dispatch is happening for that
         * event will be ignored.
         *
         * @param mixed argument (optional)
         *
         *      If the event has argument information to provide when it is
         *      dispatched it should be provided with this argument.
         *
         * @return undefined
         */
        function
        CB_Event_dispatch(
            argument
        ) {
            if (
                dispatchIsHappening
            ) {
                return;
            }

            dispatchIsHappening = true;

            listeners.forEach(
                function (
                    listener
                ) {
                    listener(
                        argument
                    );
                }
            );

            dispatchIsHappening = false;
        }
        /* CB_Event_dispatch() */



        /**
         * closure in create()
         *
         * @param function listener
         *
         * @return undefined
         */
        function
        CB_Event_removeListener(
            listener
        ) {
            if (
                typeof listener !== "function"
            ) {
                throw new TypeError(
                    "The parameter to CBEvent.removeListener() must be a " +
                    "function."
                );
            }

            let index = listeners.indexOf(
                listener
            );

            if (
                index > -1
            ) {
                listeners.splice(
                    index,
                    1
                );
            }
        }
        /* CB_Event_removeListener() */

        

        return {
            CB_Event_addListener,
            CB_Event_dispatch,
            CB_Event_removeListener,
        };
    }
    /* CB_Event_create() */

})();
