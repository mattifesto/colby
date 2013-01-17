"use strict";

/**
 * This class is a temporary stand in for the FormData class that is only
 * available on the most recent browsers. It should be used for any public
 * facing AJAX. As soon as IE9 support is dropped all uses of this class should
 * be changed to `FormData` and this file should be deleted.
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
