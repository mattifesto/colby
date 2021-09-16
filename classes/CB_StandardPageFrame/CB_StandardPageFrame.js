(function () {

    window.addEventListener(
        "resize",
        function() {
            CB_StandardPageFrame_handleResize();
        }
    );

    CB_StandardPageFrame_handleResize();



    /**
     * @return undefined
     */
    function
    CB_StandardPageFrame_handleResize() {
        if (window.innerWidth > 960) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_firstSidebarEnabled"
            );
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_firstSidebarEnabled"
            );
        }

        if (window.innerWidth > 1200) {
            document.documentElement.classList.add(
                "CB_StandardPageFrame_secondSidebarEnabled"
            );
        } else {
            document.documentElement.classList.remove(
                "CB_StandardPageFrame_secondSidebarEnabled"
            );
        }
    }
    /* CB_StandardPageFrame_handleResize() */

})();
