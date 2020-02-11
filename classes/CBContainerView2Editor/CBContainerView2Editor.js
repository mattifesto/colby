"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBContainerView2Editor */
/* globals
    CBErrorHandler,
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISpec,
    CBUISpecArrayEditor,
    CBUIStringEditor,
    Colby,

    CBContainerView2Editor_addableClassNames,
*/



var CBContainerView2Editor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement(
        args
    ) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBContainerView2Editor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        let imageChooser = CBUIImageChooser.create();
        imageChooser.src = CBImage.toURL(args.spec.image, "rw960");

        imageChooser.chosen = function (chooserArgs) {
            CBContainerView2Editor.promise = Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                chooserArgs.file
            ).then(
                function (imageModel) {
                    args.spec.image = imageModel;
                    imageChooser.src = CBImage.toURL(imageModel, "rw960");

                    args.specChangedCallback();
                }
            ).catch(
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        };

        imageChooser.removed = function () {
            args.spec.image = undefined;

            args.specChangedCallback();
        };

        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Title",
                    propertyName: "title",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* subviews */
        {
            if (args.spec.subviews === undefined) {
                args.spec.subviews = [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: CBContainerView2Editor_addableClassNames,
                    specs: args.spec.subviews,
                    specsChangedCallback: args.specChangedCallback,
                }
            );

            editor.title = "Views";

            element.appendChild(editor.element);
            element.appendChild(CBUI.createHalfSpace());
        }

        /* CSSClassNames */

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `
                        Supported Class Names:
                        `,`
                        flow: Flow subviews from left to right and wrap into new
                        lines. Center each line of children. Example scenario:
                        displaying a collection of images.
                        `,`
                        hero1: Present the view as a full window view. The
                        minimum height is the window height. The background
                        image will always cover the entire view which will hide
                        some of the edges of the image depending on the shape of
                        the window.
                        `
                   ],
                }
            )
        );

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "CSS Class Names",
                    propertyName: "CSSClassNames",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        element.appendChild(section);

        /* localCSSTemplate */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Local CSS Template",
                    propertyName: "localCSSTemplate",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let title = CBModel.valueToString(spec, "title").trim();

        if (title !== "") {
            return title;
        } else {
            if (Array.isArray(spec.subviews)) {
                for (let i = 0; i < spec.subviews.length; i++) {
                    let description = CBUISpec.specToDescription(
                        spec.subviews[i]
                    );

                    if (description) {
                        return description;
                    }
                }
            }
        }
    },
    /* CBUISpec_toDescription() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toThumbnailURL: function (spec) {
        if (spec.image) {
            return CBImage.toURL(
                spec.image,
                'rw320'
            );
        } else {
            if (Array.isArray(spec.subviews)) {
                for (let i = 0; i < spec.subviews.length; i++) {
                    let thumbnailURI = CBUISpec.specToThumbnailURL(
                        spec.subviews[i]
                    );

                    if (thumbnailURI) {
                        return thumbnailURI;
                    }
                }
            }
        }
    },
    /* CBUISpec_toThumbnailURL() */

};
/* CBContainerView2Editor */
