-- ColbyUsers --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `id` BIGINT UNSIGNED NOT NULL,
    `facebookId` BIGINT UNSIGNED NOT NULL,
    `facebookAccessToken` VARCHAR(255),
    `facebookAccessExpirationTime` INT UNSIGNED,
    `facebookName` VARCHAR(100) NOT NULL,
    `facebookFirstName` VARCHAR(50) NOT NULL,
    `facebookLastName` VARCHAR(50) NOT NULL,
    `facebookTimeZone` TINYINT NOT NULL DEFAULT '0',
    `hasBeenVerified` BIT(1) NOT NULL DEFAULT b'0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `facebookId` (`facebookId`),
    KEY `hasBeenVerified_facebookLastName`
        (`hasBeenVerified`, `facebookLastName`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;


-- ColbySequences ----------------------------------------------------

-- add a new row to the table for each sequence

CREATE TABLE IF NOT EXISTS `ColbySequences`
(
  `name` VARCHAR(50) NOT NULL,
  `id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`name`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

-- sequence name should be TableNameFieldName

-- user id sequence ----------------------------------------------

INSERT INTO `ColbySequences`
(`name`, `id`)
VALUES ('ColbyUsersId', '0');

-- to get next id in a sequence in a thread safe manner:

-- step 1: update the sequence row

UPDATE `ColbySequences`
SET `id` = LAST_INSERT_ID(`id` + 1)
WHERE `name` = 'TheSequenceName';

-- step 2: get the last insert id for the value to use

LAST_INSERT_ID();


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

INSERT INTO `ColbySequences`
(`name`, `id`)
VALUES ('ArticlesId', '0');
