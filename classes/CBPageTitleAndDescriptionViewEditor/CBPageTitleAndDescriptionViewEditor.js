"use strict";

var CBPageTitleAndDescriptionViewEditor = {

    /**
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPageTitleAndDescriptionViewEditor";

        section = CBUI.createSection();

        /* themeID */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIThemeSelector.create({
            classNameForKind : "CBPageTitleAndDescriptionView",
            labelText : "Theme",
            navigateCallback : args.navigateCallback,
            propertyName : "themeID",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* hideDescription */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Hide Description",
            propertyName : "hideDescription",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* showPublicationDate */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Show Publication Date",
            propertyName : "showPublicationDate",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },
};
