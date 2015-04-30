"use strict";

var CBTestPage = {

    /*
    @return {undefined}
    */
    DOMContentDidLoad : function() {
        var mainElement         = document.getElementsByTagName("main")[0];

        var button              = document.createElement("button");
        button.style.display    = "block";
        button.style.margin     = "50px auto";
        button.textContent      = "Run PHP Tests";
        button.addEventListener("click", CBTestPage.runPHPTests);
        mainElement.appendChild(button);

        button                  = document.createElement("button");
        button.style.display    = "block";
        button.style.margin     = "50px auto";
        button.textContent      = "Run JavaScript Tests";
        button.addEventListener("click", CBTestPage.runJavaScriptTests);
        mainElement.appendChild(button);

        CBTestPage.panel = CBTestPage.newPanel();
    }
};


/**
 * @return function
 */
CBTestPage.dismissPanelCallback = function(panel)
{
    return function()
    {
        document.body.removeChild(panel);
    };
};

/**
 * @return HTMLElement
 */
CBTestPage.newPanel = function()
{
    var panel                   = document.createElement("div");
    panel.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    panel.style.bottom          = 0;
    panel.style.left            = 0;
    panel.style.position        = "fixed";
    panel.style.right           = 0;
    panel.style.top             = 0;

    var inner                   = document.createElement("div");
    inner.style.backgroundColor = "white";
    inner.style.width           = "640px";
    inner.style.height          = "320px";
    inner.style.margin          = "0px auto";
    inner.style.position        = "relative";
    inner.style.top             = "100px";
    panel.appendChild(inner);

    var pre                     = document.createElement("pre");
    pre.style.overflow          = "scroll";
    pre.style.padding           = "20px 20px 30px";
    panel.pre                   = pre;
    inner.appendChild(pre);

    var button                  = document.createElement("button");
    var buttonText              = document.createTextNode("Dismiss");
    button.style.bottom         = "10px";
    button.style.position       = "absolute";
    button.style.right          = "10px";
    button.addEventListener("click", CBTestPage.dismissPanelCallback(panel));
    button.appendChild(buttonText);
    inner.appendChild(button);

    return panel;
};

/**
 * @return void
 */
CBTestPage.runJavaScriptTests = function()
{
    CBTestPage.setPanelText("Running tests...");

    document.body.appendChild(CBTestPage.panel);

    var message = ColbyUnitTests.runJavaScriptTests();

    CBTestPage.setPanelText(message);
};

/**
 * @return void
 */
CBTestPage.runPHPTests = function()
{
    var xhr     = new XMLHttpRequest();
    xhr.onload  = CBTestPage.runPHPTestsDidComplete;
    xhr.open('POST', '/api/?class=CBUnitTests&function=runAllForAjax', true);
    xhr.send();

    CBTestPage.setPanelText("Waiting for response...");
    document.body.appendChild(CBTestPage.panel);
};

/**
 * @return void
 */
CBTestPage.runPHPTestsDidComplete = function()
{
    var xhr         = this;
    var response    = Colby.responseFromXMLHttpRequest(xhr);

    CBTestPage.setPanelText(response.message);
};

/**
 * @return void
 */
CBTestPage.setPanelText = function(text)
{
    var pre         = CBTestPage.panel.pre;
    var textNode    = document.createTextNode(text);

    while (pre.lastChild)
    {
        pre.removeChild(pre.lastChild);
    }

    pre.appendChild(textNode);
};

document.addEventListener("DOMContentLoaded", CBTestPage.DOMContentDidLoad, false);
