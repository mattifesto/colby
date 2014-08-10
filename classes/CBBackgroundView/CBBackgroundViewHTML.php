<?php

$styles = array();
$styles[] = "position: relative;";

if ($this->model->imageURL)
{
    $styles[] = "background-image: url({$this->model->imageURLHTML});";
    $styles[] = "background-position: center top;";

    if ($this->model->imageShouldRepeatVertically)
    {
        if ($this->model->imageRepeatHorizontally)
        {
            $repeat = "repeat";
        }
        else
        {
            $repeat = "repeat-y";
        }
    }
    else if ($this->model->imageShouldRepeatHorizontally)
    {
        $repeat = "repeat-x";
    }
    else
    {
        $repeat = "no-repeat";
    }

    $styles[]   = "background-repeat: {$repeat};";
}

if (!empty($this->model->color))
{
    $styles[] = "background-color: {$this->model->colorHTML};";
}

if ($this->model->minimumViewHeightIsImageHeight)
{
    $styles[] = "min-height: {$this->model->imageHeight}px;";
}

$styles = implode(' ', $styles);

?>

<div class="CBBackgroundView" style="<?php echo $styles; ?>">

    <?php

    if ($this->model->linkURL)
    {
        $anchorStyles   = array();
        $anchorStyles[] = "bottom: 0px;";
        $anchorStyles[] = "left: 0px;";
        $anchorStyles[] = "position: absolute;";
        $anchorStyles[] = "right: 0px;";
        $anchorStyles[] = "top: 0px;";
        $anchorStyles   = implode(' ', $anchorStyles);

        echo "<a href=\"{$this->model->linkURLHTML}\" style=\"{$anchorStyles}\"></a>";
    }

    global $CBHackSectionedPagesPageModel;

    CBSectionedPageRenderSections($this->model->children, $CBHackSectionedPagesPageModel);

    ?>

</div>
