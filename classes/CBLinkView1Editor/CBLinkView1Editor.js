"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLinkView1Editor */
/* globals
    CBImage,
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor,
    Colby,
*/

var CBLinkView1Editor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBLinkView1Editor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        var chooser = CBUIImageChooser.createFullSizedChooser(
            {
                imageChosenCallback: createEditor_handleImageChosen,
                imageRemovedCallback: createEditor_handleImageRemoved,
            }
        );

        chooser.setImageURLCallback(
            CBImage.toURL(args.spec.image, "rw960")
        );

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Title",
            propertyName: "title",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Description",
            propertyName: "description",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "URL",
            propertyName: "URL",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Size",
            navigateToItemCallback: args.navigateToItemCallback,
            options: [
                {title: "Small", value: "small"},
                {title: "Medium", value: undefined},
                {title: "Large", value: "large"},
            ],
            propertyName: "size",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
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
        function createEditor_handleImageChosen(chooserArgs) {
            var ajaxURI = "/api/?class=CBImages&function=upload";
            var formData = new FormData();
            formData.append("image", chooserArgs.file);

            CBLinkView1Editor.promise = Colby.fetchAjaxResponse(
                ajaxURI,
                formData
            ).then(
                function (response) {
                    args.spec.image = response.image;
                    args.specChangedCallback();
                    chooserArgs.setImageURLCallback(
                        CBImage.toURL(args.spec.image, "rw960")
                    );
                }
            );
        }
        /* createEditor_handleImageChosen() */


        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved(chooserArgs) {
            args.spec.image = undefined;
            args.specChangedCallback();
        }
        /* createEditor_handleImageRemoved() */
    },
    /* createEditor() */
};
/* CBLinkView1Editor */
