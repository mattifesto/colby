<?php

$SQL = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBModels`
    (
        `ID`        BINARY(20) NOT NULL,
        `version`   BIGINT NOT NULL,
        PRIMARY KEY (`ID`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;

Colby::query($SQL);

$SQL = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBModelVersions`
    (
        `ID`            BINARY(20) NOT NULL,
        `version`       BIGINT NOT NULL,
        `modelAsJSON`   LONGTEXT NOT NULL,
        `specAsJSON`    LONGTEXT NOT NULL,
        `timestamp`     BIGINT NOT NULL,
        PRIMARY KEY     (`ID`, `version`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;

Colby::query($SQL);
