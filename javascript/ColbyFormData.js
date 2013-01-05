"use strict";

/**
 *
 */
function ColbyFormData()
{
    var parameters = '';
}

/**
 *
 */
ColbyFormData.prototype.append = function(name, value)
{
    if (parameters)
    {
        parameters += '&';
    }

    parameters += encodeURIComponenet(name) + '=' + encodeURIComponent(value);
};
