CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `facebookId` BIGINT UNSIGNED NOT NULL,
    `facebookAccessToken` VARCHAR(255),
    `facebookAccessExpirationTime` INT UNSIGNED,
    `facebookName` VARCHAR(100) NOT NULL,
    `facebookFirstName` VARCHAR(50) NOT NULL,
    `facebookLastName` VARCHAR(50) NOT NULL,
    `facebookTimeZone` TINYINT NOT NULL DEFAULT '0',
    `capabilities` VARCHAR(255),
    PRIMARY KEY (`facebookId`),
    KEY `facebookLastName` (`facebookLastName`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ColbySequences`
(
  `name` VARCHAR(50) NOT NULL,
  `id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`name`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
