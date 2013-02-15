<?php

// mise en place

$titleHTML = $this->titleHTML;
$descriptionHTML = $this->descriptionHTML;

$stubs = ColbyRequest::decodedStubs();

$adminSelectedClass = (isset($stubs[0]) && $stubs[0] == 'admin') ? 'class="selected"' : '';

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
              href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/equalize.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/shared.css">

        <link rel="stylesheet"
              type="text/css"
              href="<?php echo COLBY_SITE_URL; ?>/colby/css/admin.css">

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/html5shiv.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/Colby.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyFormManager.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbySheet.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyXMLHttpRequest.js"></script>

        <script src="<?php echo COLBY_SITE_URL; ?>/colby/javascript/ColbyFormData.js"></script>
    </head>
    <body>
        <nav class="menubar">
            <ul class="horizontal">
                <li><a href="<?php echo COLBY_SITE_URL; ?>">Home</a></li>
                <li><a href="<?php echo COLBY_SITE_URL; ?>/admin/"
                       <?php echo $adminSelectedClass; ?>>Admin</a></li>

                <?php

                    if ($userRow = ColbyUser::userRow())
                    {
                        ?>

                        <li><?php echo $userRow->facebookName; ?></li>

                        <?php
                    }

                ?>

                <li><?php echo ColbyUser::loginHyperlink(); ?></li>
            </ul>
        </nav>
        <section>
            <nav class="menu-column">
                <ul>

                    <?php

                    if (ColbyUser::current()->isOneOfThe('Administrators'))
                    {
                        ?>

                        <li><h1>Administrators</h1></li>

                        <?php

                        $adminSections = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,*.php');
                        $adminSections = array_merge($adminSections, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,*.php'));

                        $adminSections = preg_grep('/handle,admin,[^,]*.php$/', $adminSections);

                        foreach ($adminSections as $adminSection)
                        {
                            preg_match('/handle,admin,(.*).php$/', $adminSection, $matches);

                            echo "<li><a href=\"/admin/{$matches[1]}/\">{$matches[1]}</a></li>\n";
                        }

                        ?>

                        <li><h1>Help</h1></li>

                        <?php

                        $helpPages = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,help,*.php');
                        $helpPages = array_merge($helpPages, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,help,*.php'));

                        $helpPages = preg_grep('/handle,admin,help,[^,]*.php$/', $helpPages);

                        foreach ($helpPages as $helpPage)
                        {
                            preg_match('/handle,admin,help,(.*).php$/', $helpPage, $matches);

                            $helpPageStub = $matches[1];

                            echo "<li><a href=\"/admin/help/{$helpPageStub}/\">{$helpPageStub}</a></li>\n";
                        }
                    }

                    if (ColbyUser::current()->isOneOfThe('Developers'))
                    {
                        ?>

                        <li><h1>Developers</h1></li>

                        <?php

                        $developerSections = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,developer,*.php');
                        $developerSections = array_merge($developerSections, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,developer,*.php'));

                        // Reduce list to only files matching the exact pattern of developer admin section pages.

                        $developerSections = preg_grep('/handle,developer,[^,]*.php$/', $developerSections);

                        foreach ($developerSections as $developerSection)
                        {
                            preg_match('/handle,developer,(.*).php$/', $developerSection, $matches);

                            echo "<li><p><a href=\"/developer/{$matches[1]}/\">{$matches[1]}</a></li>\n";
                        }
                    }

                    ?>

                </ul>
            </nav>
