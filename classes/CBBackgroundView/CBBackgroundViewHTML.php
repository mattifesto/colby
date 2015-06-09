<?php

$styles     = array();
$styles[]   = "display: flex; display: -ms-flexbox; display: -webkit-flex;";
$styles[]   = "justify-content: center; -ms-flex-pack: center; -webkit-justify-content: center;";
$styles[]   = "flex-wrap: wrap; -ms-flex-wrap: wrap; -webkit-flex-wrap: wrap;";

if ($model->imageURL) {
    $styles[] = "background-image: url({$model->imageURLHTML});";
    $styles[] = "background-position: center top;";

    if ($model->imageShouldRepeatVertically) {
        if ($model->imageShouldRepeatHorizontally) {
            $repeat = "repeat";
        } else {
            $repeat = "repeat-y";
        }
    } else if ($model->imageShouldRepeatHorizontally) {
        $repeat = "repeat-x";
    } else {
        $repeat = "no-repeat";
    }

    $styles[]   = "background-repeat: {$repeat};";
}

if (!empty($model->color)) {
    $styles[] = "background-color: {$model->colorHTML};";
}

if ($model->minimumViewHeightIsImageHeight) {
    $styles[] = "min-height: {$model->imageHeight}px;";
}

$styles = implode(' ', $styles);

?>

<div class="CBBackgroundView" style="<?php echo $styles; ?>">
    <?php array_walk($model->children, 'CBView::renderModelAsHTML'); ?>
</div>
