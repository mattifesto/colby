<?php

CBHTMLOutput::addCSSURL(CBSystemURL . '/sections/admin-page-footer-2.css');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');

?>

<section class="admin-page-footer-2">
    <ul>
        <li>Copyright &copy; 2012-<?php echo gmdate('Y'); ?> Mattifesto Design</li>

        <?php

        if (ColbyUser::current()->isLoggedIn())
        {

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
