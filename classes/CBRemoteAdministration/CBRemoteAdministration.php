<?php

/**
 * @deprecated Remove this class once install has been run once on each site.
 */
final class CBRemoteAdministration {

    /**
     * @return null
     */
    static function CBInstall_install() {
        CBModels::deleteByID('9893b4a401686ac0e85707b6c19a01405481cc38');
    }
}
