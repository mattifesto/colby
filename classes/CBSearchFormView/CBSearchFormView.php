<?php

final class CBSearchFormView {

    /* -- CBHTMLOutput interfaces -- */



    static function CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v643.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBView interfaces -- */



    /**
    * @param object $viewModel
    *
    * @return
    */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $searchQuery = cb_query_string_value(
            'search-for'
        );

        $searchQueryAsHTML = cbhtml(
            $searchQuery
        );

        $formClass = (
            empty($searchQuery) ?
            'no-query' :
            'has-query'
        );

        ?>

        <div class="CBSearchFormView CBUI_view">
            <form
                action="/search/"
                class="CBUI_viewContent <?= $formClass ?>"
            >
                <div>
                    <input
                        type="text"
                        name="search-for"
                        value="<?= $searchQueryAsHTML ?>"
                    >
                    <input
                        type="submit"
                        value="Search Now"
                    >
                </div>
            </form>
        </div>

        <?php
    }
    /* CBView_render() */

}
