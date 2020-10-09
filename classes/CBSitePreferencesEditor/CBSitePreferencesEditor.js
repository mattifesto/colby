"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBSitePreferencesEditor */
/* globals
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIPanel,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby,
*/


(function () {

    window.CBSitePreferencesEditor = {
        CBUISpecEditor_createEditorElement,
    };



    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let section, item;

        let element = document.createElement("div");
        element.className = "CBSitePreferencesEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Site Name",
                    propertyName: "siteName",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Administrator Email Addresses",
                    propertyName: "administratorEmails",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());


        /* -- website icon section -- -- -- -- -- */

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");

            element.appendChild(sectionTitleElement);

            sectionTitleElement.textContent = "Website Icon";
        }

        section = CBUI.createSection();

        element.appendChild(section);

        /* image chooser for website icon */

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_handleImageChosen;
        imageChooser.removed = createEditor_handleImageRemoved;

        imageChooser.src = CBImage.toURL(args.spec.imageForIcon, "rw960");

        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());


        /* -- developer settings section -- -- -- -- -- */

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");

            element.appendChild(sectionTitleElement);

            sectionTitleElement.textContent = "Developer Settings";
        }

        section = CBUI.createSection();

        element.appendChild(section);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "This is a Development Website",
                    propertyName: "debug",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Disallow Robots",
                    propertyName: "disallowRobots",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);


        /* google analytics */

        section.appendChild(
            createGoogleAnalyticsIDEditorElement(
                spec,
                specChangedCallback
            )
        );


        /* default page settings class name */

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: (
                        "Default Class Name for Page Settings (deprecated)"
                    ),
                    propertyName: "defaultClassNameForPageSettings",
                    spec,
                    specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);


        /* on demand resize operations */

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "On Demand Image Resize Operations (deprecated)",
                    propertyName: "onDemandImageResizeOperations",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* path */

        {
            section.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "path",
                        propertyName: "path",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );
        }

        /* Slack */

        section.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Slack Webhook URL",
                    propertyName: "slackWebhookURL",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );


        /* -- social section -- -- -- -- -- */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Facebook URL",
                    propertyName: "facebookURL",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor({
                    labelText: "Twitter URL",
                    propertyName: "twitterURL",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());

        {
            let socialTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            socialTitleElement.textContent = "Social";

            element.appendChild(socialTitleElement);
        }

        element.appendChild(section);

        /* Google reCAPTCHA */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Site Key",
                    propertyName: "reCAPTCHASiteKey",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Secret Key",
                    propertyName: "reCAPTCHASecretKey",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());

        {
            let recaptchaTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            recaptchaTitleElement.textContent = "Google reCAPTCHA";

            element.appendChild(recaptchaTitleElement);
        }

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* custom values */
        {
            if (args.spec.custom === undefined) {
                args.spec.custom = [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: ["CBKeyValuePair"],
                    specs: args.spec.custom,
                    specsChangedCallback: args.specChangedCallback,
                }
            );

            editor.title = "Custom Values";

            element.appendChild(editor.element);
            element.appendChild(CBUI.createHalfSpace());
        }

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function createEditor_handleImageChosen() {
            Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    imageChooser.src = CBImage.toURL(imageModel, "rw960");
                    args.spec.imageForIcon = imageModel;

                    args.specChangedCallback();

                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
        /* createEditor_handleImageChosen() */


        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            args.spec.imageForIcon = undefined;
            args.specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */

    }
    /* CBUISpecEditor_createEditorElement() */



    /* -- functions -- */



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function createGoogleAnalyticsIDEditorElement(
        spec,
        specChangedCallback
    ) {
        let googleAnalyticsIDEditor = CBUIStringEditor.create();

        googleAnalyticsIDEditor.title = (
            "Google Analytics ID | Google Tag Manager ID"
        );

        googleAnalyticsIDEditor.value = CBModel.valueToString(
            spec,
            "googleTagManagerID"
        );

        createGoogleAnalyticsIDEditorElement_validate();

        googleAnalyticsIDEditor.changed = function () {
            spec.googleTagManagerID = googleAnalyticsIDEditor.value;

            createGoogleAnalyticsIDEditorElement_validate();
            specChangedCallback();
        };

        return googleAnalyticsIDEditor.element;



        /**
         * @return undefined
         */
        function createGoogleAnalyticsIDEditorElement_validate() {
            let value = googleAnalyticsIDEditor.value.trim();

            if (
                /^(UA-|GTM-)/.test(value) ||
                value === ""
            ) {
                googleAnalyticsIDEditor.element.classList.remove(
                    "CBUIStringEditor_error"
                );
            } else {
                googleAnalyticsIDEditor.element.classList.add(
                    "CBUIStringEditor_error"
                );
            }
        }
        /* createGoogleAnalyticsIDEditorElement_validate() */

    }
    /* createGoogleAnalyticsIDEditorElement() */

})();
