#The forum struct definition
#Add NOT NULL ?
#We need MyISAM for the LINESTRING trick
#is the LINESTRING trick needed ?

DROP TABLE IF EXISTS forum_topic;
CREATE TABLE forum_topic (
    id INT PRIMARY KEY AUTO_INCREMENT,
    root_id INT,
#other
    title TINYTEXT CHARSET utf8 COLLATE utf8_general_ci
) DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS forum_nodes;
CREATE TABLE forum_nodes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    root_id INT,

    L INT,
    R INT,
    box LINESTRING not NULL,
    depth INT,

    content_id INT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#DROP INDEX forum_nodes_index_L;
CREATE INDEX forum_nodes_index_L ON forum_nodes(L);
#DROP INDEX forum_nodes_index_R;
CREATE INDEX forum_nodes_index_R ON forum_nodes(R);
#DROP INDEX forum_nodes_index_boxe;
CREATE SPATIAL INDEX forum_nodes_index_box ON forum_nodes(box);

DROP TABLE IF EXISTS forum_content;
CREATE TABLE forum_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    node_id INT,

    title TINYTEXT CHARSET utf8 COLLATE utf8_general_ci,
    message TEXT CHARSET utf8 COLLATE utf8_general_ci,
    author_id INT,

    last_modification_date TIMESTAMP, #automatically updated
    creation_date TIMESTAMP
) DEFAULT CHARSET=utf8;


DELIMITER $$

DROP PROCEDURE IF EXISTS temp_whatever $$
CREATE PROCEDURE temp_whatever(IN a INT) BEGIN
END$$

DROP PROCEDURE IF EXISTS forum_nodes_insert $$
CREATE PROCEDURE forum_nodes_insert(IN parent_id INT, IN id_content INT, OUT new_id INT)
BEGIN
    DECLARE parent_R INT;
    DECLARE parent_depth INT;
    DECLARE parent_root_id INT DEFAULT NULL;

    START TRANSACTION;
       SELECT R, depth, root_id INTO parent_R, parent_depth, parent_root_id FROM forum_nodes WHERE id = parent_id;

       IF ISNULL(parent_root_id) THEN
            INSERT INTO forum_nodes SET L = 0, R = 1, box = LINESTRING(POINT(-1, 0), POINT(1, 1)), depth = 0, content_id = id_content;
            SELECT LAST_INSERT_ID() into new_id;
            UPDATE forum_nodes SET root_id = @new_id WHERE id = new_id;
        ELSE
            UPDATE forum_nodes SET box = LINESTRING(POINT(-1, L+2), POINT(1, R)), L = L + 2 WHERE L >= parent_R AND root_id = parent_root_id;
            UPDATE forum_nodes SET box = LINESTRING(POINT(-1, L), POINT(1, R+2)), R = R + 2 WHERE R >= parent_R AND root_id = parent_root_id;
            INSERT INTO forum_nodes SET L = parent_R, R = parent_R + 1,
                box = LINESTRING(POINT(-1, parent_R), POINT(1, parent_R + 1)),
                depth = parent_depth + 1, content_id = id_content;
            SELECT LAST_INSERT_ID() into new_id;
        END IF;
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS forum_nodes_remove $$
CREATE PROCEDURE forum_nodes_remove(IN node_id INT)
BEGIN
    DECLARE node_R INT;
    DECLARE node_L INT;
    DECLARE node_root_id INT DEFAULT NULL;
    DECLARE delta INT;

    START TRANSACTION;
        SELECT L, R, root_id INTO node_L, node_R, node_root_id FROM forum_nodes WHERE id = node_id;
        IF !ISNULL(node_root_id) THEN
            SET delta = node_R - node_L + 1;
            DELETE n, c FROM forum_nodes AS n INNER JOIN forum_content AS c ON n.id = c.node_id WHERE n.L >= node_L AND n.R <= node_R AND n.root_id = node_root_id;
            UPDATE forum_nodes SET box = LINESTRING(POINT(-1, L - delta), POINT(1, R)), L = L - delta WHERE L >= node_L and root_id = node_root_id;
            UPDATE forum_nodes SET box = LINESTRING(POINT(-1, L), POINT(1, R - delta)), R = R - delta WHERE R >= node_R and root_id = node_root_id;
        END IF;
    COMMIT;
END$$

DELIMITER ;
