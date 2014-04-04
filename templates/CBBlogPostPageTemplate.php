<?php

include_once CBSystemDirectory . '/groups/CBBlogPostsGroup.php';
include_once CBSystemDirectory . '/sections/CBStandardPageHeaderSection.php';
include_once CBSystemDirectory . '/sections/CBStandardPageFooterSection.php';
include_once CBSystemDirectory . '/templates/CBBlankPageTemplate.php';


define('CBBlogPostPageTemplateID', '3765a8af2aa44c6d514407e60a43ad93e46c8776');

global $CBSections;

$model             = json_decode(CBBlankPageTemplateModelJSON);
$model->groupID    = CBBlogPostsGroupID;
$model->sections[] = json_decode($CBSections[CBStandardPageHeaderSectionTypeID]->modelJSON);
$model->sections[] = json_decode($CBSections[CBStandardPageFooterSectionTypeID]->modelJSON);

define('CBBlogPostPageTemplateModelJSON', json_encode($model));
