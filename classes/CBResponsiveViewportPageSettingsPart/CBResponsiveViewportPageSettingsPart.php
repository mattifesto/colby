<?php

final class CBResponsiveViewportPageSettingsPart {

    /**
     * @return void
     */
    static function CBPageSettings_renderHeadElementHTML(): void {
        ?><meta name="viewport" content="width=device-width, initial-scale=1"><?php
    }
}
