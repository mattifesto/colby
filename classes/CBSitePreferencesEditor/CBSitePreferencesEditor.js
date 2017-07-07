"use strict"; /* jshint strict: global */
/* globals
    CBArrayEditor,
    CBBooleanEditorFactory,
    CBUI,
    CBUIActionLink,
    CBUIImageChooser,
    CBUIStringEditor,
    Colby */

var CBSitePreferencesEditor = {

    /**
     * @param function args.navigateCallback
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

        element.appendChild(CBUI.createHalfSpace());
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

        element.appendChild(section);

        /* error tests */

        element.appendChild(CBUI.createHalfSpace());
        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : function () {
                CBSitePreferencesEditor.promise = Colby.fetchAjaxResponse("/api/?class=CBSitePreferencesEditor&function=errorTest");
            },
            labelText : "PHP Error Test",
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            callback : function () {
                throw new Error("Sample JavaScript Error");
            },
            labelText : "JavaScript Error Test",
        }).element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged : args.specChangedCallback,
            labelText : "Debug",
            propertyName : "debug",
            spec : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged : args.specChangedCallback,
            labelText : "Disallow Robots",
            propertyName : "disallowRobots",
            spec : args.spec,
        }));
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
            labelText : "Default Class Name for Page Settings",
            propertyName : "defaultClassNameForPageSettings",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "On Demand Image Resize Operations",
            propertyName : "onDemandImageResizeOperations",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* Social */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Social",
        }));

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

        element.appendChild(section);

        /* Google reCAPTCHA */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Google reCAPTCHA",
        }));

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
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        return element;
    },
};
