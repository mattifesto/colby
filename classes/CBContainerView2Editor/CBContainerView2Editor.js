"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
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

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({
            paragraphs: [
                `
                Supported Class Names:
                `,`
                flow: Flow subviews from left to right and wrap into new lines.
                Center each line of children. Example scenario: displaying a
                collection of images.
                `,`
                hero1: Present the view as a full window view. The minimum
                height is the window height. The background image will always
                cover the entire view which will hide some of the edges of the
                image depending on the shape of the window.
                `
           ],
        }));

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "CSS Class Names",
            propertyName : "CSSClassNames",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* localCSSTemplate */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Local CSS Template",
            propertyName : "localCSSTemplate",
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
