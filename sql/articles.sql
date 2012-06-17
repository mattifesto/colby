--
-- right now this file contains only experimental SQL
-- it's not used currently
-- TODO: figure out the fate of this stuff
--

-- Articles ----------------------------------------------------------

CREATE TABLE IF NOT EXISTS `Articles`
(
    `id` BIGINT UNSIGNED NOT NULL,
    `stub` VARCHAR(50) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `created` DATETIME NOT NULL,
    `published` DATETIME NOT NULL,
    `updated` DATETIME NOT NULL,
    `author` BIGINT UNSIGNED NOT NULL,
    `headline` TEXT NOT NULL,
    `subhead` TEXT,
    `excerpt` TEXT,
    `content` LONGTEXT,
    `keywords` TEXT NOT NULL,
    `isPublished` BIT(1) NOT NULL DEFAULT b'0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `stub` (`stub`),
    KEY `created` (`created`),
    KEY `isPublished_published` (`isPublished`, `published`),
    CONSTRAINT `article_author` FOREIGN KEY (`author`)
        REFERENCES `ColbyUsers` (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;


-- Articles id sequence ----------------------------------------------

CALL CreateSequence('ArticlesId');
