"using strict";

/**
 *
 */
function CBSection(title)
{
    var outerElement    = document.createElement("section");
    this._outerElement  = outerElement;


    var header      = document.createElement("header");
    header.appendChild(document.createTextNode(title));
    this._header    = header;

    outerElement.appendChild(header);


    var innerElement    = document.createElement("div");
    this._innerElement  = innerElement;

    outerElement.appendChild(innerElement);
}

/**
 *
 */
CBSection.prototype.innerElement = function()
{
    return this._innerElement;
}

/**
 *
 */
CBSection.prototype.outerElement = function()
{
    return this._outerElement;
}
