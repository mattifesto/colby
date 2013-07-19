"use strict";

var ColbyUserManagerViewController = {};

/**
 *
 */
ColbyUserManagerViewController.handleUpdateUserPermissionsResponse = function()
{
    var response = Colby.responseFromXMLHttpRequest(this);

    if (response.wasSuccessful)
    {
    }
    else
    {
        Colby.displayResponse(response);
    }
};

/**
 *
 */
ColbyUserManagerViewController.updateUserPermissions = function(userId, group, isMember)
{
    var formData = new FormData();
    formData.append('id', id);
    formData.append('hasBeenVerified', hasBeenVerified ? '1' : '0');

    xhr = new XMLHttpRequest();
    xhr.open('POST', '/admind/users/ajax/update-user-permissions/', true);
    xhr.onload = ColbyUserManagerViewController.handleUpdateUserPermissionsResponse;
    xhr.send(formData);
};
