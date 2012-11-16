"use strict";

var Colby = {};

Colby.handleError = function(message, url, lineNumber)
{
    alert('message: ' + message + '\n' +
          'url: ' + url + '\n' +
          'line number: ' + lineNumber);
}

onerror = Colby.handleError;
