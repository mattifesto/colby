/* global
    CB_SampleImages_5000x1000_imageModel_jsvariable,
    CB_SampleImages_1000x5000_imageModel_jsvariable,
*/


(function () {
    "use strict";

    window.CB_SampleImages = {
        getSampleImageModelCBID_5000x1000:
        CB_SampleImages_getSampleImageModelCBID_5000x1000,

        getSampleImageModelCBID_1000x5000:
        CB_SampleImages_getSampleImageModelCBID_1000x5000,
    };



    /**
     * @return object
     */
    function
    CB_SampleImages_getSampleImageModelCBID_1000x5000(
    ) // -> object
    {
        return CB_SampleImages_1000x5000_imageModel_jsvariable;
    }
    /* CB_SampleImages_getSampleImageModelCBID_5000x1000() */



    /**
     * @return object
     */
    function
    CB_SampleImages_getSampleImageModelCBID_5000x1000(
    ) // -> object
    {
        return CB_SampleImages_5000x1000_imageModel_jsvariable;
    }
    /* CB_SampleImages_getSampleImageModelCBID_5000x1000() */

})();
