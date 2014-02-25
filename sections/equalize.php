<?php

/**
 * This "section" exists so that equalization remains separated from the
 * `CBHTMLOutput` class. Most page templates will include this.
 *
 * Eventually, the JavaScript files will go away as support for older browsers
 * is removed, but equalize.css does some things that will always be necessary
 * because browsers have different defaults and even the defaults they have in
 * common are not always desirable baseline styles.
 */

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/equalize.css');

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/html5shiv.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyEqualize.js');
