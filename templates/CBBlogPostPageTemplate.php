<?php

include_once CBSystemDirectory . '/groups/CBBlogPostsGroup.php';
include_once CBSystemDirectory . '/sections/CBStandardPageHeaderSection.php';
include_once CBSystemDirectory . '/sections/CBStandardPageFooterSection.php';
include_once CBSystemDirectory . '/templates/CBBlankPageTemplate.php';


define('CBBlogPostPageTemplateID', '3765a8af2aa44c6d514407e60a43ad93e46c8776');

$model                  = json_decode(CBBlankPageTemplateModelJSON);
$model->groupID         = CBBlogPostsGroupID;
$model->sections[] = json_decode(CBStandardPageHeaderSectionModelJSON);
$model->sections[] = json_decode(CBStandardPageFooterSectionModelJSON);

define('CBBlogPostPageTemplateModelJSON', json_encode($model));
