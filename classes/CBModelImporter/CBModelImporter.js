"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelImporter */
/* global
    CBModelImporter_processID,
    Colby,
*/

var CBModelImporter = {

    /**
     * This function can be called by an import admin page before
     * DOMContentLoaded to make sure that even the first task processing request
     * will focus on the model import tasks.
     */
    initBeforeDOMContentLoaded: function () {
        Colby.CBTasks2_processID = CBModelImporter_processID;
        Colby.CBTasks2_delay = 0;
    },
};
