"use strict";

/**
 *
 */
function ColbyXMLHttpRequest()
{
    this.onload = null;
    this.request = new XMLHttpRequest();
}

/**
 *
 */
ColbyXMLHttpRequest.prototype.open = function(method, url, isAsynchronous)
{
    if (method != 'POST')
    {
        throw new Error('The method for a ColbyXMLHttpRequest must be "POST".');
    }

    this.request.open(method, url, isAsynchronous);
};

/**
 *
 */
ColbyXMLHttpRequest.prototype.send = function(formData)
{
    if (this.onload)
    {
        var that = this;

        this.request.onreadystatechange = function()
        {
            if (that.request.readyState == 4)
            {
                that.onload();
            }
        }
    }

    if (arguments.length == 1)
    {
        this.request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        this.request.setRequestHeader('Content-length', formData.parameters.length);
        this.request.setRequestHeader('Connection', 'close');

        this.request.send(formData.parameters);
    }
    else
    {
        this.request.send();
    }
};
