<?php

class CBRecentlyEditedPagesView extends CBView
{
    /**
     * @return void
     */
    public function renderHTML()
    {
        include __DIR__ . '/CBRecentlyEditedPagesViewHTML.php';
    }
}
