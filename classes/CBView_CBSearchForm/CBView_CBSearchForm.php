<?php

final class CBView_CBSearchForm {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.9.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        $viewSpec
    ): stdClass {
        return (object)[];
    }
    /* CBModel_build() */



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

        <div class="CBView_CBSearchForm CBUI_view">
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
