"use strict"; /* jshint strict: global */
/* globals
    CBArrayEditor,
    CBContainerView2EditorAddableViews,
    CBUI,
    CBUIImageChooser,
    CBUIStringEditor,
    Colby */

var CBContainerView2Editor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBContainerView2Editor";

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
            labelText : "CSS Class Names",
            propertyName : "CSSClassNames",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* subviews */
        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({ text : "Subviews" }));

        if (args.spec.subviews === undefined) { args.spec.subviews = []; }

        element.appendChild(CBArrayEditor.createEditor({
            array : args.spec.subviews,
            arrayChangedCallback : args.specChangedCallback,
            classNames : CBContainerView2EditorAddableViews,
            navigateCallback : args.navigateCallback,
            navigateToItemCallback : args.navigateToItemCallback,
        }));

        element.appendChild(CBUI.createHalfSpace());

        return element;

        /**
         *
         */
        function handleImageChosen(chooserArgs) {
            var ajaxURI = "/api/?class=CBImages&function=upload";
            var formData = new FormData();
            formData.append("image", chooserArgs.file);

            CBContainerView2Editor.promise = Colby.fetchAjaxResponse(ajaxURI, formData)
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
