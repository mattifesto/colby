/* globals
    Colby,
*/

(function () {

    let doesBrowserPreferDarkMode = window.matchMedia(
        "(prefers-color-scheme: dark)"
    );

    doesBrowserPreferDarkMode.addEventListener(
        "change",
        setDarkMode
    );

    Colby.afterDOMContentLoaded(
        function () {
            setDarkMode();
        }
    );



    /**
     * @return undefined
     */
    function
    setDarkMode(
    ) {
        if (doesBrowserPreferDarkMode.matches) {
            document.documentElement.classList.add(
                "CB_UI_browserPrefersDarkMode",
                "CBDarkTheme" /* 2021_09_06 deprecated */
            );

            document.documentElement.classList.remove(
                "CBLightTheme" /* 2021_09_06 deprecated */
            );
        } else {
            document.documentElement.classList.remove(
                "CB_UI_browserPrefersDarkMode",
                "CBDarkTheme" /* 2021_09_06 deprecated */
            );

            document.documentElement.classList.add(
                "CBLightTheme" /* 2021_09_06 deprecated */
            );
        }
    }
    /* setDarkMode() */

})();
