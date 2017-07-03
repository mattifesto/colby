"use strict"; /* jshint strict: global */
/* globals
    CBUI,
    CBUIImageChooser,
    CBUISelector,
    CBUIStringEditor,
    Colby */

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

        section = CBUI.createSection();

        var chooser = CBUIImageChooser.createFullSizedChooser({
            imageChosenCallback : handleImageChosen,
            imageRemovedCallback : handleImageRemoved,
        });

        chooser.setImageURLCallback(Colby.imageToURL(args.spec.image, "rw960"));

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Description",
            propertyName : "description",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "URL",
            propertyName : "URL",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Size",
            navigateCallback: args.navigateCallback,
            navigateToItemCallback: args.navigateToItemCallback,
            options: [
                {title: "Small", value: "small"},
                {title: "Medium", value: undefined},
                {title: "Large", value: "large"},
            ],
            propertyName : "size",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;

        /**
         *
         */
        function handleImageChosen(chooserArgs) {
            var ajaxURI = "/api/?class=CBImages&function=upload";
            var formData = new FormData();
            formData.append("image", chooserArgs.file);

            CBLinkView1Editor.promise = Colby.fetchAjaxResponse(ajaxURI, formData)
                .then(handleImageUploaded);

            function handleImageUploaded(response) {
                args.spec.image = response.image;
                args.specChangedCallback();
                chooserArgs.setImageURLCallback(Colby.imageToURL(args.spec.image, "rw960"));
            }
        }

        /**
         *
         */
        function handleImageRemoved(chooserArgs) {
            args.spec.image = undefined;
            args.specChangedCallback();
        }
    },
};
