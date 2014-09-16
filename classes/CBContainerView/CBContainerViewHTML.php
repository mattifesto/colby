<div class="CBContainerView">

    <?php

    foreach ($this->subviews as $subview) {

        $subview->renderHTML();
    }

    ?>

</div>
