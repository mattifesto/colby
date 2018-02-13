<?php

final class CBViewTests {

    /**
     * This test runs a CBView::render() test for all known classes.
     *
     * @NOTE 2018.02.13
     *
     *      This test exposes the awkwardness in trying to render a view for
     *      purposes other than rendering it to the current page. The renderg of
     *      views below may be affecting the CBHTMLOutput state.
     *
     *      We can't use CBHTMLOutput::begin() and reset() because begin() will
     *      set an exception handler. It's not vitally important, but I created
     *      this note for future reference.
     */
    static function renderTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $spec = (object)[
                'className' => $className,
            ];

            try {
                ob_start();

                $model = CBView::render($spec);

                ob_end_clean();
            } catch (Throwable $throwable) {
                ob_end_clean();

                throw $throwable;
            }
        }
    }
}
