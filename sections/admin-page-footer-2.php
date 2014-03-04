<?php

CBHTMLOutput::addCSSURL(CBSystemURL . '/sections/admin-page-footer-2.css');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');

$userName       = ColbyUser::userRow()->facebookName;
$userNameHTML   = ColbyConvert::textToHTML($userName);

?>

<section class="admin-page-footer-2">
    <ul>
        <li>Copyright &copy; 2012-<?php echo gmdate('Y'); ?> Mattifesto Design</li>
        <li><?php echo $userNameHTML; ?></li>
    </ul>
</section>
