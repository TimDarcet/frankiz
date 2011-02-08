SET NAMES 'utf8';

DROP TABLE IF EXISTS links;
CREATE TABLE IF NOT EXISTS links (
  id int(11) NOT NULL AUTO_INCREMENT,
  image int(11) DEFAULT NULL,
  link tinytext NOT NULL,
  label tinytext NOT NULL,
  description text NOT NULL,
  `comment` text NOT NULL,
  ns enum('partners','usefuls') NOT NULL,
  rank int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(1, NULL, 'home/contact', 'Contacter les élèves', '', '', 'usefuls', 0);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(2, NULL, 'home/howtocome', 'Venir à l\'X', '', '', 'usefuls', 1);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(3, NULL, 'links/partners', 'Partenariats', '', '', 'usefuls', 2);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(4, NULL, 'http://www.polytechnique.edu', 'Site de l\'École', '', '', 'usefuls', 3);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(5, NULL, 'ttp://www.etudes.polytechnique.edu', 'Site de la DE', '', '', 'usefuls', 4);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(6, NULL, 'http://enex.polytechnique.fr', 'ENEX', '', '', 'usefuls', 5);
INSERT INTO links (id, image, link, `label`, description, `comment`, ns, rank) VALUES(7, NULL, 'http://www.polytechnique.fr/sites/orientation4a/pages_orientation/', 'Orientation 4eme année', '', '', 'usefuls', 6);
