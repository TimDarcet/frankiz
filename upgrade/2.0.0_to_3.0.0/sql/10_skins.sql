SET NAMES 'utf8';


DROP TABLE IF EXISTS skins;
CREATE TABLE IF NOT EXISTS skins (
  `name` varchar(40) CHARACTER SET utf8 NOT NULL,
  label varchar(100) CHARACTER SET utf8 NOT NULL,
  description tinytext CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  visibility tinyint(1) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO skins (`name`, `label`, description, `date`, visibility) VALUES('default', 'Défaut', 'La skin par défaut de Frankiz3', '2008-08-10', 1);
INSERT INTO skins (`name`, `label`, description, `date`, visibility) VALUES('default.mobile', 'Smartphone', 'Cette skin permet d''accéder rapidement et facilement au trombinoscope sur votre téléphone mobile', '2008-08-16', 0);

