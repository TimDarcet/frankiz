SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO account (uid, hruid, `password`, `group`, perms, state, `hash`, hashstamp, hash_rss, email_format, skin, firstname, lastname, nickname, email, gender, birthdate, next_birthday, cellphone, original, photo, `comment`) VALUES(-1, 'anonymous.external', '', 0, 'anonymous', 'disabled', '', '0000-00-00 00:00:00', '', 'text', 'default', '', '', '', '', 'woman', '0000-00-00', '0000-00-00', '', 0, 0, 'External anonymous user');
INSERT INTO account (uid, hruid, `password`, `group`, perms, state, `hash`, hashstamp, hash_rss, email_format, skin, firstname, lastname, nickname, email, gender, birthdate, next_birthday, cellphone, original, photo, `comment`) VALUES('0', 'anonymous.internal', '', 0, 'anonymous', 'disabled', '', '0000-00-00 00:00:00', '', 'text', 'default', '', '', '', '', 'woman', '0000-00-00', '0000-00-00', '', 0, 0, 'Internal anonymous User');


INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(0, 'days', 'COL_LEFT', 0);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(0, 'ik', 'COL_LEFT', 1);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(0, 'meteo', 'COL_MIDDLE', 0);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(0, 'jtx', 'COL_RIGHT', 1);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(0, 'quicksearch', 'COL_FLOAT', 0);


INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(-1, 'days', 'COL_FLOAT', 0);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(-1, 'meteo', 'COL_FLOAT', 1);
INSERT INTO users_minimodules (uid, `name`, col, `row`) VALUES(-1, 'timeleft', 'COL_FLOAT', 2);
