<?php

class CBContainerView extends CBView {

    /**
     * @return void
     */
    public function renderHTML() {

        include __DIR__ . '/CBContainerViewHTML.php';
    }
}
