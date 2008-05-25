CREATE TABLE auth (
	login_edu VARCHAR(255) NOT NULL,
	mdp VARCHAR(255),
	PRIMARY KEY login_edu (login_edu)
);

CREATE TABLE codes (
	code VARCHAR(8) NOT NULL,
        batiment ENUM('Fayolle','PEM'),
	login_edu VARCHAR(255) DEFAULT NULL,
	timestamp DATE DEFAULT NULL,
	PRIMARY KEY code (code),
	KEY login_edu (login_edu)
);
/* vim:set et sw=4 sts=4 sws=4 foldmethod=marker syntax=mysql: */
