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
        var imageSpec = {
            URL : args.spec.imageURL,
        };
        var handleImageChanged = CBBackgroundViewEditor.handleImageChanged.bind(undefined, {
            handleSpecChanged : args.specChangedCallback,
            imageSpec : imageSpec,
            spec : args.spec,
        });

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Repeat Horizontally",
            propertyName : "imageShouldRepeatHorizontally",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Repeat Vertically",
            propertyName : "imageShouldRepeatVertically",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Minimum View Height is Image Height",
            propertyName : "minimumViewHeightIsImageHeight",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Background color",
            propertyName : "color",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));
        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.children,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBBackgroundViewAddableViews,
            navigateCallback : args.navigateCallback,
        }));

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({ text : "Background Image" }));
        element.appendChild(CBImageEditorFactory.createEditor({
            handleSpecChanged : handleImageChanged,
            spec : imageSpec,
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
