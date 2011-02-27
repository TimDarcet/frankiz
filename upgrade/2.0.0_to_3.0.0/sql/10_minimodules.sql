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

INSERT INTO minimodules (`name`, `label`, description) VALUES('activity', 'Activités du jour', 'Vue rapide sur les activités de la journée');
INSERT INTO minimodules (`name`, `label`, description) VALUES('birthday', 'Anniversaires', 'Parce que tout le monde peut jouer au Binet Niversaire');
INSERT INTO minimodules (`name`, `label`, description) VALUES('days', 'Fêtes', 'Les fêtes des saints (et des autres)');
INSERT INTO minimodules (`name`, `label`, description) VALUES('debug', 'Debug', '');
INSERT INTO minimodules (`name`, `label`, description) VALUES('groups', 'Binets & groupes', 'Liste de ses binets et de ses groupes');
INSERT INTO minimodules (`name`, `label`, description) VALUES('ik', 'IK de la semaine', 'Accès rapide à l\'InfoKès');
INSERT INTO minimodules (`name`, `label`, description) VALUES('jtx', 'Video', 'Le JTX vous présente ses meilleures vidéos');
INSERT INTO minimodules (`name`, `label`, description) VALUES('links', 'Liens utiles', 'Accès rapides aux sites utiles');
INSERT INTO minimodules (`name`, `label`, description) VALUES('meteo', 'Météo', 'Météo du platal (rarement bonne)');
INSERT INTO minimodules (`name`, `label`, description) VALUES('qdj', 'Question Du Jour', 'Politique ou juste comique, réponds à la Question Du Jour');
INSERT INTO minimodules (`name`, `label`, description) VALUES('quicksearch', 'TOL', 'Rercherche rapide sur le tol ou le wikix');
INSERT INTO minimodules (`name`, `label`, description) VALUES('todo', 'To-Do', 'Une liste de choses à faire');
INSERT INTO minimodules (`name`, `label`, description) VALUES('news', 'Annonces', 'D\'un seul coup d\'oeil les annonces non-lues ou suivies');
