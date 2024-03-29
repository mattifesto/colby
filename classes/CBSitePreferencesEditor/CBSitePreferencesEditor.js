/* globals
    CBAjax,
    CBImage,
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIPanel,
    CBUISelector,
    CBUISpecArrayEditor,
    CBUIStringEditor2,

    CBSitePreferencesEditor_appearanceOptions,
    CBSitePreferencesEditor_environmentOptions,
*/


(function ()
{
    "use strict";



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
    function
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;
        let element;

        {
            let elements = CBUI.createElementTree(
                "CBSitePreferencesEditor",
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element = elements[0];

            let sectionElement = elements[2];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "siteName",
                    "Site Name",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "administratorEmails",
                    "Administrator Email Addresses",
                    specChangedCallback
                )
            );
        }


        /* -- website icon section -- -- -- -- -- */

        element.append(
            createWebsiteIconImageEditorElement(
                spec,
                specChangedCallback
            )
        );



        /* -- developer settings section -- -- -- -- -- */

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");

            element.appendChild(sectionTitleElement);

            sectionTitleElement.textContent = "Developer Settings";
        }

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];


            /* website appearance */
            {
                let selector = CBUISelector.create();
                selector.title = "Website Appearance";
                selector.value = spec.CBSitePreferences_appearance;
                selector.options = CBSitePreferencesEditor_appearanceOptions;

                sectionElement.append(
                    selector.element
                );

                selector.onchange = function () {
                    spec.CBSitePreferences_appearance = selector.value;
                    specChangedCallback();
                };
            }
            /* website appearance */


            /* website environment */
            {
                let selector = CBUISelector.create();
                selector.title = "Website Environment";
                selector.value = spec.CBSitePreferences_environment;
                selector.options = CBSitePreferencesEditor_environmentOptions;

                sectionElement.append(
                    selector.element
                );

                selector.onchange = function () {
                    spec.CBSitePreferences_environment = selector.value;
                    specChangedCallback();
                };
            }
            /* website environment */


            sectionElement.appendChild(
                CBUIBooleanEditor.create(
                    {
                        labelText: "Disallow Robots",
                        propertyName: "disallowRobots",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );

            sectionElement.appendChild(
                createGoogleAnalyticsIDEditorElement(
                    spec,
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "CBSitePreferences_adSensePublisherID",
                    "AdSense Publisher ID",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "onDemandImageResizeOperations",
                    "On Demand Image Resize Operations (deprecated)",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "path",
                    "Path",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "slackWebhookURL",
                    "Slack Webhook URL",
                    specChangedCallback
                )
            );
        }



        /* -- ads.txt -- */



        {
            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "ads.txt";

            element.appendChild(
                titleElement
            );

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "CBSitePreferences_adsTxtContent",
                    "Content",
                    specChangedCallback
                )
            );
        }



        /* -- social section -- -- -- -- -- */

        {
            let socialTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            socialTitleElement.textContent = "Social";

            element.appendChild(
                socialTitleElement
            );

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "CBSitePreferences_youtubeChannelID",
                    "YouTube Channel ID",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "CBSitePreferences_youtubeAPIKey",
                    "YouTube API Key",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "facebookURL",
                    "Facebook URL",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "twitterURL",
                    "Twitter URL",
                    specChangedCallback
                )
            );
        }


        /* Google reCAPTCHA */

        {
            let recaptchaTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            recaptchaTitleElement.textContent = "Google reCAPTCHA";

            element.appendChild(
                recaptchaTitleElement
            );

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "reCAPTCHASiteKey",
                    "Site Key",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "reCAPTCHASecretKey",
                    "Secret Key",
                    specChangedCallback
                )
            );
        }

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
    }
    /* CBUISpecEditor_createEditorElement() */



    // -- functions



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
        let IDEditor = CBUIStringEditor2.create();

        IDEditor.CBUIStringEditor2_setTitle(
            "Google Analytics ID | Google Tag Manager ID"
        );

        IDEditor.CBUIStringEditor2_setValue(
            CBModel.valueToString(
                spec,
                "googleTagManagerID"
            )
        );

        createGoogleAnalyticsIDEditorElement_validate();

        IDEditor.CBUIStringEditor2_setChangedEventListener(
            function () {
                spec.googleTagManagerID = IDEditor.CBUIStringEditor2_getValue();

                createGoogleAnalyticsIDEditorElement_validate();
                specChangedCallback();
            }
        );

        return IDEditor.CBUIStringEditor2_getElement();



        /**
         * @return undefined
         */
        function createGoogleAnalyticsIDEditorElement_validate() {
            let value = IDEditor.CBUIStringEditor2_getValue().trim();

            if (
                /^(UA-|GTM-)/.test(value) ||
                value === ""
            ) {
                IDEditor.CBUIStringEditor2_getElement().classList.remove(
                    "CBUIStringEditor_error"
                );
            } else {
                IDEditor.CBUIStringEditor2_getElement().classList.add(
                    "CBUIStringEditor_error"
                );
            }
        }
        /* createGoogleAnalyticsIDEditorElement_validate() */

    }
    /* createGoogleAnalyticsIDEditorElement() */



    /**
     * @param object sitePreferencesSpecArgument
     * @param function specChangedCallbackArgument
     *
     * @return Element
     */
    function
    createWebsiteIconImageEditorElement(
        sitePreferencesSpecArgument,
        specChangedCallbackArgument
    ) {
        let headerImageEditorElement =
        document.createElement(
            "div"
        );

        headerImageEditorElement.className =
        "CBSitePreferencesEditor_headerImageEditor_element";

        let imageChooser;

        let sectionTitleElement =
        CBUI.createElement(
            "CBUI_title1"
        );

        headerImageEditorElement.append(
            sectionTitleElement
        );

        sectionTitleElement.textContent =
        "Website Icon";

        let elements =
        CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        headerImageEditorElement.append(
            elements[0]
        );

        let sectionElement =
        elements[1];

        imageChooser =
        CBUIImageChooser.create();

        imageChooser.chosen =
        handleImageChosen;

        imageChooser.removed =
        handleImageRemoved;

        imageChooser.src =
        CBImage.toURL(
            sitePreferencesSpecArgument.imageForIcon,
            "rw960"
        );

        sectionElement.appendChild(
            imageChooser.element
        );



        /**
         * @return undefined
         */
        async function
        handleImageChosen(
        ) // -> Promise -> undefined
        {
            try
            {
                let imageModel =
                await CBAjax.call(
                    "CBImages",
                    "upload",
                    {},
                    imageChooser.file
                );

                imageChooser.src =
                CBImage.toURL(
                    imageModel,
                    "rw960"
                );

                sitePreferencesSpecArgument.imageForIcon =
                imageModel;

                specChangedCallbackArgument();
            }
            // try

            catch(
                error
            ) {
                CBUIPanel.displayAndReportError(
                    error
                );
            }
            // catch
        }
        /* createEditor_handleImageChosen() */



        /**
         * @return undefined
         */
        function
        handleImageRemoved(
        ) // -> undefined
        {
            sitePreferencesSpecArgument.imageForIcon =
            undefined;

            specChangedCallbackArgument();
        }
        /* createEditor_handleImageRemoved() */



        return headerImageEditorElement;
    }
    // createWebsiteIconImageEditorElement()



}
)();
