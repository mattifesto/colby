/* jshint esversion: 6 */

(function () {
    "use strict";

    window.CBID = {
        generateRandomCBID: CBID_generateRandomCBID,
    };



    /**
     * This method generates a random hex string representing a 160-bit number
     * which is the same length as a SHA-1 hash and can be used as a unique ID.
     *
     * @return string
     */
    function
    CBID_generateRandomCBID(
    ) {
        let randomNumbers = new Uint16Array(10);

        window.crypto.getRandomValues(
            randomNumbers
        );

        let CBID = "";

        for (
            let index = 0;
            index < 10;
            index++
        ) {
            let hexString = randomNumbers[index].toString(
                16
            );

            let leadingZeros = "0000".substr(
                0,
                4 - hexString.length
            );

            CBID = CBID + leadingZeros + hexString;
        }

        return CBID;
    }
    /* CBID_generateRandomCBID() */

})();
