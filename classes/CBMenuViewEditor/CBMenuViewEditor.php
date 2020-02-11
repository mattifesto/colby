<?php

final class CBMenuViewEditor {

    /* -- CBHtMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v572.js', cbsysurl()),
        ];
    }



    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBMenuViewEditor_menuOptions',
                CBMenuViewEditor::fetchMenuOptions(),
            ],
            [
                'CBMenuViewEditor_menuItemOptionsByMenuID',
                CBMenuViewEditor::fetchMenuItemOptionsByMenuID(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBModel',
            'CBUI',
            'CBUISelector',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [menuID => [object]]
     */
    static function fetchMenuItemOptionsByMenuID() {
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`))
            FROM    CBModels
            WHERE   `className` = 'CBMenu'

        EOT;

        $menuIDs = CBDB::SQLToArray($SQL);
        $menus = CBModels::fetchModelsByID($menuIDs);

        $menuItemOptionsByMenuID = [];

        foreach ($menus as $menu) {
            $options = [(object)[
                'title' => 'None',
            ]];

            foreach ($menu->items as $item) {
                $options[] = (object)[
                    'title' => CBModel::valueToString($item, 'text'),
                    'value' => CBModel::valueToString($item, 'name'),
                ];
            }

            $menuItemOptionsByMenuID[$menu->ID] = $options;
        }

        return $menuItemOptionsByMenuID;
    }
    /* fetchMenuItemOptionsByMenuID() */



    /**
     * @return [object]
     */
    static function fetchMenuOptions() {
        $SQL = <<<EOT

            SELECT  `title`, LOWER(HEX(`ID`)) AS `value`
            FROM    CBModels
            WHERE   `className` = 'CBMenu'

        EOT;

        $options = CBDB::SQLToObjects($SQL);

        array_unshift(
            $options,
            (object)[
                'title' => 'None',
            ]
        );

        return $options;
    }
    /* fetchMenuOptions() */

}
/* CBMenuViewEditor */
