"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBSitePreferencesEditor */
/* globals
    CBErrorHandler,
    CBImage,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby,
*/

var CBSitePreferencesEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBSitePreferencesEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Site Name",
            propertyName: "siteName",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Administrator Email Addresses",
            propertyName: "administratorEmails",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
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
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Debug",
            propertyName: "debug",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Disallow Robots",
            propertyName: "disallowRobots",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Google Tag Manager ID",
            propertyName: "googleTagManagerID",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Default Class Name for Page Settings (deprecated)",
            propertyName: "defaultClassNameForPageSettings",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "On Demand Image Resize Operations (deprecated)",
            propertyName: "onDemandImageResizeOperations",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
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
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Facebook URL",
            propertyName: "facebookURL",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Twitter URL",
            propertyName: "twitterURL",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text: "Social",
        }));
        element.appendChild(section);

        /* Google reCAPTCHA */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Site Key",
            propertyName: "reCAPTCHASiteKey",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Secret Key",
            propertyName: "reCAPTCHASecretKey",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text: "Google reCAPTCHA",
        }));
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* custom values */
        {
            if (args.spec.custom === undefined) {
                args.spec.custom = [];
            }

            let editor = CBUISpecArrayEditor.create({
                addableClassNames: ["CBKeyValuePair"],
                specs: args.spec.custom,
                specsChangedCallback: args.specChangedCallback,
            });

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
                    CBErrorHandler.displayAndReport(error);
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
    },
    /* createEditor() */
};
/* CBSitePreferencesEditor */
