--
-- the purpose of this file is that the developer should be able to type
--
-- SOURCE setup.sql
--
-- inside of mysql to set up a new Colby project
-- so the code in this file must always run
-- and always be necessary for a minimum Colby installation
--

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

CREATE TABLE IF NOT EXISTS `ColbySequences`
(
  `name` VARCHAR(50) NOT NULL,
  `id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`name`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

-- `name` values should be formatted like --> TableNameFieldName


-- stored procedures -------------------------------------------------

DELIMITER //

--
--
--

DROP PROCEDURE IF EXISTS CreateSequence//

CREATE PROCEDURE CreateSequence(IN sequenceName VARCHAR(50))
BEGIN
    INSERT INTO `ColbySequences`
        (
            `name`,
            `id`
        )
        VALUES
        (
            sequenceName,
            '0'
        );
END//

--
--
--

DROP PROCEDURE IF EXISTS GetNextInsertIdForSequence//

CREATE PROCEDURE GetNextInsertIdForSequence
(
    IN sequenceName VARCHAR(50),
    OUT insertId BIGINT UNSIGNED
)
BEGIN
    UPDATE `ColbySequences`
    SET
        `id` = LAST_INSERT_ID(`id` + 1)
    WHERE
        `name` = sequenceName;

    SELECT LAST_INSERT_ID() INTO insertId;
END//

--
--
--

DROP PROCEDURE IF EXISTS ShowUnverifiedUsers//

CREATE PROCEDURE ShowUnverifiedUsers()
BEGIN
    SELECT
        `id`,
        `facebookname`
    FROM
        `ColbyUsers`
    WHERE
        `hasBeenVerified` = b'0';
END//

--
--
--

DROP PROCEDURE IF EXISTS VerifyUser//

CREATE PROCEDURE VerifyUser(IN userId BIGINT UNSIGNED)
BEGIN
    UPDATE `ColbyUsers`
    SET
        `hasBeenVerified` = b'1'
    WHERE
        `id` = userId;
END//

DELIMITER ;


-- create ColbyUsersId sequence

CALL CreateSequence('ColbyUsersId');
