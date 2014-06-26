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

        <div class="CBLogInLink">
            <a href="<?php echo ColbyUser::loginURL(); ?>">Log in</a>
        </div>

        <?php
    }

    ?>

</main>
