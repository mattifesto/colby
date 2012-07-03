--
-- the purpose of this file is that the developer should be able to type
--
-- SOURCE setup.sql
--
-- inside of mysql to set up a new Colby project
-- so the code in this file must always run
-- and always be necessary for a minimum Colby installation
--


-- Database Character Set and Collation ------------------------------
--
-- I started getting some errors about mismatched collations
-- it turns out that when functions and procedures are created
-- they use the database character set and collation
-- I often don't have the ability to set these at database creation time
-- especially with a web hosting service
-- but these database variables do need to be set correctly
-- before the functions and procedures are created

ALTER DATABASE
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

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
-- the IGNORE keyword prevents the function from reporting an error if a
-- sequence with the provided name already exists - in this case the
-- procedure will do nothing
--

DROP PROCEDURE IF EXISTS CreateSequence//

CREATE PROCEDURE CreateSequence(IN sequenceName VARCHAR(50))
BEGIN
    INSERT IGNORE INTO `ColbySequences`
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
-- DROP PROCEDURE in addtion to DROP FUNCTION
-- because this used to be a procedure
--

DROP PROCEDURE IF EXISTS GetNextInsertIdForSequence//
DROP FUNCTION IF EXISTS GetNextInsertIdForSequence//

CREATE FUNCTION GetNextInsertIdForSequence
(
    theSequenceName VARCHAR(50)
)
RETURNS BIGINT UNSIGNED
BEGIN
    UPDATE `ColbySequences`
    SET
        `id` = LAST_INSERT_ID(`id` + 1)
    WHERE
        `name` = theSequenceName;

    RETURN LAST_INSERT_ID();
END//

--
--
--

DROP FUNCTION IF EXISTS GetUserIdWithFacebookId//

CREATE FUNCTION GetUserIdWithFacebookId
(
    theFacebookId BIGINT UNSIGNED
)
RETURNS BIGINT UNSIGNED
BEGIN
    DECLARE theUserId BIGINT UNSIGNED DEFAULT NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND BEGIN END;

    SELECT
        `id` INTO theUserId
    FROM
        `ColbyUsers`
    WHERE
        `facebookId` = theFacebookId;

    RETURN theUserId;
END//

--
--
--

DROP FUNCTION IF EXISTS LoginFacebookUser;

CREATE FUNCTION LoginFacebookUser
(
    theFacebookId BIGINT UNSIGNED,
    theFacebookAccessToken VARCHAR(255),
    theFacebookAccessExpirationTime INT UNSIGNED,
    theFacebookName VARCHAR(100),
    theFacebookFirstName VARCHAR(50),
    theFacebookLastName VARCHAR(50),
    theFacebookTimeZone TINYINT
)
RETURNS BIGINT UNSIGNED
BEGIN
    DECLARE theUserId BIGINT UNSIGNED DEFAULT NULL;

    SELECT
        IFNULL
        (
            GetUserIdWithFacebookId(theFacebookId),
            GetNextInsertIdForSequence('ColbyUsersId')
        )
    INTO
        theUserId;

    INSERT INTO `ColbyUsers`
    (
        `id`,
        `facebookId`,
        `facebookAccessToken`,
        `facebookAccessExpirationTime`,
        `facebookName`,
        `facebookFirstName`,
        `facebookLastName`,
        `facebookTimeZone`
    )
    VALUES
    (
        theUserId,
        theFacebookId,
        theFacebookAccessToken,
        theFacebookAccessExpirationTime,
        theFacebookName,
        theFacebookFirstName,
        theFacebookLastName,
        theFacebookTimeZone
    )
    ON DUPLICATE KEY UPDATE
        `facebookAccessToken` = theFacebookAccessToken,
        `facebookAccessExpirationTime` = theFacebookAccessExpirationTime,
        `facebookName` = theFacebookName,
        `facebookFirstName` = theFacebookFirstName,
        `facebookLastName` = theFacebookLastName,
        `facebookTimeZone` = theFacebookTimeZone;

    RETURN theUserId;
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
