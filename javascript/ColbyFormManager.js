"use strict";

/**
 *
 */
function ColbyFormManager(ajaxURL)
{
    this.ajaxURL = ajaxURL;
    this.isAjaxIndicatorOn = false;

    var elements = document.getElementsByTagName('fieldset');

    if (elements.length != 1)
    {
        throw new Error('There should be exactly one fieldset element on the page.');

        return;
    }

    this.fieldsetElement = elements.item(0);

    elements = this.fieldsetElement.getElementsByTagName('progress');

    if (elements.length != 1)
    {
        throw new Error('There should be exactly one progress element in the fieldset.');

        return;
    }

    this.progressElement = elements.item(0);

    elements = this.fieldsetElement.getElementsByTagName('input');

    this.addKeyDownListenerToElements(elements);

    elements = this.fieldsetElement.getElementsByTagName('textarea');

    this.addKeyDownListenerToElements(elements);
}

/**
 *
 */
ColbyFormManager.prototype.addKeyDownListenerToElements = function(collection)
{
    var self = this;

    var handler = function()
    {
        self.handleKeyDown(this);
    }

    for (var i = 0; i < collection.length; i++)
    {
        var element = collection[i];

        if (element.classList.contains('ignore'))
        {
            continue;
        }

        // All elements in the collection will have the same tag name. If the current colletion is of input elements, we only want to attach listeners to text type inputs.

        if (element.tagName == 'INPUT' && element.type != 'text')
        {
            continue;
        }

        element.addEventListener('keydown', handler, false);
    }
}

/**
 * @return array
 */
ColbyFormManager.prototype.getFormElements = function()
{
    var element;
    var i;
    var arrayOfElements = new Array();

    elements = this.fieldsetElement.getElementsByTagName('input');

    i = 0;

    while (element = elements.item(i))
    {
        if (!element.classList.contains('ignore'))
        {
            arrayOfElements.push(element);
        }

        i++;
    }

    elements = this.fieldsetElement.getElementsByTagName('textarea');

    i = 0;

    while (element = elements.item(i))
    {
        if (!element.classList.contains('ignore'))
        {
            arrayOfElements.push(element);
        }

        i++;
    }

    return arrayOfElements;
}

/**
 * @return object
 *  ajax response data
 */
ColbyFormManager.prototype.handleAjaxResponse = function()
{
    if (xhr.status == 200)
    {
        var response = JSON.parse(xhr.responseText);
    }
    else
    {
        var response =
        {
            'wasSuccessful' : false,
            'message' : xhr.status + ': ' + xhr.statusText
        };
    }

    var errorLog = document.getElementById('error-log');

    // remove error-log element content

    while (errorLog.firstChild)
    {
        errorLog.removeChild(errorLog.firstChild);
    }

    var p = document.createElement('p');
    var t = document.createTextNode(response.message);

    p.appendChild(t);
    errorLog.appendChild(p);

    if ('stackTrace' in response)
    {
        var pre = document.createElement('pre');
        t = document.createTextNode(response.stackTrace);

        pre.appendChild(t);
        errorLog.appendChild(pre);
    }

    xhr = null;

    formManager.setIsAjaxIndicatorOn(false);

    return response;
}

/**
 *
 */
ColbyFormManager.prototype.handleKeyDown = function(sender)
{
    // add class "needs-update"

    sender.classList.add('needs-update');

    this.setNeedsUpdate(true);
}

/**
 *
 */
ColbyFormManager.prototype.handleUpdateComplete = function()
{
    var response = this.handleAjaxResponse();

    this.setIsAjaxIndicatorOn(false);

    // remove class "now-updating" from all elements

    var elements = this.fieldsetElement.getElementsByClassName('now-updating');

    var element = null;

    while (element = elements.item(0))
    {
        element.classList.remove('now-updating');
    }
}

/**
 *
 */
ColbyFormManager.prototype.setIsAjaxIndicatorOn = function(isAjaxIndicatorOn)
{
    if (isAjaxIndicatorOn)
    {
        this.progressElement.removeAttribute('value');
    }
    else
    {
        this.progressElement.setAttribute('value', '0');
    }

    this.isAjaxIndicatorOn = isAjaxIndicatorOn;
}

/**
 *
 */
ColbyFormManager.prototype.setNeedsUpdate = function(needsUpdate)
{
    if (needsUpdate)
    {
        this.needsUpdate = true;

        if (this.timer)
        {
            clearTimeout(this.timer);
        }

        self = this;

        var handler = function()
        {
            self.update();
        }

        this.timer = setTimeout(handler, 3000);
    }
    else
    {
        this.needsUpdate = false;

        if (this.timer)
        {
            clearTimeout(this.timer);

            this.timer = null;
        }
    }
}

/**
 *
 */
ColbyFormManager.prototype.update = function()
{
    this.setNeedsUpdate(false);

    this.setIsAjaxIndicatorOn(true);

    // change all elements with class "needs-update" to class "now-updating"

    var elements = this.fieldsetElement.getElementsByClassName('needs-update');

    var element = null;

    while (element = elements.item(0))
    {
        element.classList.add('now-updating');
        element.classList.remove('needs-update');
    }

    var self = this;

    var handler = function()
    {
        self.handleUpdateComplete();
    }

    elements = this.getFormElements();

    formData = new FormData();

    for (var i = 0; i < elements.length; i++)
    {
        element = elements[i];

        formData.append(element.id, element.value);
    }

    xhr = new XMLHttpRequest();
    xhr.open('POST', this.ajaxURL, true);
    xhr.onload = handler;
    xhr.send(formData);
}
