<?php

final class CBProjectionTests {

    static function test() {
        $projection = CBProjection::withSize(100, 200);
        $projection = CBProjection::scale($projection, 0.5);

        if ($projection->destination->width != 50 || $projection->destination->height != 100) {
            throw new RuntimeException('CBProjection::scale() test failed. Projection: ' . json_encode($projection));
        }

        $projection = CBProjection::withSize(100, 200);
        $projection = CBProjection::applyOpString($projection, "s0.5");

        if ($projection->destination->width != 50 || $projection->destination->height != 100) {
            throw new RuntimeException('CBProjection::applyOpString(..., "s0.5") test failed. Projection: ' . json_encode($projection));
        }

    }
}
