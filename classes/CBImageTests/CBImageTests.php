<?php

final class CBImageTests {

    /**
     * @return void
     */
    static function upgradeTest(): void {
        $originalSpec = (object)[
            'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
            'base' => 'original',
            'extension' => 'jpeg',
            'height' => 600,
            'width' => 800,
        ];

        $expectedSpec = (object)[
            'className' => 'CBImage',
            'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
            'filename' => 'original',
            'extension' => 'jpeg',
            'height' => 600,
            'width' => 800,
        ];

        $upgradedSpec = CBImage::fixAndUpgrade($originalSpec);

        if ($upgradedSpec != $expectedSpec) {
            throw new Exception("The upgraded CBImage spec does not match what was excpected.");
        }
    }
}
