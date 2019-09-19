"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIPanel_Tests */
/* global
    CBUI,
    CBUIPanel,
*/

var CBUIPanel_Tests = {

    /**
     * @return object
     */
    CBTest_deprecated: function () {
        CBUIPanel.reset();
        CBUIPanel.message = `

        It was the best of times, it was the worst of times, it was the age of
        wisdom, it was the age of foolishness, it was the epoch of belief, it
        was the epoch of incredulity, it was the season of Light, it was the
        season of Darkness, it was the spring of hope, it was the winter of
        despair, we had everything before us, we had nothing before us, we were
        all going direct to Heaven, we were all going direct the other way—in
        short, the period was so far like the present period, that some of its
        noisiest authorities insisted on its being received, for good or for
        evil, in the superlative degree of comparison only.

        `;

        CBUIPanel.buttons = [
            {
                title: "Dismiss",
            },
            {
                callback: function () {
                    window.alert("This is the alert!");
                },
                title: "Alert",
            }
        ];

        CBUIPanel.isShowing = true;

        return {
            succeeded: true,
        };
    },
    /* CBTest_deprecated() */


    /**
     * @return object
     */
    CBTest_displayElementThreeTimes: function () {
        display("one", "red");
        display("two", "green");
        display("three", "blue");

        return {
            succeeded: true,
        };

        /* -- closures -- -- -- -- -- */

        function display(text, color) {
            let contentElement = CBUI.createElement();
            contentElement.style.backgroundColor = color;
            contentElement.textContent = text;

            contentElement.addEventListener(
                "click",
                function clickEventListener() {
                    contentElement.CBUIPanel.hide();
                }
            );

            CBUIPanel.displayElement(contentElement);
        }
    },
    /* CBTest_displayElementThreeTimes() */


    /**
     * @return object
     */
    CBTest_displayError: function () {
        CBUIPanel.displayError(
            TypeError("This is an example error.")
        );

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayError() */


    /**
     * @return object
     */
    CBTest_displayTextThreeTimes: function () {
        CBUIPanel.displayText(
            `
            He was so fluttered and so glowing with his good intentions, that
            his broken voice would scarcely answer to his call. He had been
            sobbing violently in his conflict with the Spirit, and his face was
            wet with tears.
            `
        );

        CBUIPanel.displayText(
            `
            “I will live in the Past, the Present, and the Future!”, Scrooge
            repeated, as he scrambled out of bed. “The Spirits of all Three
            shall strive within me. Oh Jacob Marley! Heaven, and the Christmas
            Time be praised for this! I say it on my knees, old Jacob; on my
            knees!”
            `
        );

        CBUIPanel.displayText(
            `
            And the bedpost was his own. The bed was his own, the room was his
            own. Best and happiest of all, the Time before him was his own, to
            make amends in!
            `
        );

        return {
            succeeded: true,
        };
    },
    /* CBTest_displayTextThreeTimes() */
};
/* CBUIPanel_Tests */
