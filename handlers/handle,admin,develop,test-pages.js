"use strict";


var CBTestPages =
{
    requestIsActive : false
};

/**
 * @return void
 */
CBTestPages.deleteTestPages = function()
{
    if (CBTestPages.requestIsActive)
    {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/develop/test-pages/api/delete/", true);
    xhr.onload = CBTestPages.requestDidComplete;
    xhr.send();

    CBTestPages.requestIsActive = true;
    document.getElementById("spinner").classList.add("active");
};

/**
 * @return void
 */
CBTestPages.generateTestPages = function()
{
    if (CBTestPages.requestIsActive)
    {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/admin/develop/test-pages/api/generate/", true);
    xhr.onload = CBTestPages.requestDidComplete;
    xhr.send();

    CBTestPages.requestIsActive = true;
    document.getElementById("spinner").classList.add("active");
};

/**
 * @return void
 */
CBTestPages.requestDidComplete = function()
{
    CBTestPages.requestIsActive = false;
    document.getElementById("spinner").classList.remove("active");

    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        console.log("request did complete");
    }
    else
    {
        Colby.displayResponse(response);
    }
};
