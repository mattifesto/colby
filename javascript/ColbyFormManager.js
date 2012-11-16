"use strict";

function ColbyFormManager()
{
    var elements = document.getElementsByTagName('fieldset');

    if (elements.length != 1)
    {
        throw new Error('There should be exactly one fieldset element on the page.');

        return;
    }

    this.fieldsetElement = elements[0];

    elements = this.fieldsetElement.getElementsByTagName('progress');

    if (elements.length != 1)
    {
        throw new Error('There should be exactly one progress element in the fieldset.');

        return;
    }

    this.progressElement = elements[0];

    this.isAjaxIndicatorOn = false;
}

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
