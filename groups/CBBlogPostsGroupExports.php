<?php

include_once CBSystemDirectory . '/groups/CBBlogPostsGroup.php';

$descriptor = new stdClass();

$descriptor->name        = 'Blog Posts';
$descriptor->URIPrefix   = 'blog';

CBHTMLOutput::exportListItem('CBPageGroupDescriptors', CBBlogPostsGroupID, $descriptor);
