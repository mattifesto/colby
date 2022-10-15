/* globals
    CB_UI_CBSitePreferences_appearance,
*/

(function () {
    "use strict";

    let doesBrowserPreferDarkMode;


    window.CB_UI = {
        getNonBreakingSpaceCharacter,
    };


    if (
        CB_UI_CBSitePreferences_appearance === (
            'CBSitePreferences_appearance_light'
        )
    ) {
        setLightAppearance();
    }


    else if (
        CB_UI_CBSitePreferences_appearance === (
            'CBSitePreferences_appearance_dark'
        )
    ) {
        setDarkAppearance();
    }


    else {
        doesBrowserPreferDarkMode = window.matchMedia(
            "(prefers-color-scheme: dark)"
        );

        doesBrowserPreferDarkMode.addEventListener(
            "change",
            function () {
                setAutoAppearance();
            }
        );

        setAutoAppearance();
    }



    /* -- functions -- */



    /**
     * @return string
     */
    function
    getNonBreakingSpaceCharacter(
    ) {
        return "\u00A0";
    }
    /* getNonBreakingSpaceCharacter() */



    /**
     * @return undefined
     */
    function
    setAutoAppearance(
    ) {
        if (
            doesBrowserPreferDarkMode.matches
        ) {
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
            "CB_UI_theme_dark",

            /**
             * @deprecated 2022_10_15_1665842023
             *
             *      Use CB_UI_theme_dark
             */
            "CB_UI_browserPrefersDarkMode",

            /**
             * @deprecated 2021_09_06
             *
             *      Use CB_UI_theme_dark
             */
            "CBDarkTheme"
        );

        document.documentElement.classList.remove(
            "CB_UI_theme_light",

            /**
             * @deprecated 2021_09_06
             *
             *      Use CB_UI_theme_light
             */
            "CBLightTheme"
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
            "CB_UI_theme_dark",

            /**
             * @deprecated 2022_10_15_1665842023
             *
             *      Use CB_UI_theme_dark
             */
            "CB_UI_browserPrefersDarkMode",

            /**
             * @deprecated 2021_09_06
             *
             *      Use CB_UI_theme_dark
             */
            "CBDarkTheme"
        );

        document.documentElement.classList.add(
            "CB_UI_theme_light",

            /**
             * @deprecated 2021_09_06
             *
             *      Use CB_UI_theme_light
             */
            "CBLightTheme"
        );
    }
    /* setLightAppearance() */

})();
