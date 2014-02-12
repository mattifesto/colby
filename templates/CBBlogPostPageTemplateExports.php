<?php

include_once CBSystemDirectory . '/templates/CBBlogPostPageTemplate.php';


$descriptor             = new stdClass();
$descriptor->name       = 'Blog Post';
$descriptor->modelJSON  = json_encode($model);

CBHTMLOutput::exportListItem('CBPageTemplateDescriptors', CBBlogPostPageTemplateID, $descriptor);
