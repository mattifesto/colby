"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLinkView1Editor */
/* globals
    CBException,
    CBImage,
    CBModel,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor,
    Colby,
*/



var CBLinkView1Editor = {

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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = CBModel.valueAsModel(
            args,
            "spec"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The spec parameter is not a valid model."
                ),
                args,
                "72f17a59d7931caff1744c8f09caa1029926fd76"
            );
        }

        let specChangedCallback = CBModel.valueAsFunction(
            args,
            "specChangedCallback"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The specChangedCallback parameter is not a valid function."
                ),
                args,
                "6250bd96166fa932a06a710932b99cfbe6e188e6"
            );
        }

        var section, item;
        let element = CBUI.createElement("CBLinkView1Editor");

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        let imageChooser = CBUIImageChooser.create();
        imageChooser.chosen = createEditor_handleImageChosen;
        imageChooser.removed = createEditor_handleImageRemoved;

        imageChooser.src = CBImage.toURL(spec.image, "rw960");

        item = CBUI.createSectionItem();
        item.appendChild(imageChooser.element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Title",
            propertyName: "title",
            spec: spec,
            specChangedCallback: specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Description",
            propertyName: "description",
            spec: spec,
            specChangedCallback: specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "URL",
            propertyName: "URL",
            spec: spec,
            specChangedCallback: specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Size",
            options: [
                {title: "Small", value: "small"},
                {title: "Medium", value: undefined},
                {title: "Large", value: "large"},
            ],
            propertyName: "size",
            spec: spec,
            specChangedCallback: specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @param object chooserArgs
         *
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
                    spec.image = imageModel;

                    specChangedCallback();

                    imageChooser.src = CBImage.toURL(imageModel, "rw960");
                }
            );
        }
        /* createEditor_handleImageChosen() */


        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            spec.image = undefined;
            specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */
    },
    /* createEditor() */
};
/* CBLinkView1Editor */
