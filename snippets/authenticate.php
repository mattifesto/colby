<main>
    <style scoped>

        main
        {
            padding-top:  100px;
            text-align: center;
        }

        .big-button
        {
            margin-top: 20px;
        }

    </style>

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

        <div>
            <a href="<?php echo ColbyUser::loginURL(); ?>" class="big-button">Log In</a>
        </div>

        <?php
    }

    ?>

</main>
