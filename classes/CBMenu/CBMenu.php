<?php

final class
CBMenu
{
    // -- CBModel interfaces



    /**
     * @param object $spec
     *
     *      {
     *          items: ?[model]
     *          title: ?string
     *          titleURI: ?string
     *      }
     *
     * @return object|null
     */
    static function
    CBModel_build(
        stdClass $spec
    ): ?stdClass
    {
        $model =
        (object)
        [
            'title' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'title'
                )
            ),

            'titleURI' =>
            trim(
                CBModel::valueToString(
                    $spec,
                    'titleURI'
                )
            ),
        ];


        /* menu items */

        $menuItemSpecs =
        CBMenu::getMenuItems(
            $spec
        );

        $menuItemModels =
        array_map(

            function (
                $menuItemSpec
            ) // -> object
            {
                return
                CBModel::build(
                    $menuItemSpec
                );
            },

            $menuItemSpecs

        );

        CBMenu::setMenuItems(
            $model,
            $menuItemModels
        );


        /* done */

        return
        $model;
    }
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass
    {
        $spec->items =
        array_values(
            array_filter(
                array_map(

                    'CBModel::upgrade',

                    CBModel::valueToArray(
                        $spec,
                        'items'
                    )

                )
            )
        );

        return
        $spec;
    }
    /* CBModel_upgrade() */



    /* -- accessors -- */



    /**
     * @param object $menuModel
     *
     * @return [object]
     */
    static function
    getMenuItems(
        stdClass $menuModel
    ): array {
        return CBModel::valueToArray(
            $menuModel,
            'items'
        );
    }
    /* getMenuItems() */



    /**
     * @param object $menuModel
     * @param [object] $menuItemModels
     *
     * @return void
     */
    static function
    setMenuItems(
        stdClass $menuModel,
        array $menuItemModels
    ): void {
        $menuModel->items = $menuItemModels;
    }
    /* setMenuItems() */



    /**
     * @param object $menuSpec
     *
     * @return string
     */
    static function
    getTitle(
        stdClass $menuSpec
    ): string {
        return CBModel::valueToString(
            $menuSpec,
            'title'
        );
    }
    /* getTitle() */



    /**
     * @param object $menuSpec
     * @param string $title
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $menuSpec,
        string $title
    ): void {
        $menuSpec->title = $title;
    }
    /* getTitle() */



    /**
     * @param object $menuSpec
     *
     * @return string
     */
    static function
    getTitleURL(
        stdClass $menuSpec
    ): string {
        return CBModel::valueToString(
            $smenuSpecpec,
            'titleURI'
        );
    }
    /* getTitle() */



    /**
     * @param object $menuSpec
     * @param string $title
     *
     * @return void
     */
    static function
    setTitleURL(
        stdClass $menuSpec,
        string $titleURL
    ): void {
        $menuSpec->titleURI = $titleURL;
    }
    /* getTitle() */



    /* -- functions -- */



    /**
     * If an item with the same name as the provided item already exists in the
     * menu, the provided item will replace that item. If no item with the same
     * name exists in the menu, the provided item will be appended to the menu.
     *
     * @param model $menu
     * @param model $item
     *
     * @return void
     */
    static function addOrReplaceItem(stdClass $menu, stdClass $item): void {
        $items = CBModel::valueToArray($menu, 'items');
        $name = CBModel::valueToString($item, 'name');
        $index = CBModel::indexOf($items, 'name', $name);

        if ($index === null) {
            array_push($items, $item);
        } else {
            $items[$index] = $item;
        }

        $menu->items = $items;
    }



    /**
     * If an item with the provided name exists in the menu it will be removed.
     *
     * @param model $menu
     * @param string $name
     *
     * @return void
     */
    static function removeItemByName(stdClass $menu, string $name): void {
        $items = CBModel::valueToArray($menu, 'items');
        $index = CBModel::indexOf($items, 'name', $name);

        if ($index !== null) {
            unset($items[$index]);
            $menu->items = array_values($items);
        }
    }

    /**
     * @param mixed $model
     * @param string $selectedMenuItemName
     *
     * @return object|null
     */
    static function selectedMenuItem($model, $selectedMenuItemName): ?stdClass {
        $items = CBConvert::valueToArray(CBModel::value($model, 'items'));

        foreach ($items as $item) {
            $name = CBConvert::valueToString(CBModel::value($item, 'name'));

            if ($name === $selectedMenuItemName) {
                return $item;
            }
        }

        return null;
    }

}
