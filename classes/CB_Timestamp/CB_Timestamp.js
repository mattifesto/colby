/* global
    CBConvert,
    CBException,
    CBModel,
*/


(function () {

    window.CB_Timestamp = {
        getUnixTimestamp,
        setUnixTimestamp,
        getFemtoseconds,
        setFemtoseconds,
        decrement,
        from,
    };



    /* -- accessors -- */



    /**
     * @param object cbtimestampModel
     *
     * @return int
     */
    function
    getUnixTimestamp(
        cbtimestampModel
    ) {
        return CBModel.valueAsInt(
            cbtimestampModel,
            'CB_Timestamp_unixTimestamp_property'
        ) || 0;
    }
    /* getUnixTimestamp() */



    /**
     * @param object cbtimestampModel
     * @param int unixTimestamp
     *
     * @return undefined
     */
    function
    setUnixTimestamp(
        cbtimestampModel,
        unixTimestamp
    ) {
        let verifiedUnixTimestamp = CBConvert.valueAsInt(
            unixTimestamp
        );

        if (
            verifiedUnixTimestamp === undefined
        ) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The unixTimestamp argument must be an integer.",
                    unixTimestamp,
                    '68ee3f611868c52a966071229bc9a62fa66b87d5'
                )
            );
        }

        cbtimestampModel.CB_Timestamp_unixTimestamp_property = (
            verifiedUnixTimestamp
        );
    }
    /* setUnixTimestamp() */



    /**
     * @param object cbtimestampModel
     *
     * @return int
     */
    function
    getFemtoseconds(
        cbtimestampModel
    ) {
        return CBModel.valueAsInt(
            cbtimestampModel,
            'CB_Timestamp_femtoseconds_property'
        ) || 0;
    }
    /* getFemtoseconds() */



    /**
     * @param object cbtimestampModel
     * @param int femtoseconds
     *
     * @return undefined
     */
    function
    setFemtoseconds(
        cbtimestampModel,
        femtoseconds
    ) {
        let verifiedFemtoseconds = CBConvert.valueAsInt(
            femtoseconds
        );

        if (
            verifiedFemtoseconds === undefined ||
            femtoseconds < 0 ||
            femtoseconds > 999999999999999
        ) {
            throw CBException.withValueRelatedError(
                TypeError(
                    CBConvert.stringToCleanLine(`

                        The femtoseconds argument must be an integer beween 0
                        and 999999999999999 inclusive.

                    `),
                    femtoseconds,
                    '133b0225f01677c51ab8a0650e98e9ff657a0880'
                )
            );
        }

        cbtimestampModel.CB_Timestamp_femtoseconds_property = verifiedFemtoseconds;
    }
    /* setFemtoseconds() */



    /* -- functions -- */



    /**
     * @param object cbtimestampModel
     *
     * @return object
     *
     *      Returns a CB_Timestamp model one femtosecond less that the argument.
     */
    function
    decrement(
        cbtimestampModel
    ) {
        let unixTimestamp = getUnixTimestamp(
            cbtimestampModel
        );

        let femtoseconds = getFemtoseconds(
            cbtimestampModel
        );

        if (
            femtoseconds === 0
        ) {
            unixTimestamp -= 1;
            femtoseconds = 999999999999999;
        } else {
            femtoseconds -= 1;
        }

        return from(
            unixTimestamp,
            femtoseconds
        );
    }
    /* decrement() */



    /**
     * @param int unixTimestamp
     * @param int femtoseconds
     *
     * @return object
     */
    function
    from(
        unixTimestamp,
        femtoseconds
    ) {
        let cbtimestampSpec = CBModel.createSpec(
            'CB_Timestamp'
        );

        setUnixTimestamp(
            cbtimestampSpec,
            unixTimestamp
        );

        setFemtoseconds(
            cbtimestampSpec,
            femtoseconds
        );

        return cbtimestampSpec;
    }
    /* from() */

})();
