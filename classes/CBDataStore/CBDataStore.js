"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBDataStore */

var CBDataStore = {

    /**
     * @param ID ID
     * @param string basename
     * @param string flexdir
     *
     * @return string
     */
    flexpath: function (ID, basename, flexdir) {
        var flexpath = ID.replace(/^(..)(..)/, "data/$1/$2/");

        if (basename) {
            flexpath = flexpath + "/" + basename;
        }

        if (flexdir) {
            flexpath = flexdir + "/" + flexpath;
        }

        return flexpath;
    },
};
/* CBDataStore */
