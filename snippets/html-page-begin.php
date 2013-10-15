<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */

global $searchQueryHTML;
$searchQueryHTML = isset($searchQueryHTML) ? $searchQueryHTML : '';

$stubs = ColbyRequest::decodedStubs();

$homeSelectedClass = isset($stubs[0]) ? '' : 'class="selected"';
$searchSelectedClass = (isset($stubs[0]) && $stubs[0] == 'search') ? 'class="selected"' : '';
$blogSelectedClass = (isset($stubs[0]) && $stubs[0] == 'blog') ? 'class="selected"' : '';

if (ColbyRequest::$archive)
{
    $documentGroupId = ColbyRequest::$archive->valueForKey('documentGroupId');
    $documentTypeId = ColbyRequest::$archive->valueForKey('documentTypeId');

    $documentTypeStyleSheetURL = Colby::findFileForDocumentType('view.css',
                                                                $documentGroupId,
                                                                $documentTypeId,
                                                                Colby::returnURL);

    if ($documentTypeStyleSheetURL)
    {
        $this->cssFilenames[] = $documentTypeStyleSheetURL;
    }
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $this->titleHTML; ?></title>
        <meta name="description" content="<?php echo $this->descriptionHTML; ?>">

        <?php foreach ($this->cssURLs as $cssURL) { ?>

            <link rel="stylesheet" href="<?php echo $cssURL; ?>">

        <?php } ?>

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
