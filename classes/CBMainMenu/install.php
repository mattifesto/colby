<?php

/**
 * This file is included by CBMainMenu::update()
 */

$model = CBModels::fetchModelByID(CBMainMenu::ID);

if ($model === false) {
    $home           = CBModels::modelWithClassName('CBMenuItem');
    $home->name     = 'home';
    $home->text     = 'Home';
    $home->URL      = '/';
    $spec           = CBModels::modelWithClassName('CBMenu', ['ID' => CBMainMenu::ID]);
    $spec->title    = 'Main Menu';
    $spec->items    = [$home];

    CBModels::save([$spec]);
}
