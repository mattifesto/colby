"use strict";

var ColbyUserManagerViewController = {};

/**
 *
 */
ColbyUserManagerViewController.handleUpdateUserPermissionsResponse = function()
{
    this.checkboxElement.disabled = false;

    var response = Colby.responseFromXMLHttpRequest(this);

    if (!response.wasSuccessful)
    {
        Colby.displayResponse(response);
    }
};

/**
 *
 */
ColbyUserManagerViewController.updateUserPermissions = function(userId, groupName, checkbox)
{
    var shouldBeInGroup = checkbox.checked;

    var formData = new FormData();
    formData.append("userId", userId);
    formData.append("groupName", groupName);
    formData.append("shouldBeInGroup", shouldBeInGroup ? '1' : '0');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/users/ajax/update-user-permissions/', true);
    xhr.onload = ColbyUserManagerViewController.handleUpdateUserPermissionsResponse;
    xhr.send(formData);
    xhr.checkboxElement = checkbox;

    xhr.checkboxElement.disabled = true;
};
