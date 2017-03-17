<?php

CBHTMLOutput::addCSSURL(CBSystemURL . '/sections/standard-page-footer.css');

?>

<footer class="standard-page-footer">
    <div class="copyright">
        Copyright &copy; 1984 - <?php echo gmdate('Y'), ' ', cbhtml(CBSitePreferences::siteName()); ?>
    </div>
</footer>
