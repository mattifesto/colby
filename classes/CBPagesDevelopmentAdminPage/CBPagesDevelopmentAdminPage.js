"use strict";
/* jshint strict: global */
/* global
    CBUI,
    Colby */

var CBPagesDevelopmentAdminPage = {

    /**
     * @return undefined
     */
    init: function () {
        var element = document.getElementsByClassName("CBPagesDevelopmentAdminPage_content")[0];
        var buttonsElement = document.createElement("div");
        buttonsElement.className = "buttons";

        buttonsElement.appendChild(CBUI.createButton({
            callback: function () {
                Colby.callAjaxFunction("CBPageVerificationTask", "startForAllPages")
                    .then(function () { Colby.alert("Verification as been started for all pages."); })
                    .catch(Colby.displayAndReportError);
            },
            text: "Start Verification for All Pages",
        }).element);

        buttonsElement.appendChild(CBUI.createButton({
            callback: function () {
                Colby.callAjaxFunction("CBPageVerificationTask", "startForNewPages")
                    .then(function () { Colby.alert("Verification has been started for all new pages."); })
                    .catch(Colby.displayAndReportError);
            },
            text: "Start Verification for New Pages",
        }).element);

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(buttonsElement);
        element.appendChild(CBUI.createHalfSpace());
    }
};

Colby.afterDOMContentLoaded(CBPagesDevelopmentAdminPage.init);
