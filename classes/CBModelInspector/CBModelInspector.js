"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBArtworkElement,
    CBMessageMarkup,
    CBModelInspector_modelID,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISectionItem4,
    CBUIStringEditor,
    CBUIStringsPart,
    Colby */

var CBModelInspector = {

    /**
     * @return undefined
     */
    init: function () {
        let navigator = CBUINavigationView.create();
        let spec = {
            ID: CBModelInspector_modelID,
        };
        let navigationHomeElement = document.createElement("div");
        let modelInformationElement = document.createElement("div");
        let IDDidChangeCallback = CBModelInspector.IDDidChange.bind(undefined, {
            spec: spec,
            container: modelInformationElement,
        });

        navigationHomeElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItemElement = CBUI.createSectionItem();
            sectionItemElement.appendChild(CBUIStringEditor.createEditor({
                labelText: "ID",
                propertyName: "ID",
                spec: spec,
                specChangedCallback: IDDidChangeCallback,
            }).element);
            sectionElement.appendChild(sectionItemElement);

            navigationHomeElement.appendChild(sectionElement);
            navigationHomeElement.appendChild(CBUI.createHalfSpace());
        }

        navigationHomeElement.appendChild(modelInformationElement);
        navigationHomeElement.appendChild(CBUI.createHalfSpace());

        let mainElement = document.getElementsByTagName("main")[0];
        mainElement.appendChild(navigator.element);

        navigator.navigate({
            element: navigationHomeElement,
            title: "Inspector",
        });

        IDDidChangeCallback();
    },

    /**
     * @param hex160? args.spec.ID
     * @param Element args.container
     *
     * @return undefined
     */
    IDDidChange: function (args) {
        if (/^[0-9a-f]{40}$/.test(args.spec.ID)) {
            Colby.callAjaxFunction("CBModelInspector", "fetchModelData", {ID: args.spec.ID})
                 .then(resolved)
                 .catch(Colby.displayAndReportError);
        } else {
            document.title = "Inspector: Invalid ID";
        }

        function resolved(response) {
            var section;

            args.container.textContent = "";

            section = CBUI.createSection();

            if (response.modelVersions.length === 0) {
                document.title = "Inspector: " + args.spec.ID;
                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Notice",
                    value: "This ID has no model."
                }).element);
            } else {
                var model = JSON.parse(response.modelVersions[0].modelAsJSON);

                document.title = "Inspector: " + (model.title ? model.title.trim() : model.className);

                if (model.className === "CBImage") {
                    let container = document.createElement("div");
                    container.style.display = "flex";
                    container.style.padding = "0 20px";
                    container.style.justifyContent = "center";

                    let image = CBArtworkElement.create({
                        filename: "rw1600",
                        image: model,
                        width: "800px"
                    });

                    container.appendChild(image);
                    args.container.appendChild(container);
                    args.container.appendChild(CBUI.createHalfSpace());
                }

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Class Name",
                    value: model.className,
                }).element);

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Title",
                    value: model.title,
                }).element);

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Description",
                    value: model.description,
                }).element);

                {
                    let sectionItem = CBUISectionItem4.create();
                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = "Edit Model";

                    stringsPart.element.classList.add("action");

                    sectionItem.callback = function () {
                        window.location = '/admin/?c=CBModelEditor&ID=' + args.spec.ID;
                    };

                    sectionItem.appendPart(stringsPart);
                    section.appendChild(sectionItem.element);
                }
            }

            args.container.appendChild(section);
            args.container.appendChild(CBUI.createHalfSpace());

            {
                let sectionHeaderElement = CBUI.createSectionHeader({
                    text: "Versions",
                });

                args.container.appendChild(sectionHeaderElement);
            }

            if (response.modelVersions.length > 0) {
                section = CBUI.createSection();

                response.modelVersions.forEach(function (version) {
                    var item = CBUI.createSectionItem2();

                    var time = document.createElement("time");
                    time.className = "time";
                    time.dataset.timestamp = version.timestamp * 1000;
                    item.titleElement.appendChild(time);

                    var specCommand = document.createElement("div");
                    specCommand.className = "command";
                    specCommand.textContent = "Spec";
                    specCommand.addEventListener("click", showSpec);

                    item.commandsElement.appendChild(specCommand);

                    var modelCommand = document.createElement("div");
                    modelCommand.className = "command";
                    modelCommand.textContent = "Model";
                    modelCommand.addEventListener("click", showModel);

                    item.commandsElement.appendChild(modelCommand);

                    var revertCommand = document.createElement("div");
                    revertCommand.className = "command";
                    revertCommand.textContent = "Revert";
                    revertCommand.addEventListener("click", revert);

                    item.commandsElement.appendChild(revertCommand);

                    section.appendChild(item.element);

                    function showModel() {
                        var pre = document.createElement("div");
                        pre.textContent = JSON.stringify(JSON.parse(version.modelAsJSON), undefined, 2);
                        pre.style.whiteSpace = "pre-wrap";

                        Colby.setPanelElement(pre);
                        Colby.showPanel();
                    }

                    function showSpec() {
                        var pre = document.createElement("div");
                        pre.textContent = JSON.stringify(JSON.parse(version.specAsJSON), undefined, 2);
                        pre.style.whiteSpace = "pre-wrap";

                        Colby.setPanelElement(pre);
                        Colby.showPanel();
                    }

                    function revert() {
                        var data = new FormData();
                        data.append("ID", args.spec.ID);
                        data.append("version", version.version);

                        Colby.fetchAjaxResponse("/api/?class=CBModels&function=revert", data)
                             .then(resolved)
                             .catch(Colby.report);

                        function resolved(response) {
                            location.reload(true);
                        }
                    }
                });

                args.container.appendChild(section);
            }

            args.container.appendChild(CBUI.createHalfSpace());

            if (response.rowFromColbyPages) {
                args.container.appendChild(CBUIExpander.create({
                    message: "ColbyPages Row\n\n--- pre\n" +
                             CBMessageMarkup.stringToMarkup(JSON.stringify(response.rowFromColbyPages, undefined, 2)) +
                             "\n---",
                }).element);
            }

            if (response.rowFromCBImages) {
                args.container.appendChild(CBUIExpander.create({
                    message: "CBImages Row\n\n--- pre\n" +
                             CBMessageMarkup.stringToMarkup(JSON.stringify(response.rowFromCBImages, undefined, 2)) +
                             "\n---",
                }).element);
            }

            if (response.dataStoreFiles.length > 0) {
                let links = response.dataStoreFiles.map(function (file) {
                    let text = CBMessageMarkup.stringToMarkup(file.text);
                    let URL = CBMessageMarkup.stringToMarkup(file.URL);

                    return `(${text} (a ${URL}))`;
                });

                args.container.appendChild(CBUIExpander.create({
                    message: "Data Store Files\n\n--- ul\n" +
                             links.join("\n\n") +
                             "\n---",
                }).element);
            }

            if (response.archive.length > 0) {
                args.container.appendChild(CBUIExpander.create({
                    message: "Archive\n\n--- pre\n" +
                             CBMessageMarkup.stringToMarkup(response.archive) +
                             "\n---",
                }).element);
            }

            Colby.updateTimes();
        }
    },
};

Colby.afterDOMContentLoaded(CBModelInspector.init);
