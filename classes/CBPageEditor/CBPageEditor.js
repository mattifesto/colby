"use strict";

var CBPageEditor = {

    /**
     * @param function args.navigateCallback
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createPagePropertiesEditor : function (args) {
        var getSuggestedURICallback = CBPageEditor.pageSpecToSuggestedURI.bind(undefined, args.spec);

        var editorForURIPath = CBUISuggestedStringEditor.createEditor({
            getSuggestedStringCallback : getSuggestedURICallback,
            labelText : "URI",
            navigateCallback : args.navigateCallback,
            propertyName : "URIPath",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        });

        var titleChangedCallback = CBPageEditor.handleTitleChanged.bind(undefined, {
            specChangedCallback : args.specChangedCallback,
            suggestedURIChangedCallback : editorForURIPath.suggestedStringChangedCallback,
        });

        var publishedChangedCallback = CBPageEditor.handlePublishedChanged.bind(undefined, {
            getSuggestedURICallback : getSuggestedURICallback,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            updateURICallback : editorForURIPath.updateValueCallback,
        });

        var item;
        var section = CBUI.createSection();

        section.classList.add("CBPagePropertiesEditor");

        // title
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Title",
            propertyName : "title",
            spec : args.spec,
            specChangedCallback : titleChangedCallback,
        }).element);
        section.appendChild(item);

        // description
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Description",
            propertyName : "description",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        // URI
        item = CBUI.createSectionItem();
        item.appendChild(editorForURIPath.element);
        section.appendChild(item);

        // published
        item = CBUI.createSectionItem();
        item.appendChild(CBUIUnixTimestampEditor.createEditor({
            defaultValueText : "Not Published",
            labelText : "Published",
            navigateCallback : args.navigateCallback,
            propertyName : "published",
            spec : args.spec,
            specChangedCallback : publishedChangedCallback,
        }).element);
        section.appendChild(item);

        // preview
        item = CBUI.createSectionItem();
        item.classList.add("preview");
        var preview = document.createElement("a");
        preview.textContent = "preview";
        preview.href = "/admin/pages/preview/?ID=" + args.spec.ID;
        item.appendChild(preview);
        section.appendChild(item);

        return section;
    },

    /**
     * @param function args.getSuggestedURICallback
     * @param object spec
     * @param function specChangedCallback
     * @param function updateURICallback
     *
     * @return undefined
     */
    handlePublishedChanged : function (args) {
        if (args.spec.published !== undefined && args.spec.URIPath === undefined) {
            var URI = args.getSuggestedURICallback.call();
            args.updateURICallback.call(undefined, URI);
        }

        args.specChangedCallback.call();
    },

    /**
     * @param function args.specChangedCallback
     * @param function args.suggestedURIChangedCallback
     *
     * @return undefined
     */
    handleTitleChanged : function (args) {
        args.suggestedURIChangedCallback.call();
        args.specChangedCallback.call();
    },

    /**
     * @param object spec
     *
     * @return string
     */
    pageSpecToSuggestedURI : function (spec) {
        var URI = "";

        if (typeof spec.title === "string") {
            URI = Colby.textToURI(spec.title);
        }

        if (URI.length < 5) {
            URI = spec.ID;
        }

        return URI;
    },
};
