/* global
    Colby,
*/

(function () {

    window.addEventListener(
        "resize",
        function() {
            CB_StandardPageFrame_handleResize();
        }
    );

    CB_StandardPageFrame_handleResize();

    Colby.afterDOMContentLoaded(
        function () {
            let menuButtonElement = (
                document.getElementsByClassName(
                    "CB_CBView_MainHeader_menuButton_menu"
                )[0]
            );

            menuButtonElement.addEventListener(
                "click",
                function () {
                    document.documentElement.classList.remove(
                        "CB_StandardPageFrame_mainMenuPopup_isVisible"
                    );
                }
            );

            let pageButtonElement = (
                document.getElementsByClassName(
                    "CB_CBView_MainHeader_menuButton_page"
                )[0]
            );

            pageButtonElement.addEventListener(
                "click",
                function() {
                    document.documentElement.classList.add(
                        "CB_StandardPageFrame_mainMenuPopup_isVisible"
                    );
                }
            );
        }
    );

    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_handleResize(
    ) {
        /**
         * If the window is greater than 960 points wide, show the right
         * sidebar. We will only show another sidebar if main panel can still be
         * 720 points wide.
         *
         * 961 = 720 + 1 + 240
         */

        if (
            window.innerWidth > 960
        ) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_rightSidebarEnabled"
            );
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_rightSidebarEnabled"
            );
        }

        /**
         * If the window is greater than 1201 points wide, show the left
         * sidebar. We will only show another sidebar if main panel can still be
         * 720 points wide.
         *
         * 1202 = 240 + 1 + 720 + 1 + 240
         */

        if (
            window.innerWidth > 1201
        ) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_leftSidebarEnabled"
            );
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_leftSidebarEnabled"
            );
        }
    }
    /* CB_StandardPageFrame_handleResize() */

})();
