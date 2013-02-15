<main>

    <?php

    if (ColbyUser::current()->isLoggedIn())
    {
        ?>

        <p>You are not authorized to view this page.

        <?php
    }
    else
    {
        ?>

        <p>You must be logged in to view this page.

        <div><a href="<?php echo ColbyUser::loginURL(); ?>">Log In</a></div>

        <?php
    }

    ?>

</main>
