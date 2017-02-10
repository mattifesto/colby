"use strict"; /* jshint strict: global */

/* global
    CBAdminPageForModelInspectorID,
    CBUI,
    Colby */

var CBAdminPageForModelInspector = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        var main = document.getElementsByTagName("main")[0];
        var versionsSectionElement = CBUI.createSection();

        main.appendChild(CBUI.createHalfSpace());

        main.appendChild(versionsSectionElement);

        main.appendChild(CBUI.createHalfSpace());

        CBAdminPageForModelInspector.IDDidChange(CBAdminPageForModelInspectorID, versionsSectionElement);
    },

    /**
     * @param hex160 ID
     * @param Element versionsSectionElement
     *
     * @return undefined
     */
    IDDidChange: function (ID, versionsSectionElement) {
        if (/^[0-9a-f]{40}$/.test(ID)) {
            console.log("yes");
            var data = new FormData();
            data.append("ID", ID);

            Colby.fetchAjaxResponse("/api/?class=CBModels&function=fetchModelVersionsByID", data)
                 .then(resolved)
                 .catch(Colby.report);
        }

        function resolved(response) {
            versionsSectionElement.textContent = undefined;
            response.versions.forEach(function (version) {
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

                //item.titleElement.textContent = version.version;
                versionsSectionElement.appendChild(item.element);

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
                    data.append("ID", ID);
                    data.append("version", version.version);

                    Colby.fetchAjaxResponse("/api/?class=CBModels&function=revert", data)
                         .then(resolved)
                         .catch(Colby.report);

                    function resolved(response) {
                        location.reload(true);
                    }
                }
            });

            Colby.updateTimes();
        }
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForModelInspector.DOMContentDidLoad);
