CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `facebookId` BIGINT UNSIGNED NOT NULL,
    `facebookAccessToken` VARCHAR(255),
    `facebookAccessExpirationTime` INT UNSIGNED,
    `facebookName` VARCHAR(100) NOT NULL,
    `facebookFirstName` VARCHAR(50) NOT NULL,
    `capabilities` VARCHAR(255),
    PRIMARY KEY (`facebookId`)
)
DEFAULT CHARSET=utf8
ENGINE=InnoDB;
