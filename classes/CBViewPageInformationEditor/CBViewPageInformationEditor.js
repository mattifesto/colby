"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageInformationEditor */
/* globals
    CBImage,
    CBModel,
    CBUI,
    CBUIActionLink,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISectionItem4,
    CBUISelector,
    CBUISpecPropertyEditor,
    CBUIStringEditor,
    CBUIStringsPart,
    CBUIUnixTimestampEditor,
    Colby,

    CBViewPageEditor,

    CBPageClassNamesForLayouts,
    CBUsersWhoAreAdministrators,
    CBViewPageInformationEditor_currentUserNumericID,
    CBViewPageInformationEditor_frameClassNames,
    CBViewPageInformationEditor_kindClassNames,
    CBViewPageInformationEditor_pagesAdminURL,
    CBViewPageInformationEditor_settingsClassNames,
*/

var CBViewPageInformationEditor = {

    /**
     * @param object args
     *
     *      {
     *          handleTitleChanged: function
     *          makeFrontPageCallback: function
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = CBUI.createElement("CBViewPageInformationEditor");

        section = CBUI.createSection();

        /* title */

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Title",
                    propertyName: 'title',
                    spec: args.spec,
                    specChangedCallback: function () {
                        args.handleTitleChanged();
                        args.specChangedCallback();
                    },
                }
            ).element
        );

        section.appendChild(item);

        /* description */

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Description",
                    propertyName: 'description',
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);

        /* editors */

        var URIEditor = CBUIStringEditor.createEditor(
            {
                labelText: "URI",
                propertyName: "URI",
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        var publicationDateEditor = CBUIUnixTimestampEditor.create(
            {
                labelText: "Publication Date",
                propertyName: "publicationTimeStamp",
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }
        );

        /* isPublished */

        item = CBUI.createSectionItem();
        item.appendChild(
            CBUIBooleanEditor.create(
                {
                    labelText: "Published",
                    propertyName: "isPublished",
                    spec: args.spec,
                    specChangedCallback: function () {
                        var URI = CBModel.valueToString(
                            args.spec,
                            "URI"
                        ).trim();

                        if (args.spec.isPublished) {
                            if (URI === "") {
                                URIEditor.updateValueCallback(
                                    Colby.textToURI(args.spec.title)
                                );
                            }

                            if (args.spec.publicationTimeStamp === undefined) {
                                args.spec.publicationTimeStamp = (
                                    Math.floor(Date.now() / 1000)
                                );

                                publicationDateEditor.refresh();
                            }
                        }

                        args.specChangedCallback();
                    },
                }
            ).element
        );

        section.appendChild(item);

        /* publicationTimeStamp */

        item = CBUI.createSectionItem();
        item.appendChild(publicationDateEditor.element);
        section.appendChild(item);

        /* URI */

        item = CBUI.createSectionItem();
        item.appendChild(URIEditor.element);
        section.appendChild(item);

        /* publishedBy */

        if (!args.spec.publishedBy) {
            args.spec.publishedBy =
            CBViewPageInformationEditor_currentUserNumericID;
        }

        var users = CBUsersWhoAreAdministrators.map(
            function (user) {
                return {
                    title: user.name,
                    value: user.ID,
                };
            }
        );

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUISelector.create(
                {
                    labelText: "Published By",
                    navigateToItemCallback: args.navigateToItemCallback,
                    propertyName: "publishedBy",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                    options: users,
                }
            ).element
        );

        section.appendChild(item);

        /* classNameForSettings */

        {
            let selector = CBUISelector.create();
            selector.title = "Page Settings";
            selector.value = args.spec.classNameForSettings;
            selector.onchange = function () {
                args.spec.classNameForSettings = selector.value;
                args.specChangedCallback();
            };

            let options = [
                {
                    title: "None",
                    value: undefined,
                },
            ];

            CBViewPageInformationEditor_settingsClassNames.forEach(
                function (settingsClassName) {
                    options.push(
                        {
                            title: settingsClassName,
                            value: settingsClassName,
                        }
                    );
                }
            );

            selector.options = options;

            section.appendChild(selector.element);
        }

        /* classNameForKind */

        {
            let selector = CBUISelector.create();
            selector.title = "Page Kind";
            selector.value = args.spec.classNameForKind;
            selector.onchange = function () {
                args.spec.classNameForKind = selector.value;
                args.specChangedCallback();
            };

            let options = [
                {
                    title: "None",
                    value: undefined,
                },
            ];

            CBViewPageInformationEditor_kindClassNames.forEach(
                function (kindClassName) {
                    options.push({
                        title: kindClassName,
                        value: kindClassName,
                    });
                }
            );

            selector.options = options;

            section.appendChild(selector.element);
        }

        /* frameClassName */

        {
            let selector = CBUISelector.create();
            selector.title = "Page Frame";
            selector.value = args.spec.frameClassName;
            selector.onchange = function () {
                args.spec.frameClassName = selector.value;
                args.specChangedCallback();
            };

            let options = [
                {
                    title: "None",
                    value: undefined,
                },
            ];

            CBViewPageInformationEditor_frameClassNames.forEach(
                function (frameClassName) {
                    options.push(
                        {
                            title: frameClassName,
                            value: frameClassName,
                        }
                    );
                }
            );

            selector.options = options;

            section.appendChild(selector.element);
        }

        { /* selectedMainMenuItemName */
            let sectionItemElement = CBUI.createSectionItem();
            let stringEditor = CBUIStringEditor.create();
            stringEditor.title = "Selected Main Menu Item Name";

            if (args.spec.selectedMainMenuItemName !== undefined) {
                stringEditor.value = args.spec.selectedMainMenuItemName;
            }

            stringEditor.changed = function () {
                args.spec.selectedMainMenuItemName = stringEditor.value;
                args.specChangedCallback();
            };

            sectionItemElement.appendChild(stringEditor.element);
            section.appendChild(sectionItemElement);
        }

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        /* thumbnail */

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");
            sectionTitleElement.textContent = "Page Thumbnail Image";

            element.appendChild(sectionTitleElement);
        }

        section = CBUI.createSection();

        var thumbnailChosenCallback =
        CBViewPageInformationEditor.handleThumbnailChosen;

        var thumbnailRemovedCallback =
        CBViewPageInformationEditor.handleThumbnailRemoved;

        var chooser = CBUIImageChooser.createThumbnailSizedChooser(
            {
                imageChosenCallback: thumbnailChosenCallback,
                imageRemovedCallback: thumbnailRemovedCallback,
            }
        );

        CBViewPageEditor.thumbnailChangedCallback =
        CBViewPageInformationEditor.handleThumbnailChanged.bind(
            undefined,
            {
                setImageURLCallback: chooser.setImageURLCallback,
            }
        );

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(section);

        var imageURL = args.spec.thumbnailURL; /* deprecated */

        if (args.spec.image) {
            imageURL = CBImage.toURL(
                args.spec.image,
                "rw320"
            );
        }

        chooser.setImageURLCallback(imageURL);

        /* actions */

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");
            sectionTitleElement.textContent = "Actions";

            element.appendChild(sectionTitleElement);
        }

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIActionLink.create(
                {
                    labelText: "Preview",
                    callback: function () {
                        var name = "preview_" + args.spec.ID;
                        window.open(
                            "/admin/pages/preview/?ID=" +
                            args.spec.ID,
                            name
                        );
                    },
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIActionLink.create(
                {
                    labelText: "Use as Front Page",
                    callback: args.makeFrontPageCallback,
                }
            ).element
        );

        section.appendChild(item);


        /* copy */
        {
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                let newID = Colby.random160();

                let URI =
                `/admin/?c=CBModelEditor&ID=${newID}&copyID=${args.spec.ID}`;

                window.location = URI;
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Copy";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }
        /* copy */


        /* move to trash */
        {
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                if (
                    window.confirm(
                        "Are you sure you want to move this page to the trash?"
                    )
                ) {
                    Colby.callAjaxFunction(
                        "CBPages",
                        "moveToTrash",
                        {
                            ID: args.spec.ID
                        }
                    ).then(
                        function () {
                            window.alert(
                                "The page is the trash. You will be " +
                                "redirected to pages administration."
                            );

                            window.location =
                            CBViewPageInformationEditor_pagesAdminURL;
                        }
                    ).catch(
                        function (error) {
                            Colby.displayAndReportError(error);
                        }
                    );
                }
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Move to Trash";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }
        /* move to trash */


        element.appendChild(section);

        /* layout */

        if (args.spec.layout) {
            element.appendChild(CBUI.createHalfSpace());

            var options = CBPageClassNamesForLayouts.map(
                function (className) {
                    return {
                        title: className,
                        value: className
                    };
                }
            );

            options.unshift(
                {
                    title: "None",
                    value: undefined
                }
            );

            var editor = CBUISpecPropertyEditor.create(
                {
                    labelText: "Layout",
                    navigateToItemCallback: args.navigateToItemCallback,
                    options: options,
                    propertyName: "layout",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            );

            element.appendChild(editor.element);
        }

        return element;
    },
    /* createEditor() */


    /**
     * All this function does is set the uploader thumbnail image. It may be
     * able to be replaced with a bound setImageURL callback. I think it's only
     * called by this file. Also we probably don't have to handle the
     * thumbnailURL case anymore.
     *
     * @param function args.setImageURLCallback
     * @param object pageArgs.spec
     * @param object pageArgs.image
     */
    handleThumbnailChanged: function (args, pageArgs) {
        if (pageArgs.image) {
            args.setImageURLCallback(
                CBImage.toURL(pageArgs.image, "rw320")
            );
        } else if (pageArgs.spec.thumbnailURL) {
            args.setImageURLCallback(pageArgs.spec.thumbnailURL);
        } else {
            args.setImageURLCallback();
        }
    },
    /* handleThumbnailChanged() */


    /**
     * @param file chooserArgs.file
     * @param function chooserArgs.setImageURLCallback
     *
     * @return undefined
     */
    handleThumbnailChosen: function (chooserArgs) {
        Colby.callAjaxFunction(
            "CBImages",
            "upload",
            {},
            chooserArgs.file
        ).then(
            function (imageModel) {
                CBViewPageEditor.setThumbnailImage(imageModel);
            }
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);
            }
        );
    },
    /* handleThumbnailChosen() */


    /**
     * @return undefined
     */
    handleThumbnailRemoved: function () {
        CBViewPageEditor.setThumbnailImage();
    },
};
/* CBViewPageInformationEditor */
