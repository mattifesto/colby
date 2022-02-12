(function () {

    let elementsAreReady;
    let leftSidebarIsEnabled;
    let rightSidebarIsEnabled;

    window.addEventListener(
        "resize",
        function() {
            CB_StandardPageFrame_handleResize();
        }
    );

    CB_StandardPageFrame_handleResize();

    document.addEventListener(
        "DOMContentLoaded",
        function () {
            elementsAreReady = true;

            CB_StandardPageFrame_handleResize();

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
         * If the window is greater than 1201 points wide, show the left
         * sidebar. We will only show another sidebar if main panel can still be
         * 720 points wide.
         *
         * 1202 = 240 + 1 (border) + 720 + 1 (border) + 240
         */

        if (
            window.innerWidth > 1201
        ) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_leftSidebarEnabled"
            );

            if (
                elementsAreReady === true &&
                leftSidebarIsEnabled !== true
            ) {
                leftSidebarIsEnabled = true;

                CB_StandardPageFrame_leftSidebarBecameEnabled();
            }
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_leftSidebarEnabled"
            );

            if (
                elementsAreReady === true &&
                leftSidebarIsEnabled !== false
            ) {
                leftSidebarIsEnabled = false;

                CB_StandardPageFrame_leftSidebarBecameDisabled();
            }
        }



        /**
         * If the window is greater than 960 points wide, show the right
         * sidebar. We will only show another sidebar if main panel can still be
         * 720 points wide.
         *
         * 961 = 720 + 1 (border) + 240
         */

        if (
            window.innerWidth > 960
        ) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_rightSidebarEnabled"
            );

            if (
                elementsAreReady === true &&
                rightSidebarIsEnabled !== true
            ) {
                rightSidebarIsEnabled = true;

                CB_StandardPageFrame_rightSidebarBecameEnabled();
            }
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_rightSidebarEnabled"
            );

            if (
                elementsAreReady === true &&
                rightSidebarIsEnabled !== false
            ) {
                rightSidebarIsEnabled = false;

                CB_StandardPageFrame_rightSidebarBecameDisabled();
            }
        }
    }
    /* CB_StandardPageFrame_handleResize() */



    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_leftSidebarBecameEnabled(
    ) {
        let pageLeftSidebarElement = document.getElementsByClassName(
            "CB_StandardPageFrame_page_leftSidebar_element"
        )[0];


        let leftSidebarContentElement = document.getElementsByClassName(
            "CB_StandardPageFrame_leftSidebarContent_element"
        )[0];

        pageLeftSidebarElement.append(
            leftSidebarContentElement
        );
    }
    /* CB_StandardPageFrame_leftSidebarBecameEnabled() */



    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_leftSidebarBecameDisabled(
    ) {
        let menuPopupMainElement = document.getElementsByClassName(
            "CB_StandardPageFrame_menuPopup_main_element"
        )[0];

        let leftSidebarContentElement = document.getElementsByClassName(
            "CB_StandardPageFrame_leftSidebarContent_element"
        )[0];

        menuPopupMainElement.append(
            leftSidebarContentElement
        );
    }
    /* CB_StandardPageFrame_leftSidebarBecameDisabled() */



    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_rightSidebarBecameEnabled(
    ) {
        let pageRightSidebarElement = document.getElementsByClassName(
            "CB_StandardPageFrame_page_rightSidebar_element"
        )[0];


        let rightSidebarContentElement = document.getElementsByClassName(
            "CB_StandardPageFrame_rightSidebarContent_element"
        )[0];

        pageRightSidebarElement.append(
            rightSidebarContentElement
        );
    }
    /* CB_StandardPageFrame_rightSidebarBecameEnabled() */



    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_rightSidebarBecameDisabled(
    ) {
        let menuPopupMainElement = document.getElementsByClassName(
            "CB_StandardPageFrame_menuPopup_main_element"
        )[0];

        let rightSidebarContentElement = document.getElementsByClassName(
            "CB_StandardPageFrame_rightSidebarContent_element"
        )[0];

        menuPopupMainElement.append(
            rightSidebarContentElement
        );
    }
    /* CB_StandardPageFrame_rightSidebarBecameDisabled() */

})();
