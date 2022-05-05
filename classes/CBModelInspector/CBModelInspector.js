"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBAjax,
    CBArtworkElement,
    CBImage,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIExpander,
    CBUIImageChooser,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUIStringEditor2,
    CBUIStringsPart,
    Colby,

    CBModelInspector_associatedImageModel,
    CBModelInspector_modelID,
*/



(function () {


    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let navigator = CBUINavigationView.create();

        let spec = {
            ID: CBModelInspector_modelID,
        };

        let navigationHomeElement = document.createElement("div");
        let modelInformationElement = document.createElement("div");

        let IDDidChangeCallback = IDDidChange.bind(
            undefined,
            {
                spec: spec,
                container: modelInformationElement,
            }
        );

        navigationHomeElement.appendChild(
            CBUI.createHalfSpace()
        );

        {
            let sectionElement = CBUI.createSection();

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "ID",
                    "ID",
                    IDDidChangeCallback
                )
            );

            navigationHomeElement.appendChild(
                sectionElement
            );

            navigationHomeElement.appendChild(
                CBUI.createHalfSpace()
            );
        }

        navigationHomeElement.appendChild(modelInformationElement);

        navigationHomeElement.appendChild(
            CBUI.createHalfSpace()
        );

        let mainElement = document.getElementsByTagName("main")[0];
        mainElement.appendChild(navigator.element);

        navigator.navigate(
            {
                element: navigationHomeElement,
                title: "Inspector",
            }
        );

        IDDidChangeCallback();
    }
    /* afterDOMContentLoaded() */



    /**
     * @param object args
     *
     *      {
     *          container: Element
     *          spec: object
     *
     *              {
     *                  ID: string
     *              }
     *      }
     *
     * @return undefined
     */
    function IDDidChange(
        args
    ) {
        let containerElement = args.container;
        let model, modelData;

        let modelID = CBModel.valueAsID(
            args.spec,
            "ID"
        );

        if (modelID === undefined) {
            document.title = "Inspector: Invalid ID";
            containerElement.textContent = "";
            return;
        }

        CBAjax.call(
            "CBModelInspector",
            "fetchModelData",
            {
                ID: modelID,
            }
        ).then(
            function (value) {
                modelData = value;

                if (modelData.modelVersions.length > 0) {
                    model = JSON.parse(
                        modelData.modelVersions[0].modelAsJSON
                    );
                } else {
                    model = undefined;
                }

                IDDidChange_render();
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return Element
         */
        function IDDidChange_createAssociatedWithElement() {
            let element = CBUI.createElement(
                "CBModelInspector_associatedWith"
            );

            if (modelData.associatedWith.length === 0) {
                return element;
            }

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Associated With";

            element.appendChild(titleElement);

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            modelData.associatedWith.forEach(
                function renderAssociation(association) {
                    let associationElement = CBUI.createElement(
                        "CBUI_container_leftAndRight"
                    );

                    sectionElement.appendChild(associationElement);

                    /* associated with ID */

                    let associatedWithIDElement = CBUI.createElement(
                        "CBUI_textSize_small CBUI_fontFamily_monospace",
                        "a"
                    );

                    let associatedWithID = CBModel.valueAsID(
                        association,
                        "ID"
                    );

                    associatedWithIDElement.href = (
                        "/admin/?c=CBModelInspector&ID=" +
                        associatedWithID
                    );

                    associatedWithIDElement.textContent = associatedWithID;

                    associationElement.appendChild(associatedWithIDElement);

                    /* association key */

                    let associationKeyElement = CBUI.createElement();

                    associationKeyElement.textContent = CBModel.valueToString(
                        association,
                        "className"
                    );

                    associationElement.appendChild(associationKeyElement);
                }
            );

            return element;
        }
        /* IDDidChange_createAssociatedWithElement() */



        /**
         * @return Element
         */
        function IDDidChange_createAssociationsElement() {
            let element = CBUI.createElement(
                "CBModelInspector_associations"
            );

            if (modelData.associations.length === 0) {
                return element;
            }

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Associations";

            element.appendChild(titleElement);

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            modelData.associations.forEach(
                function renderAssociation(association) {
                    let associationElement = CBUI.createElement(
                        "CBUI_container_leftAndRight"
                    );

                    sectionElement.appendChild(associationElement);

                    let keyElement = CBUI.createElement();

                    keyElement.textContent = CBModel.valueToString(
                        association,
                        "className"
                    );

                    associationElement.appendChild(keyElement);

                    let IDElement = CBUI.createElement(
                        "CBUI_textSize_small CBUI_fontFamily_monospace",
                        "a"
                    );

                    let associatedID = CBModel.valueAsID(
                        association,
                        "associatedID"
                    );

                    IDElement.href = (
                        "/admin/?c=CBModelInspector&ID=" +
                        associatedID
                    );

                    IDElement.textContent = associatedID;

                    associationElement.appendChild(IDElement);
                }
            );

            return element;
        }
        /* IDDidChange_createAssociationsElement() */



        /**
         * @return undefined
         */
        function IDDidChange_render() {
            let section;

            containerElement.textContent = "";

            section = CBUI.createSection();
            containerElement.appendChild(section);

            if (model === undefined) {
                document.title = "Inspector: " + modelData.modelID;

                section.appendChild(
                    CBUI.createKeyValueSectionItem(
                        {
                            key: "Notice",
                            value: "This ID has no model."
                        }
                    ).element
                );

                return;
            }

            document.title = (
                "Inspector: " +
                (
                    model.title ?
                    model.title.trim() :
                    model.className
                )
            );

            section.appendChild(
                CBUI.createKeyValueSectionItem(
                    {
                        key: "Class Name",
                        value: model.className,
                    }
                ).element
            );

            section.appendChild(
                CBUI.createKeyValueSectionItem(
                    {
                        key: "Title",
                        value: model.title,
                    }
                ).element
            );

            section.appendChild(
                CBUI.createKeyValueSectionItem(
                    {
                        key: "Description",
                        value: model.description,
                    }
                ).element
            );

            {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Edit Model";

                stringsPart.element.classList.add("action");

                sectionItem.callback = function () {
                    window.location = (
                        '/admin/?c=CBModelEditor&ID=' +
                        model.ID
                    );
                };

                sectionItem.appendPart(stringsPart);
                section.appendChild(sectionItem.element);
            }

            {
                let sectionItem = CBUISectionItem4.create();

                sectionItem.callback = function () {
                    confirm();
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Delete Model";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendChild(sectionItem.element);

                /* stage 1 */
                let confirm = function () {
                    CBUIPanel.confirmText(
                        "Are you sure you want to delete this model?"
                    ).then(
                        function (wasConfirmed) {
                            if (wasConfirmed) {
                                deleteModel();
                            }
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayAndReportError(error);
                        }
                    );
                };

                /* stage 2 */
                let deleteModel = function () {
                    let controller = CBUIPanel.displayBusyText(
                        "Deleting model..."
                    );

                    CBAjax.call(
                        "CBModels",
                        "deleteByID",
                        {
                            ID: model.ID,
                        }
                    ).then(
                        function () {
                            return new Promise(
                                function (resolve) {
                                    window.setTimeout(
                                        resolve,
                                        3000
                                    );
                                }
                            );
                        }
                    ).then(
                        function () {
                            report();
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayAndReportError(error);
                        }
                    ).finally(
                        function () {
                            controller.hide();
                        }
                    );
                };

                /* stage 3 */
                let report = function () {
                    CBUIPanel.displayText(
                        "The model has been deleted. Press OK to " +
                        "navigate to the models admin page."
                    ).then(
                        function () {
                            window.location =
                            "/admin/?c=Admin_CBModelClassList";
                        }
                    ).catch(
                        function (error) {
                            CBUIPanel.displayAndReportError(error);
                        }
                    );
                };
            }

            containerElement.appendChild(
                CBUI.createHalfSpace()
            );


            /* CBImage view */

            if (model.className === "CBImage") {
                containerElement.appendChild(
                    createCBImageViewElement(model)
                );
            }


            /* associated image */

            if (
                model.className !== "CBImage" ||
                CBModelInspector_associatedImageModel !== null
            ){
                containerElement.appendChild(
                    createAssociatedImageEditorElement(model)
                );
            }


            /* associations */

            containerElement.appendChild(
                IDDidChange_createAssociationsElement()
            );


            /* associated with */

            containerElement.appendChild(
                IDDidChange_createAssociatedWithElement()
            );


            /* versions */

            if (modelData.modelVersions.length > 0) {
                {
                    let titleElement = document.createElement("div");
                    titleElement.className = "CBUI_title1";
                    titleElement.textContent = "Versions";
                    containerElement.appendChild(titleElement);
                }

                section = CBUI.createSection();

                modelData.modelVersions.forEach(
                    function (versionInformation) {
                        section.appendChild(
                            createVersionSectionItemElement(
                                versionInformation
                            )
                        );
                    }
                );

                containerElement.appendChild(section);
            }

            containerElement.appendChild(
                CBUI.createHalfSpace()
            );

            if (modelData.rowFromColbyPages) {
                containerElement.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "ColbyPages Row\n\n--- pre\n" +
                                CBMessageMarkup.stringToMarkup(
                                    JSON.stringify(
                                        modelData.rowFromColbyPages,
                                        undefined,
                                        2
                                    )
                                ) +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            if (modelData.rowFromCBImages) {
                containerElement.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "CBImages Row\n\n--- pre\n" +
                                CBMessageMarkup.stringToMarkup(
                                    JSON.stringify(
                                        modelData.rowFromCBImages,
                                        undefined,
                                        2
                                    )
                                ) +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            if (modelData.dataStoreFiles.length > 0) {
                let links = modelData.dataStoreFiles.map(
                    function (file) {
                        let text = CBMessageMarkup.stringToMarkup(file.text);
                        let URL = CBMessageMarkup.stringToMarkup(file.URL);

                        return `(${text} (a ${URL}))`;
                    }
                );

                containerElement.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "Data Store Files\n\n--- ul\n" +
                                links.join("\n\n") +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            Colby.updateTimes();
        }
        /* IDDidChange_render() */

    }
    /* IDDidChange() */



    /**
     * @return Element
     */
    function createAssociatedImageEditorElement() {
        let editorElement = CBUI.createElement(
            "CBModelInspector_associatedImageEditor"
        );

        let titleElement = CBUI.createElement(
            "CBUI_title1"
        );

        editorElement.appendChild(titleElement);

        titleElement.textContent = "Associated Image";

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        editorElement.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(sectionElement);

        let chooser = CBUIImageChooser.create();

        sectionElement.appendChild(
            chooser.element
        );

        if (CBModelInspector_associatedImageModel !== null) {
            chooser.src = CBImage.toURL(
                CBModelInspector_associatedImageModel,
                "rw1600"
            );
        }

        chooser.chosen = function (args) {
            CBAjax.call(
                "CBImages",
                "upload",
                undefined,
                args.file
            ).then(
                function (imageSpec) {
                    return CBAjax.call(
                        "CBModelToCBImageAssociation",
                        "replaceImageID",
                        {
                            modelID: CBModelInspector_modelID,
                            imageID: imageSpec.ID,
                        }
                    ).then(
                        function () {
                            chooser.src = CBImage.toURL(
                                imageSpec,
                                "rw1600"
                            );
                        }
                    );
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        };

        return editorElement;
    }
    /* createAssociatedImageEditorElement() */



    /**
     * This function renders the image for a CBImage model.
     *
     * @param object model
     *
     * @return Element
     */
    function createCBImageViewElement(model) {
        let viewElement = CBUI.createElement(
            "CBModelInspector_CBImageView"
        );

        let titleElement = CBUI.createElement(
            "CBUI_title1"
        );

        viewElement.appendChild(titleElement);

        titleElement.textContent = "Image";

        let imageContainerElement = CBUI.createElement(
            "CBUI_container1 CBDarkTheme"
        );

        viewElement.appendChild(imageContainerElement);

        let artworkElement = CBArtworkElement.create(
            {
                URL: CBImage.toURL(model, "rw1600"),
                aspectRatioWidth: model.width,
                aspectRatioHeight: model.height,
                maxHeight: 400,
                maxWidth: 800,
            }
        );

        imageContainerElement.appendChild(artworkElement);

        return viewElement;
    }
    /* createCBImagePanelElement() */



    /**
     * @param object versionInformation
     *
     * return Element
     */
    function createVersionSectionItemElement(
        versionInformation
    ) {
        let versionSpec = JSON.parse(
            versionInformation.specAsJSON
        );

        let versionModel = JSON.parse(
            versionInformation.modelAsJSON
        );

        let unixNow = Math.floor(
            Date.now() / 1000
        );

        let versionCreated = new Date(
            versionInformation.timestamp * 1000
        );

        let info = "";

        if (versionInformation.replaced !== null) {
            let deathspan = (
                unixNow - versionInformation.replaced
            ).toLocaleString();

            let lifespan = (
                versionInformation.replaced - versionInformation.timestamp
            ).toLocaleString();

            info = `(${deathspan} / ${lifespan} ${versionInformation.action})`;
        }

        let versionSectionItemElement;
        let versionDescriptionElement;

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                "CBModelInspector_versionTitle CBUI_ellipsis"
            );

            versionSectionItemElement = elements[0];

            let titleElement = elements[2];

            titleElement.textContent = (
                `Version ${versionInformation.version} ${info}`
            );

            let textContainerElement = elements[1];

            versionDescriptionElement = CBUI.createElement(
                "CBModelInspector_versionDescription CBUI_textSize_small " +
                "CBUI_textColor2 CBUI_ellipsis"
            );

            textContainerElement.appendChild(
                versionDescriptionElement
            );
        }


        Colby.requestTimeUpdate(
            function (javascriptTimestamp) {
                let now = new Date(javascriptTimestamp);

                versionDescriptionElement.textContent = (
                    Colby.dateToRelativeLocaleString(
                        versionCreated,
                        now
                    )
                );
            }
        );

        versionSectionItemElement.addEventListener(
            "click",
            showVersion
        );

        versionSectionItemElement.appendChild(
            CBUI.createElement(
                "CBUI_navigationArrow"
            )
        );

        return versionSectionItemElement;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function showVersion() {
            let element = document.createElement("div");

            element.appendChild(
                CBUI.createHalfSpace()
            );

            {
                let sectionElement = CBUI.createSection();
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Revert to This Version";

                stringsPart.element.classList.add("action");

                sectionItem.callback = revert;

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
                element.appendChild(sectionElement);
                element.appendChild(CBUI.createHalfSpace());
            }

            {
                let message = CBMessageMarkup.stringToMarkup(
                    JSON.stringify(
                        versionSpec,
                        undefined,
                        2
                    )
                );

                let expander = CBUIExpander.create();
                expander.expanded = true;
                expander.message = `

                    Spec

                    --- pre\n${message}
                    ---

                `;

                element.appendChild(expander.element);

                element.appendChild(
                    CBUI.createHalfSpace()
                );
            }

            {
                let message = CBMessageMarkup.stringToMarkup(
                    JSON.stringify(
                        versionModel,
                        undefined,
                        2
                    )
                );

                let expander = CBUIExpander.create();
                expander.expanded = true;
                expander.message = `

                    Model

                    --- pre\n${message}
                    ---

                `;

                element.appendChild(expander.element);

                element.appendChild(
                    CBUI.createHalfSpace()
                );
            }

            CBUINavigationView.context.navigate(
                {
                    element: element,
                    title: `Version ${versionInformation.version}`,
                }
            );


            /**
             * @return undefined
             */
            function revert() {
                CBAjax.call(
                    "CBModels",
                    "revert",
                    {
                        ID: versionModel.ID,
                        version: versionInformation.version,
                    }
                ).then(
                    function () {
                        location.reload(true);
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayAndReportError(error);
                    }
                );
            }
        }
        /* showVersion() */

    }
    /* createVersionSectionItemElement() */

})();
