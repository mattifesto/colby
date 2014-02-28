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
$styles[]   = "position: relative;";

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

    <?php

    if ($sectionModel->linkURL)
    {
        $URL = $sectionModel->linkURL;

        $anchorStyles   = array();
        $anchorStyles[] = "bottom: 0px; ";
        $anchorStyles[] = "left: 0px;";
        $anchorStyles[] = "position: absolute;";
        $anchorStyles[] = "right: 0px;";
        $anchorStyles[] = "top: 0px;";

        $anchorStyles = implode(' ', $anchorStyles);

        echo "<a href=\"{$URL}\" style=\"{$anchorStyles}\"></a>";
    }

    CBSectionedPageRenderSections($sectionModel->children, $pageModel);

    ?>

</div>
