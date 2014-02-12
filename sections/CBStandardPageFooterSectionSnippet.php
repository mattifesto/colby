<?php

/**
 * Most of the time sections will have static formatting, but the standard
 * header and the standard footer include the site's standard header
 * and standard footer section snippets if they exist so that sites don't have
 * to write entire new sections just to get basic functionality.
 */

include Colby::findFile('sections/standard-page-footer.php');
