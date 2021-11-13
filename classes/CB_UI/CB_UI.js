/* globals
    Colby,

    CB_UI_CBSitePreferences_appearance,
*/

(function () {

    if (
        CB_UI_CBSitePreferences_appearance === (
            'CBSitePreferences_appearance_light'
        )
    ) {
        Colby.afterDOMContentLoaded(
            function () {
                setLightAppearance();
            }
        );

        return;
    }


    if (
        CB_UI_CBSitePreferences_appearance === (
            'CBSitePreferences_appearance_dark'
        )
    ) {
        Colby.afterDOMContentLoaded(
            function () {
                setDarkAppearance();
            }
        );

        return;
    }


    let doesBrowserPreferDarkMode = window.matchMedia(
        "(prefers-color-scheme: dark)"
    );

    doesBrowserPreferDarkMode.addEventListener(
        "change",
        function () {
            setAutoAppearance();
        }
    );

    Colby.afterDOMContentLoaded(
        function () {
            setAutoAppearance();
        }
    );



    /**
     * @return undefined
     */
    function
    setAutoAppearance(
    ) {
        if (doesBrowserPreferDarkMode.matches) {
            setDarkAppearance();
        } else {
            setLightAppearance();
        }
    }
    /* setAutoAppearance() */



    /**
     * @return undefined
     */
    function
    setDarkAppearance(
    ) {
        document.documentElement.classList.add(
            "CB_UI_browserPrefersDarkMode",
            "CBDarkTheme" /* 2021_09_06 deprecated */
        );

        document.documentElement.classList.remove(
            "CBLightTheme" /* 2021_09_06 deprecated */
        );
    }
    /* setDarkAppearance() */



    /**
     * @return undefined
     */
    function
    setLightAppearance(
    ) {
        document.documentElement.classList.remove(
            "CB_UI_browserPrefersDarkMode",
            "CBDarkTheme" /* 2021_09_06 deprecated */
        );

        document.documentElement.classList.add(
            "CBLightTheme" /* 2021_09_06 deprecated */
        );
    }
    /* setLightAppearance() */

})();
