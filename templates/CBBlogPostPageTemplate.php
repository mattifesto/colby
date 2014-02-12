<?php

define('CBBlogPostPageTemplateID', '3765a8af2aa44c6d514407e60a43ad93e46c8776');

$model                  = json_decode(CBBlankPageTemplateModelJSON);
$model->groupID         = CBBlogPostsGroupID;
$model->sectionModels[] = json_decode(CBStandardPageHeaderSectionModelJSON);
$model->sectionModels[] = json_decode(CBStandardPageFooterSectionModelJSON);

define('CBBlogPostPageTemplateModelJSON', json_encode($model));
