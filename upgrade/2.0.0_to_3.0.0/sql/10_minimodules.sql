SET NAMES 'utf8';

DROP TABLE IF EXISTS minimodules;
CREATE TABLE IF NOT EXISTS minimodules (
  `name` varchar(100) NOT NULL,
  label varchar(200) DEFAULT NULL,
  description text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS users_minimodules;
CREATE TABLE IF NOT EXISTS users_minimodules (
  uid int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  col enum('COL_LEFT','COL_MIDDLE','COL_RIGHT','COL_FLOAT') NOT NULL DEFAULT 'COL_FLOAT',
  `row` tinyint(4) NOT NULL,
  UNIQUE KEY uid (uid,`name`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO minimodules (`name`, `label`, description) VALUES('activity', 'Activités du jour', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('birthday', 'Anniversaires', 'Les anniversaires 42');
INSERT INTO minimodules (`name`, `label`, description) VALUES('days', 'Fêtes', 'Les fêtes des saints (et des autres)');
INSERT INTO minimodules (`name`, `label`, description) VALUES('debug', 'Debug', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('groups', 'Mes groupes', 'Liste de ses groupes');
INSERT INTO minimodules (`name`, `label`, description) VALUES('ik', 'IK de la semaine', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('jtx', 'Video', 'La video du jour par le jtx');
INSERT INTO minimodules (`name`, `label`, description) VALUES('links', 'Liens utiles', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('meteo', 'Météo', 'Météo du platal');
INSERT INTO minimodules (`name`, `label`, description) VALUES('qdj', 'Question Du Jour', 'Politique ou juste comique, réponds à la Question Du Jour');
INSERT INTO minimodules (`name`, `label`, description) VALUES('quicksearch', 'TOL', 'bluh');
INSERT INTO minimodules (`name`, `label`, description) VALUES('stats', 'Statistiques', 'Pour les admins');
INSERT INTO minimodules (`name`, `label`, description) VALUES('todo', 'To-Do', 'Une liste de choses à faire');
INSERT INTO minimodules (`name`, `label`, description) VALUES('news', 'Annonces', 'D\'un seul coup d\'oeil les annonces non-lues ou suivies');
