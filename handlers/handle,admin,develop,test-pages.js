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
    xhr.onload = CBTestPages.deleteTestPagesDidComplete;
    xhr.send();

    CBTestPages.requestIsActive = true;
    document.getElementById("spinner").classList.add("active");

    CBTestPages.timeElement.setAttribute("data-timestamp", Date.now());
    Colby.beginUpdatingTimes();
};

/**
 * @return void
 */
CBTestPages.deleteTestPagesDidComplete = function()
{
    CBTestPages.requestIsActive = false;
    document.getElementById("spinner").classList.remove("active");

    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    CBTestPages.setCountOfTestPages(response.countOfRemainingPages);

    if (response.countOfRemainingPages > 0)
    {
        CBTestPages.deleteTestPages();
    }
};

/**
 * @return void
 */
CBTestPages.DOMContentDidLoad = function()
{
    var mainElement = document.getElementsByTagName("main")[0];

    var div                     = document.createElement("div");
    div.style.textAlign         = "center";
    mainElement.appendChild(div);

    CBTestPages.countElement = div;

    var time = document.createElement("time");
    time.classList.add("time");
    time.setAttribute("data-timestamp", Date.now());
    mainElement.appendChild(time);

    CBTestPages.timeElement = time;
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

    CBTestPages.timeElement.setAttribute("data-timestamp", Date.now());
    Colby.beginUpdatingTimes();
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

        CBTestPages.setCountOfTestPages(response.countOfTestPages);

        if (response.countOfTestPages < 1000000)
        {
            CBTestPages.generateTestPages();
        };
    }
    else
    {
        Colby.displayResponse(response);
    }
};

/**
 * @return void
 */
CBTestPages.setCountOfTestPages = function(count)
{
    var countElement = CBTestPages.countElement;

    while (countElement.lastChild)
    {
        countElement.removeChild(countElement.lastChild);
    }

    var textNode = document.createTextNode(count);
    countElement.appendChild(textNode);
};

document.addEventListener('DOMContentLoaded', CBTestPages.DOMContentDidLoad);
