"use strict";

var CBTestPage = {

    /**
    @return {Element}
    */
    createTestUI : function() {

        var element                 = document.createElement("div");
        element.className           = "CBTestUI";
        var buttonForPHP            = document.createElement("button");
        buttonForPHP.textContent    = "Run PHP Tests";
        var buttonForJS             = document.createElement("button");
        buttonForJS.textContent     = "Run JavaScript Tests";
        var status                  = document.createElement("textarea");

        buttonForPHP.addEventListener("click", CBTestPage.handleRunPHPTestsRequested.bind(undefined, {
            buttonElement   : buttonForPHP,
            statusElement   : status }));
        buttonForJS.addEventListener("click", CBTestPage.runJavaScriptTests);

        element.appendChild(buttonForPHP);
        element.appendChild(buttonForJS);
        element.appendChild(status);

        return element;
    },

    /**
    @return {undefined}
    */
    DOMContentDidLoad : function() {
        var main = document.getElementsByTagName("main")[0];

        main.appendChild(CBTestPage.createTestUI());

        CBTestPage.panel = CBTestPage.newPanel();
    },

    /**
    @param {Element}    buttonElement
    @param {Element}    statusElement

    @return {undefined}
    */
    handleRunPHPTestsRequested : function(args) {
        args.buttonElement.disabled = true;
        var xhr                     = new XMLHttpRequest();
        xhr.onload                  = CBTestPage.runPHPTestsDidComplete.bind(undefined, {
            buttonElement           : args.buttonElement,
            statusElement           : args.statusElement,
            xhr                     : xhr });

        xhr.open('POST', '/api/?class=CBUnitTests&function=runAllForAjax', true);
        xhr.send();

        var date                    = new Date();
        args.statusElement.value    = "CBUnitTests::runAllForAjax - " +
                                      date.toLocaleDateString() +
                                      " " +
                                      date.toLocaleTimeString() +
                                      "\n";
    },

    /**
    @param {Element}        buttonElement
    @param {Element}        statusElement
    @param {XMLHttpRequest} xhr

    @return {undefined}
    */
    runPHPTestsDidComplete : function(args) {
        args.buttonElement.disabled = false;
        var response                = Colby.responseFromXMLHttpRequest(args.xhr);

        args.statusElement.value += response.message + "\n";
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
