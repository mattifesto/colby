/* global
    CB_CBView_Moment,
    Colby,
*/


(function () {

    Colby.afterDOMContentLoaded(
        function () {
            initialize();
        }
    );



    /**
     * @return undefined
     */
    function
    initialize(
    ) {
        let elements = Array.from(
            document.getElementsByClassName(
                "CB_CBView_MomentPage"
            )
        );

        elements.forEach(
            function (
                element
            ) {
                let momentModelAsJSON = element.dataset.moment;

                let momentModel = JSON.parse(
                    momentModelAsJSON
                );

                let isForMomentPage = true;

                let momentView = CB_CBView_Moment.createStandardMoment(
                    momentModel,
                    isForMomentPage
                );

                let momentViewElement = (
                    momentView.CB_CBView_Moment_getElement()
                );

                momentViewElement.classList.add(
                    'CB_CBView_Moment_momentPage'
                );

                element.append(
                    momentViewElement
                );
            }
        );
    }
    /* initialize() */

})();
