"use strict";
/* globals Colby */

var ColbySiteUpdater = {

    update : function (sender) {
        sender.disabled = true;

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind({xhr:xhr});
        xhr.onload = ColbySiteUpdater.updateDidLoad.bind(undefined, {
            button : sender,
            xhr : xhr,
        });
        xhr.open('POST', '/api/?class=CBAdminPageForUpdate&function=updateForAjax', true);
        xhr.send();

        document.getElementById('progress').removeAttribute('value');
    },

    /**
     * @param Element args.button
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    updateDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        Colby.displayResponse(response);

        document.getElementById('progress').setAttribute('value', 0);

        args.button.disabled = false;

    }
};
