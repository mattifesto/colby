"use strict";

var CBUIImageUploader = {

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return object
     */
    createUploader : function(args) {
        var element = document.createElement("div");
        element.className = "CBUIImageUploader";
        var button = document.createElement("button");
        button.textContent = "Upload Image...";
        var input = document.createElement("input");
        input.type = "file";
        input.style.display = "none";

        element.appendChild(input);
        element.appendChild(button);

        button.addEventListener("click", input.click.bind(input));
        input.addEventListener("change", CBUIImageUploader.handleImageFileChosen.bind(undefined, {
            button : button,
            input : input,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }));

        return {
            element : element
        };
    },

    /**
     * @param Element args.button
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    handleImageDidLoad : function (args) {
        args.button.disabled = false;

        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.spec[args.propertyName] = response.image;
            args.specChangedCallback.call();
        } else {
            Colby.displayResponse(response);
        }
    },

    /**
     * @param Element args.button
     * @param Element args.input
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     */
    handleImageFileChosen : function (args) {
        args.button.disabled = true;

        var formData = new FormData();
        formData.append("image", args.input.files[0]);

        var xhr = new XMLHttpRequest();
        xhr.onload = CBUIImageUploader.handleImageDidLoad.bind(undefined, {
            button : args.button,
            propertyName : args.propertyName,
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
            xhr : xhr,
        });
        xhr.onerror = Colby.displayXHRError.bind(undefined, { xhr : xhr });
        xhr.open("POST", "/api/?class=CBImages&function=upload");
        xhr.send(formData);
    },
};
