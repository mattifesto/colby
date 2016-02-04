"use strict";

var CBBackgroundViewEditor = {

    /**
     * @param function args.navigateCallback
     * @param Object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        CBBackgroundViewEditor.prepareSpec(args.spec);

        var section, item;
        var element = document.createElement("div");
        element.className = "CBBackgroundViewEditor";
        var properties = document.createElement("div");
        properties.className = "properties";
        var imageSpec = {
            URL : args.spec.imageURL,
        };
        var handleImageChanged = CBBackgroundViewEditor.handleImageChanged.bind(undefined, {
            handleSpecChanged : args.specChangedCallback,
            imageSpec : imageSpec,
            spec : args.spec,
        });

        var options1            = document.createElement("div");
        options1.className      = "options options1";

        options1.appendChild(CBImageEditorFactory.createEditor({
            handleSpecChanged   : handleImageChanged,
            spec                : imageSpec }));

        properties.appendChild(options1);
        element.appendChild(properties);

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Repeat Horizontally",
            propertyName        : "imageShouldRepeatHorizontally",
            spec                : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Repeat Vertically",
            propertyName        : "imageShouldRepeatVertically",
            spec                : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : "Minimum view height is image height",
            propertyName        : "minimumViewHeightIsImageHeight",
            spec                : args.spec,
        }));
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Background color",
            propertyName : "color",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));
        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.children,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBBackgroundViewAddableViews,
            navigateCallback : args.navigateCallback,
        }));

        return element;
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      imageSpec
     * @param {Object}      spec
     *
     * @return {undefined}
     */
    handleImageChanged : function(args) {
        args.spec.imageHeight   = args.imageSpec.height;
        args.spec.imageWidth    = args.imageSpec.width;
        args.spec.imageURL      = args.imageSpec.URL;

        args.handleSpecChanged.call();
    },

    /**
     * @return undefined
     */
    prepareSpec : function(spec) {
        if (!spec.children) {
            spec.children = [];
        }

        if (spec.minimumViewHeightIsImageHeight === undefined) {
            spec.minimumViewHeightIsImageHeight = true;
        }
    },

    /**
     * @param object spec
     * @param array? spec.children
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        var description;
        var children = spec.children;

        if (Array.isArray(children)) {
            for (var i = 0; i < children.length && description === undefined; i++) {
                description = CBArrayEditor.specToDescription(children[i]);
            }
        }

        return description;
    },
};
