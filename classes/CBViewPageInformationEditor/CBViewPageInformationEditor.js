"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageInformationEditor */
/* globals
    CBCurrentUserID,
    CBPageClassNamesForLayouts,
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
    CBUsersWhoAreAdministrators,
    CBViewPageEditor,
    CBViewPageInformationEditor_frameClassNames,
    CBViewPageInformationEditor_kindClassNames,
    CBViewPageInformationEditor_mainMenuItemOptions,
    CBViewPageInformationEditor_settingsClassNames,
    Colby,
*/

var CBViewPageInformationEditor = {

    /**
     * @param object args
     *
     *      {
     *          checkbox: Element
     *          listClassName: string
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return undefined
     */
    checkboxDidChangeForListClassName: function (args) {
        var checkbox        = args.checkbox;
        var listClassName   = args.listClassName;

        if (!args.spec.listClassNames) {
            args.spec.listClassNames = [];
        }

        var index = args.spec.listClassNames.indexOf(listClassName);

        if (checkbox.checked) {
            if (index < 0) {
                args.spec.listClassNames.push(listClassName);
            }
        } else {
            if (index >= 0) {
                args.spec.listClassNames.splice(index, 1);
            }
        }

        args.specChangedCallback.call();
    },

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
        var element = document.createElement("section");
        element.className = "CBViewPageInformationEditor";

        section = CBUI.createSection();

        /* title */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Title",
            propertyName: 'title',
            spec: args.spec,
            specChangedCallback: function () {
                args.handleTitleChanged();
                args.specChangedCallback();
            },
        }).element);
        section.appendChild(item);

        /* description */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Description",
            propertyName : 'description',
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* editors */

        var URIEditor = CBUIStringEditor.createEditor({
            labelText: "URI",
            propertyName: "URI",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        var publicationDateEditor = CBUIUnixTimestampEditor.create({
            labelText: "Publication Date",
            propertyName: "publicationTimeStamp",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        /* isPublished */

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Published",
            propertyName: "isPublished",
            spec: args.spec,
            specChangedCallback: function () {
                var URI = args.spec.URI ? args.spec.URI.trim() : "";

                if (args.spec.isPublished) {
                    if (URI === "") {
                        URIEditor.updateValueCallback(Colby.textToURI(args.spec.title));
                    }

                    if (args.spec.publicationTimeStamp === undefined) {
                        args.spec.publicationTimeStamp = Math.floor(Date.now() / 1000);

                        publicationDateEditor.refresh();
                    }
                }

                args.specChangedCallback();
            },
        }).element);
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
            args.spec.publishedBy = CBCurrentUserID;
        }

        var users = CBUsersWhoAreAdministrators.map(function (user) {
            return { title: user.name, value: user.ID };
        });

        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText : "Published By",
            navigateToItemCallback : args.navigateToItemCallback,
            propertyName : "publishedBy",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            options : users,
        }).element);
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

            CBViewPageInformationEditor_settingsClassNames.forEach(function (settingsClassName) {
                options.push({
                    title: settingsClassName,
                    value: settingsClassName,
                });
            });

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

            CBViewPageInformationEditor_kindClassNames.forEach(function (kindClassName) {
                options.push({
                    title: kindClassName,
                    value: kindClassName,
                });
            });

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

            CBViewPageInformationEditor_frameClassNames.forEach(function (frameClassName) {
                options.push({
                    title: frameClassName,
                    value: frameClassName,
                });
            });

            selector.options = options;

            section.appendChild(selector.element);
        }

        /* selectedMainMenuItemName */
        item = CBUI.createSectionItem();
        item.appendChild(CBUISelector.create({
            labelText: "Selected Main Menu Item",
            navigateToItemCallback: args.navigateToItemCallback,
            options: CBViewPageInformationEditor_mainMenuItemOptions,
            propertyName: "selectedMainMenuItemName",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        /* thumbnail */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({text: "Page Thumbnail Image"}));

        section = CBUI.createSection();

        var thumbnailChosenCallback = CBViewPageInformationEditor.handleThumbnailChosen;
        var thumbnailRemovedCallback = CBViewPageInformationEditor.handleThumbnailRemoved;
        var chooser = CBUIImageChooser.createThumbnailSizedChooser({
            imageChosenCallback : thumbnailChosenCallback,
            imageRemovedCallback : thumbnailRemovedCallback,
        });

        CBViewPageEditor.thumbnailChangedCallback = CBViewPageInformationEditor.handleThumbnailChanged.bind(undefined, {
            setImageURLCallback : chooser.setImageURLCallback,
        });

        item = CBUI.createSectionItem();
        item.appendChild(chooser.element);
        section.appendChild(item);

        element.appendChild(section);

        var imageURL = args.spec.thumbnailURL; /* deprecated */

        if (args.spec.image) {
            imageURL = Colby.imageToURL(args.spec.image, "rw320");
        }

        chooser.setImageURLCallback(imageURL);

        /* actions */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({text: "Actions"}));

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            labelText: "Preview",
            callback: function () {
                var name = "preview_" + args.spec.ID;
                window.open("/admin/pages/preview/?ID=" + args.spec.ID, name);
            },
        }).element);
        section.appendChild(item);

        item = CBUI.createSectionItem();
        item.appendChild(CBUIActionLink.create({
            labelText: "Use as Front Page",
            callback: args.makeFrontPageCallback,
        }).element);
        section.appendChild(item);

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                let newID = Colby.random160();
                let URI = `/admin/?c=CBModelEditor&ID=${newID}&copyID=${args.spec.ID}`;
                window.location = URI;
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Copy";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                if (window.confirm('Are you sure you want to move this page to the trash?')) {
                    Colby.callAjaxFunction("CBPages", "moveToTrash", { ID: args.spec.ID })
                        .then(onFulfilled)
                        .catch(Colby.displayAndReportError);
                }

                function onFulfilled() {
                    alert('The page is the trash. You will be redirected to pages administration.');
                    window.location = "/admin/page/?class=CBAdminPageForPagesFind";
                }
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Move to Trash";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }

        element.appendChild(section);

        /* layout */

        if (args.spec.layout) {
            element.appendChild(CBUI.createHalfSpace());

            var options = CBPageClassNamesForLayouts.map(function(className) {
                return { title : className, value : className };
            });

            options.unshift({ title : "None", value : undefined });

            var editor = CBUISpecPropertyEditor.create({
                labelText : "Layout",
                navigateToItemCallback : args.navigateToItemCallback,
                options : options,
                propertyName : "layout",
                spec : args.spec,
                specChangedCallback : args.specChangedCallback,
            });

            element.appendChild(editor.element);
        }

        return element;
    },

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
            args.setImageURLCallback(Colby.imageToURL(pageArgs.image, "rw320"));
        } else if (pageArgs.spec.thumbnailURL) {
            args.setImageURLCallback(pageArgs.spec.thumbnailURL);
        } else {
            args.setImageURLCallback();
        }
    },

    /**
     * @param file chooserArgs.file
     * @param function chooserArgs.setImageURLCallback
     *
     * @return undefined
     */
    handleThumbnailChosen: function (chooserArgs) {
        var formData = new FormData();
        formData.append("image", chooserArgs.file);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBViewPageInformationEditor.handleThumbnailChosenDidLoad.bind(undefined, {xhr:xhr});
        xhr.open("POST", "/api/?class=CBImages&function=upload");
        xhr.send(formData);
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleThumbnailChosenDidLoad: function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CBViewPageEditor.setThumbnailImage(response.image);
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @return undefined
     */
    handleThumbnailRemoved: function () {
        CBViewPageEditor.setThumbnailImage();
    },
};
