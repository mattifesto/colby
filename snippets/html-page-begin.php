<?php

global $searchQueryHTML;

$titleHTML = $this->titleHTML;
$descriptionHTML = $this->descriptionHTML;
$searchQueryHTML = isset($searchQueryHTML) ? $searchQueryHTML : '';

$stubs = ColbyRequest::decodedStubs();

$homeSelectedClass = isset($stubs[0]) ? '' : 'class="selected"';
$searchSelectedClass = (isset($stubs[0]) && $stubs[0] == 'search') ? 'class="selected"' : '';
$blogSelectedClass = (isset($stubs[0]) && $stubs[0] == 'blog') ? 'class="selected"' : '';

if (ColbyRequest::$archive)
{
    $documentGroupId = ColbyRequest::$archive->valueForKey('documentGroupId');
    $documentTypeId = ColbyRequest::$archive->valueForKey('documentTypeId');

    $documentTypeStyleSheetURL = Colby::findFileForDocumentType('view.css', $documentGroupId, $documentTypeId,
                                                                Colby::returnURL);
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titleHTML; ?></title>

        <meta name="description"
              content="<?php echo $descriptionHTML; ?>">

        <link rel="stylesheet"
              type="text/css"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/equalize.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/shared.css">

        <?php if (isset($documentTypeStyleSheetURL)) { ?>

            <link rel="stylesheet"
                  type="text/css"
                  href="<?php echo $documentTypeStyleSheetURL; ?>">

        <?php } ?>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/html5shiv.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyEqualize.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbySheet.js"></script>
    </head>
    <body>
        <nav class="menubar">
            <ul class="horizontal">
                <li><a href="<?php echo COLBY_SITE_URL; ?>"
                       <?php echo $homeSelectedClass; ?>>Home</a></li>
                <li><a href="<?php echo COLBY_SITE_URL; ?>/search/"
                       <?php echo $searchSelectedClass; ?>>Search</a></li>
                <li><a href="<?php echo COLBY_SITE_URL; ?>/blog/"
                       <?php echo $blogSelectedClass; ?>>Blog</a></li>

                <?php

                if ($userRow = ColbyUser::userRow())
                {
                    if ($userRow->hasBeenVerified)
                    {
                        ?>

                        <li><a href="<?php echo COLBY_SITE_URL; ?>/admin/">Admin</a></li>

                        <?php
                    }

                    ?>

                    <li><?php echo $userRow->facebookName; ?></li>

                    <?php
                }

                ?>

                <li><?php echo ColbyUser::loginHyperlink(); ?></li>
            </ul>
        </nav>
