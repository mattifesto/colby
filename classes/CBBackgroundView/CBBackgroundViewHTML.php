<?php

$styles = array();
$styles[] = "position: relative;";

if ($model->imageURL)
{
    $styles[] = "background-image: url({$model->imageURLHTML});";
    $styles[] = "background-position: center top;";

    if ($model->imageShouldRepeatVertically)
    {
        if ($model->imageShouldRepeatHorizontally)
        {
            $repeat = "repeat";
        }
        else
        {
            $repeat = "repeat-y";
        }
    }
    else if ($model->imageShouldRepeatHorizontally)
    {
        $repeat = "repeat-x";
    }
    else
    {
        $repeat = "no-repeat";
    }

    $styles[]   = "background-repeat: {$repeat};";
}

if (!empty($model->color))
{
    $styles[] = "background-color: {$model->colorHTML};";
}

if ($model->minimumViewHeightIsImageHeight)
{
    $styles[] = "min-height: {$model->imageHeight}px;";
}

$styles = implode(' ', $styles);

?>

<div class="CBBackgroundView" style="<?php echo $styles; ?>">

    <?php

    if ($model->linkURL)
    {
        $anchorStyles   = array();
        $anchorStyles[] = "bottom: 0px;";
        $anchorStyles[] = "left: 0px;";
        $anchorStyles[] = "position: absolute;";
        $anchorStyles[] = "right: 0px;";
        $anchorStyles[] = "top: 0px;";
        $anchorStyles   = implode(' ', $anchorStyles);

        echo "<a href=\"{$model->linkURLHTML}\" style=\"{$anchorStyles}\"></a>";
    }

    array_walk($model->children, 'CBView::renderModelAsHTML');

    ?>

</div>
