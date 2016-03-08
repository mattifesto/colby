<div class="flex-fill"></div>
<section class="CBAdminPageFooterView">
    <ul>
        <li>Copyright &copy; 2012-<?php echo gmdate('Y'); ?> Mattifesto Design</li>

        <?php

        if (ColbyUser::current()->isLoggedIn()) {
            $logoutURLForHTML   = ColbyConvert::textToHTML(ColbyUser::logoutURL());
            $userName           = ColbyUser::userRow()->facebookName;
            $userNameHTML       = ColbyConvert::textToHTML($userName);

            ?>

            <li><?php echo $userNameHTML; ?></li>
            <li><a href="<?php echo $logoutURLForHTML; ?>">log out</a></li>

            <?php
        }

        ?>

    </ul>
</section>
