<?php

final class CBMenuViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUISelector',
            'CBUIStringEditor',
        ];
    }

    /**
     * @return string
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v446.1.js', cbsysurl()),
        ];
    }

    /**
     * @return [[string, any]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBMenuViewEditor_menuOptions', CBMenuViewEditor::fetchMenuOptions()],
            ['CBMenuViewEditor_menuItemOptionsByMenuID', CBMenuViewEditor::fetchMenuItemOptionsByMenuID()],
        ];
    }

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
                    'title' => $item->text,
                    'value' => $item->name,
                ];
            }

            $menuItemOptionsByMenuID[$menu->ID] = $options;
        }

        return $menuItemOptionsByMenuID;
    }

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

        array_unshift($options, (object)[
            'title' => 'None',
        ]);

        return $options;
    }
}
