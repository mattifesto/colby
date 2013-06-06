"use strict";

/**
 * CPT = Colby Performance Tests
 */

var CPTMySQLvsColbyArchive = {};

CPTMySQLvsColbyArchive.ajaxActions =
[
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/100-prepare-for-tests/',
        'message': 'Preparing the test environment.'
    },
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/200-create-database-records/',
        'message': 'Testing database record creation.'
    },
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/300-create-archives/',
        'message': 'Testing archive creation.'
    },
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/400-read-database/',
        'message': 'Reading records from the database.'
    },
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/500-read-archives/',
        'message': 'Reading data form archives.'
    },
    {
        'uri': '/developer/performance-tests/mysql-vs-colbyarchive/ajax/900-cleanup/',
        'message': 'Cleaning up the test environment.'
    }
];


/**
 * @return void
 */
CPTMySQLvsColbyArchive.performNextAjaxAction = function()
{
    var ajaxAction = CPTMySQLvsColbyArchive.ajaxActions[CPTMySQLvsColbyArchive.nextAjaxActionIndex];

    var xhr = new XMLHttpRequest();

    xhr.open('POST', ajaxAction.uri, true);
    xhr.onload = function() { CPTMySQLvsColbyArchive.performNextAjaxActionCompleted(xhr); };
    xhr.send();

    document.getElementById('status').value += ajaxAction.message + '\n';
};

/**
 * @return void
 */
CPTMySQLvsColbyArchive.performNextAjaxActionCompleted = function(xhr)
{
    var response = Colby.responseFromXMLHttpRequest(xhr);

    if (response.wasSuccessful)
    {
        document.getElementById('status').value += response.message + '\n\n';

        /**
         * This works for the common case where the `actionShouldRepeat`
         * property is not defined. This way the ajax action implementer only
         * needs to return it when the action needs to repeat.
         */
        if (!response.actionShouldRepeat)
        {
            CPTMySQLvsColbyArchive.nextAjaxActionIndex++;
        }

        if (CPTMySQLvsColbyArchive.nextAjaxActionIndex < CPTMySQLvsColbyArchive.ajaxActions.length)
        {
            CPTMySQLvsColbyArchive.performNextAjaxAction();

            return;
        }
    }
    else
    {
        Colby.displayResponse(response);
    }

    CPTMySQLvsColbyArchive.runCompleted();
};

/**
 * @return void
 */
CPTMySQLvsColbyArchive.run = function()
{
    var progressElement = document.getElementById('progress');

    progressElement.removeAttribute('value');

    CPTMySQLvsColbyArchive.nextAjaxActionIndex = 0;

    CPTMySQLvsColbyArchive.performNextAjaxAction();
};

/**
 * @return void
 */
CPTMySQLvsColbyArchive.runCompleted = function()
{
    var progressElement = document.getElementById('progress');

    progressElement.setAttribute('value', 0);
};
