<?php

$currentUserIsLoggedIn  = ColbyUser::current()->isLoggedIn();
$titleHTML              = $currentUserIsLoggedIn ? 'Authorization Failed' : 'Please Log In';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML($titleHTML);
CBHTMLOutput::setDescriptionHTML('You are not authorized to view this page.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

?>

<main>
    <style scoped>

        main
        {
            padding-top:  100px;
            text-align: center;
        }

        div.CBLogInLink
        {
            margin-top: 20px;
        }

    </style>

    <?php

    if ($currentUserIsLoggedIn)
    {
        ?>

        <p>You are not authorized to view this page.

        <?php
    }
    else
    {
        ?>

        <p>You must be logged in to view this page.

        <div class="CBLogInLink">
            <a href="<?php echo ColbyUser::loginURL(); ?>">Log in</a>
        </div>

        <?php
    }

    ?>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();
