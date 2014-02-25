<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';

$dataStore          = new CBDataStore($pageModel->dataStoreID);
$dataStoreURL       = $dataStore->URL();
$backgroundImageURL = "{$dataStoreURL}/{$sectionModel->imageFilename}";

if ($sectionModel->imageRepeatVertically)
{
    if ($sectionModel->imageRepeatHorizontally)
    {
        $repeat = "repeat";
    }
    else
    {
        $repeat = "repeat-y";
    }
}
else if ($sectionModel->imageRepeatHorizontally)
{
    $repeat = "repeat-x";
}
else
{
    $repeat = "no-repeat";
}

$styles = array();
$styles[]   = "background-image: url({$backgroundImageURL});";
$styles[]   = "background-position: center top;";
$styles[]   = "background-repeat: {$repeat};";

if (!empty($sectionModel->backgroundColor))
{
    $backgroundColorHTML = ColbyConvert::textToHTML($sectionModel->backgroundColor);

    $styles[] = "background-color: {$backgroundColorHTML};";
}

if ($sectionModel->minimumSectionHeightIsImageHeight)
{
    $styles[] = "min-height: {$sectionModel->imageSizeY}px;";
}

$styles     = implode(' ', $styles);

?>

<div style="<?php echo $styles; ?>">
