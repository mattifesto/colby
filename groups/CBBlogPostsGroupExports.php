<?php

include_once CBSystemDirectory . '/groups/CBBlogPostsGroup.php';

$descriptor = new stdClass();

$descriptor->name        = 'Blog Posts';
$descriptor->URIPrefix   = CBBlogPostsGroupURIPrefix;

CBHTMLOutput::exportListItem('CBPageGroupDescriptors', CBBlogPostsGroupID, $descriptor);
