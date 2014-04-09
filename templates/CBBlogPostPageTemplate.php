<?php

include_once CBSystemDirectory . '/groups/CBBlogPostsGroup.php';
include_once CBSystemDirectory . '/sections/CBStandardPageHeaderSection.php';
include_once CBSystemDirectory . '/sections/CBStandardPageFooterSection.php';
include_once CBSystemDirectory . '/templates/CBBlankPageTemplate.php';

global $CBSections;


define('CBBlankBlogPostPageTemplateID', '3765a8af2aa44c6d514407e60a43ad93e46c8776');

$model             = json_decode($CBPageTemplates[CBBlankPageTemplateID]->modelJSON);
$model->groupID    = CBBlogPostsGroupID;
$model->sections[] = json_decode($CBSections[CBStandardPageHeaderSectionTypeID]->modelJSON);
$model->sections[] = json_decode($CBSections[CBStandardPageFooterSectionTypeID]->modelJSON);


$descriptor             = new stdClass();
$descriptor->name       = 'CBBlankBlogPostPage';
$descriptor->title      = 'Blank Blog Post Page';
$descriptor->modelJSON  = json_encode($model);

global $CBPageTemplates;
$CBPageTemplates[CBBlankBlogPostPageTemplateID] = $descriptor;
