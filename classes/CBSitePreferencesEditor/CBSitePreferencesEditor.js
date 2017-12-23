"use strict";
/* jshint strict: global */
/* exported CBSitePreferencesEditor */
/* globals
    CBArrayEditor,
    CBUI,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIStringEditor,
    Colby */

var CBSitePreferencesEditor = {

    /**
     * @param function args.navigateToItemCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBSitePreferencesEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Site Name",
            propertyName : "siteName",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Administrator Email Addresses",
            propertyName : "administratorEmails",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* imageForIcon */

        section = CBUI.createSection();

        var chooser = CBUIImageChooser.createFullSizedChooser({
            imageChosenCallback : function (chooserArgs) {
                var ajaxURI = "/api/?class=CBImages&function=upload";
                var formData = new FormData();
                formData.append("image", chooserArgs.file);

                CBSitePreferencesEditor.promise = Colby.fetchAjaxResponse(ajaxURI, formData)
                    .then(handleImageUploaded);

                function handleImageUploaded(response) {
                    args.spec.imageForIcon = response.image;
                    args.specChangedCallback();
                    chooserArgs.setImageURLCallback(Colby.imageToURL(response.image, "rw960"));
                }
            },
            imageRemovedCallback : function () {
                args.spec.imageForIcon = undefined;
                args.specChangedCallback();
            },
        });

        chooser.setImageURLCallback(Colby.imageToURL(args.spec.imageForIcon, "rw960"));

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(section);

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Debug",
            propertyName : "debug",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Disallow Robots",
            propertyName : "disallowRobots",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Google Tag Manager ID",
            propertyName : "googleTagManagerID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Default Class Name for Page Settings (deprecated)",
            propertyName : "defaultClassNameForPageSettings",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "On Demand Image Resize Operations (deprecated)",
            propertyName : "onDemandImageResizeOperations",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(section);

        /* Slack */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Webhook URL",
            propertyName : "slackWebhookURL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Slack",
        }));
        element.appendChild(section);

        /* Social */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Facebook URL",
            propertyName : "facebookURL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Twitter URL",
            propertyName : "twitterURL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Social",
        }));
        element.appendChild(section);

        /* Google reCAPTCHA */

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Site Key",
            propertyName : "reCAPTCHASiteKey",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Secret Key",
            propertyName : "reCAPTCHASecretKey",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Google reCAPTCHA",
        }));
        element.appendChild(section);

        /* custom values */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Custom",
        }));

        if (args.spec.custom === undefined) { args.spec.custom = []; }

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.custom,
            arrayChangedCallback : args.specChangedCallback,
            classNames : ['CBKeyValuePair'],
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
};
