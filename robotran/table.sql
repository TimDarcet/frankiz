CREATE TABLE auth (
	forlife VARCHAR(255) NOT NULL,
	mdp VARCHAR(255),
	PRIMARY KEY forlife (forlife)
);

CREATE TABLE codes (
	code VARCHAR(8) NOT NULL,
        batiment ENUM('Fayolle','PEM'),
	forlife VARCHAR(255) DEFAULT NULL,
	timestamp DATE DEFAULT NULL,
	PRIMARY KEY code (code),
	KEY forlife (forlife)
);
/* vim:set et sw=4 sts=4 sws=4 foldmethod=marker syntax=mysql: */
