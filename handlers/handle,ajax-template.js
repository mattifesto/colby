"use strict";

var CBAjaxTemplate = {};

/**
 * @return void
 */
CBAjaxTemplate.DOMContentDidLoad = function()
{
    var main                = document.getElementsByTagName("main")[0];
    var button              = document.createElement("button");
    var buttonText          = document.createTextNode("Make Request");
    button.style.display    = "block";
    button.style.margin     = "100px auto";
    button.addEventListener("click", CBAjaxTemplate.makeRequest);
    button.appendChild(buttonText);
    main.appendChild(button);
};

/**
 * @return void
 */
CBAjaxTemplate.makeRequest = function()
{
    var xhr     = new XMLHttpRequest();
    xhr.onload  = CBAjaxTemplate.makeRequestDidComplete;
    xhr.open("POST", "/ajax-template/api/request/", true);
    xhr.send();

    Colby.setPanelContent("Waiting for response...");
    Colby.showPanel();
};

/**
 * @return void
 */
CBAjaxTemplate.makeRequestDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    Colby.displayResponse(response);
};

document.addEventListener('DOMContentLoaded', CBAjaxTemplate.DOMContentDidLoad);
