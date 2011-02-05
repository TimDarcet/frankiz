DROP TABLE IF EXISTS days;
CREATE TABLE IF NOT EXISTS days (
  `name` varchar(255) DEFAULT NULL,
  `day` tinyint(4) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  KEY `day` (`day`),
  KEY `month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `days` (`name`, `day`, `month`) VALUES
('Juste', 14, 10),
('Aaron', 1, 7),
('Abel', 5, 8),
('Abélard', 5, 8),
('Abella', 5, 8),
('Abigaïl', 30, 7),
('Abraham', 29, 12),
('Achille', 12, 5),
('Adalbert', 15, 11),
('Adélaïde', 16, 12),
('Adèle', 24, 12),
('Adélie', 24, 12),
('Adélina', 24, 12),
('Adeline', 20, 10),
('Adélita', 24, 12),
('Adelphe', 11, 9),
('Adhémar', 29, 5),
('Adolphe', 30, 6),
('Adoucha', 16, 12),
('Adrian', 8, 9),
('Adrien', 8, 9),
('Adrienne', 8, 9),
('Agata', 5, 2),
('Agathe', 5, 2),
('Agnès', 21, 1),
('Agneta', 21, 1),
('Agrippine', 23, 6),
('Ahmed', 21, 8),
('Aimable', 18, 10),
('Aimé', 13, 9),
('Aimée', 20, 2),
('Aissa', 2, 1),
('Alain', 9, 9),
('Alaric', 15, 11),
('Alary', 15, 11),
('Alban', 22, 6),
('Albanne', 22, 6),
('Albéric', 15, 11),
('Albert', 15, 11),
('Alberta', 15, 11),
('Alberte', 15, 11),
('Albertine', 15, 11),
('Albin', 22, 6),
('Aldric', 4, 4),
('Alèthe', 4, 4),
('Alex', 30, 8),
('Alexandra', 22, 4),
('Alexandre', 22, 4),
('Alexia', 9, 1),
('Alexine', 2, 4),
('Alexis', 17, 2),
('Alfaric', 15, 11),
('Alfred', 15, 8),
('Alfrédine', 15, 8),
('Alice', 16, 12),
('Alicia', 16, 12),
('Alida', 26, 4),
('Aliette', 16, 12),
('Alison', 16, 12),
('Alissa', 16, 12),
('Alix', 9, 1),
('Alizé', 16, 12),
('Allen', 9, 1),
('Alma', 18, 10),
('Aloïs', 21, 6),
('Aloysia', 15, 3),
('Alphonse', 1, 8),
('Alphonsine', 1, 8),
('Alrick', 4, 4),
('Amadeo', 30, 3),
('Amadeus', 30, 3),
('Amael', 24, 5),
('Amalia', 19, 9),
('Amalric', 15, 1),
('Amance', 4, 11),
('Amand', 6, 2),
('Amanda', 4, 11),
('Amanda', 8, 6),
('Amandine', 9, 7),
('Amaury', 15, 1),
('Ambroise', 7, 12),
('Amédée', 30, 3),
('Amélia', 19, 9),
('Amélie', 19, 9),
('Ameline', 19, 9),
('Amery', 4, 11),
('Amour', 9, 8),
('Anaïs', 26, 7),
('Anastase', 10, 3),
('Anastasie', 10, 3),
('Anatole', 3, 2),
('André', 30, 11),
('Andréa', 30, 11),
('Andrée', 30, 11),
('Andrew', 30, 11),
('Anémone', 5, 10),
('Ange', 5, 5),
('Angéla', 27, 1),
('Angèle', 27, 1),
('Angélica', 27, 1),
('Angelina', 27, 1),
('Angéline', 27, 1),
('Angélique', 27, 1),
('Angelo', 5, 5),
('Anicet', 17, 4),
('Anita', 26, 7),
('Anna', 26, 7),
('Annaïk', 26, 7),
('Anne', 26, 7),
('Anne-Marie', 9, 6),
('Annette', 26, 7),
('Annick', 26, 7),
('Annie', 26, 7),
('Anouchka', 26, 7),
('Anouck', 26, 7),
('Anselme', 21, 4),
('Anthelme', 26, 6),
('Anthony', 17, 1),
('Antoine', 17, 1),
('Antoine', 13, 6),
('Antoine-Marie', 5, 7),
('Antoinette', 28, 2),
('Antonella', 28, 2),
('Antonin', 13, 6),
('Apollinaire', 12, 9),
('Apolline', 9, 2),
('Apollon', 9, 2),
('Archibald', 27, 3),
('Archie', 27, 3),
('Ariane', 18, 9),
('Ariel', 1, 10),
('Arielle', 1, 10),
('Aristide', 31, 8),
('Arlette', 17, 7),
('Armand', 8, 6),
('Armande', 8, 6),
('Armel', 16, 8),
('Armelle', 16, 8),
('Arnaud', 10, 2),
('Arnold', 14, 8),
('Aron', 1, 7),
('Arsène', 19, 7),
('Art', 21, 2),
('Arthur', 15, 11),
('Arthus', 15, 11),
('Astrid', 27, 11),
('Atlantide', 20, 7),
('Auban', 22, 6),
('Aubert', 15, 11),
('Aubin', 1, 3),
('Aubry', 15, 11),
('Aude', 18, 11),
('Audrey', 23, 6),
('Audric', 23, 6),
('Aufray', 15, 8),
('Aufroy', 15, 8),
('Augusta', 0, 0),
('Auguste', 0, 0),
('Augustin', 27, 5),
('Augustin', 28, 8),
('Augustine', 0, 0),
('Aure', 4, 10),
('Aurel', 16, 6),
('Aurélia', 16, 6),
('Aurélie', 16, 6),
('Aurélien', 16, 6),
('Aurélienne', 16, 6),
('Auriane', 16, 6),
('Aurora', 16, 6),
('Aurore', 16, 6),
('Austin', 0, 0),
('Axel', 22, 4),
('Axelle', 22, 4),
('Aymar', 29, 5),
('Aymeric', 4, 10),
('Aymon', 5, 10),
('Aymone', 5, 10),
('Azeline', 23, 8),
('Azemar', 29, 5),
('Azilis', 22, 11),
('Babette', 4, 12),
('Baldwin', 17, 10),
('Baldwina', 17, 10),
('Baptiste', 24, 6),
('Barbara', 4, 12),
('Barbe', 4, 12),
('Barberine', 4, 12),
('Barnabé', 11, 6),
('Barthel', 24, 8),
('Barthélémy', 24, 8),
('Bartlomé', 24, 8),
('Basile', 2, 1),
('Bastien', 20, 1),
('Bathilda', 20, 4),
('Bathilde', 20, 4),
('Bathylle', 20, 4),
('Batista', 24, 6),
('Baudoin', 17, 10),
('Béatrice', 13, 2),
('Béatrix', 13, 2),
('Beatty', 13, 2),
('Béla', 15, 11),
('Bélinda', 28, 8),
('Béline', 28, 8),
('Bella', 22, 2),
('Belle', 22, 2),
('Ben', 31, 3),
('Bénédicte', 16, 3),
('Bénita', 11, 7),
('Benjamin', 31, 3),
('Benjamine', 31, 3),
('Benny', 31, 3),
('Benoît', 11, 7),
('Benoîte', 11, 7),
('Bénoni', 31, 3),
('Bérenger', 26, 5),
('Bérengère', 26, 5),
('Bérénice', 4, 2),
('Bernadette', 18, 2),
('Bernard', 20, 8),
('Bernardin', 20, 5),
('Bernardine', 20, 5),
('Bernie', 20, 8),
('Bert', 15, 11),
('Bertha', 4, 7),
('Berthe', 4, 7),
('Bertie', 4, 7),
('Bertille', 6, 11),
('Bertrand', 6, 9),
('Bertrande', 6, 9),
('Béryl', 21, 3),
('Bessie', 17, 11),
('Bethsabée', 17, 11),
('Bettina', 17, 11),
('Betty', 17, 11),
('Bianca', 3, 10),
('Biche', 13, 2),
('Bienvenue', 30, 10),
('Billy', 10, 1),
('Blaise', 3, 2),
('Blanche', 3, 10),
('Blandine', 2, 6),
('Bluette', 5, 10),
('Bonaventure', 15, 7),
('Boniface', 5, 6),
('Boris', 2, 5),
('Boriska', 2, 5),
('Brenda', 16, 5),
('Brendan', 16, 5),
('Brice', 13, 11),
('Brieuc', 1, 5),
('Brigitte', 23, 7),
('Britt', 23, 7),
('Broz', 7, 12),
('Brunehaut', 20, 4),
('Brunehilde', 20, 4),
('Bruno', 6, 10),
('Bunny', 4, 2),
('Bunny', 4, 2),
('Callixte', 14, 10),
('Camille', 14, 7),
('Candide', 3, 10),
('Capucine', 5, 10),
('Carina', 7, 11),
('Carine', 7, 11),
('Carl', 4, 11),
('Carline', 4, 11),
('Carlos', 4, 11),
('Carlotta', 4, 11),
('Carmen', 16, 7),
('Carmencita', 16, 7),
('Carmina', 18, 7),
('Carole', 17, 7),
('Caroline', 17, 7),
('Caryle', 4, 11),
('Casimir', 4, 3),
('Cassius', 13, 8),
('Catalina', 25, 11),
('Cathel', 25, 11),
('Catherine', 25, 11),
('Cathia', 25, 11),
('Cathie', 25, 11),
('Cathleen', 25, 11),
('Cattalen', 25, 11),
('Cécile', 22, 11),
('Cécilia', 22, 11),
('Cécilie', 22, 11),
('Cédric', 7, 1),
('Céleste', 14, 10),
('Célestin', 19, 5),
('Célestine', 19, 5),
('Célia', 22, 11),
('Célie', 22, 11),
('Céline', 21, 10),
('Chantal', 12, 12),
('Charlemagne', 28, 1),
('Charles', 4, 11),
('Charlie', 4, 11),
('Charlotte', 17, 7),
('Chéryl', 4, 11),
('Chloé', 27, 1),
('Christel', 24, 7),
('Christelle', 24, 7),
('Christian', 12, 11),
('Christiane', 24, 7),
('Christine', 24, 7),
('Christophe', 21, 8),
('Cindy', 13, 12),
('Claire', 11, 8),
('Clara', 11, 8),
('Clarine', 11, 8),
('Clarissa', 12, 8),
('Clarrisse', 12, 8),
('Claude', 15, 2),
('Claudette', 6, 6),
('Claudia', 6, 6),
('Claudie', 6, 6),
('Claudine', 6, 6),
('Claudius', 6, 6),
('Clélia', 13, 7),
('Clélie', 13, 7),
('Clémence', 21, 3),
('Clément', 23, 11),
('Clémentine', 23, 11),
('Clothilde', 4, 6),
('Clotilde', 4, 6),
('Clovis', 25, 8),
('Colette', 6, 3),
('Coline', 6, 12),
('Colomba', 31, 12),
('Colombe', 31, 12),
('Colombine', 31, 12),
('Côme', 26, 9),
('Conception', 15, 8),
('Conchita', 15, 8),
('Conrad', 26, 11),
('Constance', 8, 4),
('Constant', 23, 9),
('Constantin', 21, 5),
('Constanza', 23, 9),
('Cora', 18, 5),
('Coralie', 18, 5),
('Corentin', 12, 12),
('Corinne', 18, 5),
('Cornélia', 16, 9),
('Cornélie', 16, 9),
('Cosima', 26, 9),
('Curd', 26, 11),
('Cyprien', 16, 9),
('Cyprienne', 16, 9),
('Cyrielle', 18, 3),
('Cyrille', 18, 3),
('Dahlia', 5, 10),
('Daisy', 16, 11),
('Dalila', 28, 7),
('Damia', 26, 9),
('Damien', 26, 9),
('Daniel', 11, 12),
('Danièle', 11, 12),
('Danitza', 11, 12),
('Dany', 11, 12),
('Daphné', 10, 8),
('David', 29, 12),
('Davina', 29, 12),
('Davy', 20, 9),
('Déborah', 21, 9),
('Délla', 16, 12),
('Delphin', 24, 12),
('Délphine', 24, 11),
('Démétrius', 26, 10),
('Denez', 9, 10),
('Denis', 9, 10),
('Denise', 15, 5),
('Denys', 9, 10),
('Désiré', 8, 5),
('Désirée', 8, 5),
('Diana', 9, 6),
('Diane', 9, 6),
('Didier', 23, 5),
('Diego', 13, 11),
('Diether', 1, 7),
('Dietrich', 1, 7),
('Dieudonné', 10, 8),
('Dimitri', 26, 10),
('Dina', 2, 6),
('Dirk', 1, 7),
('Djihane', 30, 6),
('Dodie', 5, 6),
('Dolores', 15, 9),
('Dominique', 8, 8),
('Don', 15, 7),
('Donald', 15, 7),
('Donatella', 24, 5),
('Donatien', 24, 5),
('Donatienne', 24, 5),
('Donella', 24, 5),
('Doria', 24, 10),
('Dorine', 9, 2),
('Doris', 6, 2),
('Dorothée', 5, 6),
('Dorothy', 5, 6),
('Doryse', 9, 11),
('Edda', 16, 9),
('Eddy', 5, 1),
('Edern', 30, 8),
('Edgar', 8, 7),
('Edith', 16, 9),
('Edma', 20, 11),
('Edmée', 20, 11),
('Edmond', 20, 11),
('Edmonde', 20, 11),
('Edouard', 5, 1),
('Edwige', 16, 10),
('Edwin', 12, 10),
('Edwina', 5, 1),
('Eglantine', 23, 8),
('Eléazar', 1, 8),
('Eléonore', 25, 6),
('Eliane', 4, 7),
('Elie', 20, 7),
('Eliette', 20, 7),
('Eline', 18, 8),
('Elisa', 17, 11),
('Elisabeth', 17, 11),
('Elise', 17, 11),
('Ella', 1, 2),
('Ellénita', 1, 2),
('Elodie', 22, 10),
('Eloi', 1, 12),
('Eloïse', 25, 8),
('Elsa', 17, 11),
('Elvire', 16, 7),
('Emeline', 27, 10),
('Emeric', 4, 11),
('Emile', 22, 5),
('Emilie', 19, 9),
('Emilien', 12, 11),
('Emma', 19, 4),
('Emmanuel', 25, 12),
('Emmanuelle', 25, 12),
('Emmeline', 30, 5),
('Enguerran', 25, 10),
('Enrique', 13, 7),
('Eric', 18, 5),
('Erich', 18, 5),
('Erika', 18, 5),
('Ernest', 7, 11),
('Ernestine', 7, 11),
('Erwan', 19, 5),
('Erwin', 19, 5),
('Erwina', 19, 5),
('Esteban', 26, 12),
('Estelle', 11, 5),
('Esther', 1, 7),
('Ethel', 24, 12),
('Etienne', 26, 12),
('Etiennette', 26, 12),
('Eudes', 19, 8),
('Eudine', 19, 8),
('Eugène', 13, 7),
('Eugénie', 7, 2),
('Eulalie', 12, 2),
('Eurielle', 1, 10),
('Eusèbe', 2, 8),
('Eustache', 20, 9),
('Eva', 6, 9),
('Evelyne', 6, 9),
('Evita', 6, 9),
('Evrard', 14, 8),
('Ezékiel', 10, 4),
('Fabien', 20, 1),
('Fabiola', 27, 12),
('Fabrice', 22, 8),
('Fanny', 26, 12),
('Faustin', 15, 2),
('Faustine', 15, 12),
('Fédor', 18, 7),
('Félicie', 7, 3),
('Félicien', 9, 6),
('Félicité', 7, 3),
('Félix', 12, 2),
('Ferdinand', 30, 5),
('Fernand', 27, 6),
('Fernande', 27, 6),
('Ferréol', 16, 6),
('Fidèle', 24, 4),
('Firmin', 11, 10),
('Flavie', 7, 5),
('Flavien', 18, 2),
('Fleur', 5, 10),
('Fleurance', 4, 7),
('Flora', 24, 11),
('Flore', 4, 7),
('Florence', 1, 12),
('Florent', 4, 7),
('Florentin', 24, 10),
('Florian', 4, 5),
('Florine', 4, 7),
('Fortunat', 23, 4),
('Franç.Xavier', 3, 12),
('France', 9, 3),
('Francette', 9, 3),
('Francine', 9, 3),
('Francis', 4, 10),
('Franck', 4, 10),
('François', 24, 1),
('Françoise', 9, 3),
('Françoise-Xav', 22, 12),
('Frankie', 4, 10),
('Fred', 15, 8),
('Freddy', 18, 7),
('Frédéric', 18, 7),
('Frédérika', 18, 7),
('Frédérique', 18, 7),
('Frida', 18, 7),
('Fulbert', 10, 4),
('Gabriel', 29, 9),
('Gabriella', 29, 9),
('Gaël', 17, 12),
('Gaëlle', 17, 12),
('Gaëtan', 7, 8),
('Gaétane', 7, 8),
('Gallia', 9, 3),
('Gallina', 23, 3),
('Gaspard', 28, 12),
('Gaston', 6, 2),
('Gatien', 18, 12),
('Gautier', 9, 4),
('Gelsomina', 5, 10),
('Geneviève', 3, 1),
('Genséric', 17, 3),
('Geoffroy', 8, 11),
('Georges', 23, 4),
('Georgette', 15, 2),
('Georgia', 15, 2),
('Georgine', 15, 2),
('Gérald', 5, 2),
('Géraldine', 5, 2),
('Gérard', 3, 10),
('Géraud', 13, 10),
('Géric', 5, 11),
('Germain', 28, 5),
('Germaine', 15, 6),
('Géronima', 30, 9),
('Gersende', 17, 3),
('Gertrude', 16, 11),
('Gervais', 19, 6),
('Gervaise', 19, 6),
('Ghislain', 10, 10),
('Ghislaine', 10, 10),
('Gilbert', 7, 6),
('Gilberte', 11, 8),
('Gildas', 29, 1),
('Gilles', 1, 9),
('Gina', 21, 6),
('Ginette', 3, 1),
('Gisèle', 7, 5),
('Gladys', 29, 3),
('Glafira', 26, 4),
('Godefroy', 8, 11),
('Gontran', 28, 3),
('Gonzague', 21, 8),
('Grâce', 21, 8),
('Gracieuse', 21, 8),
('Graziella', 21, 8),
('Grégoire', 3, 9),
('Grégory', 3, 9),
('Gretchen', 20, 7),
('Grétel', 20, 7),
('Guenièvre', 3, 1),
('Guenola', 18, 10),
('Guenole', 3, 3),
('Guerric', 19, 8),
('Guewen', 18, 10),
('Guillaume', 10, 1),
('Guillaumette', 10, 1),
('Guillemette', 10, 1),
('Gunter', 9, 10),
('Gustave', 7, 10),
('Guy', 12, 6),
('Gwen', 3, 11),
('Gwenael', 3, 11),
('Gwenaelle', 3, 11),
('Gwendoline', 14, 10),
('Gwenn', 18, 10),
('Gwenola', 18, 10),
('Gwladys', 29, 3),
('Habib', 27, 3),
('Hadrien', 8, 9),
('Hannah', 26, 7),
('Hans', 27, 12),
('Harold', 1, 10),
('Haroun', 1, 7),
('Harriet', 13, 7),
('Harry', 13, 7),
('Hartmann', 23, 12),
('Hedwige', 16, 10),
('Hélène', 18, 8),
('Helga', 11, 7),
('Helmut', 20, 11),
('Héloïse', 25, 8),
('Hélyette', 20, 7),
('Hendrick', 13, 7),
('Henri', 13, 7),
('Henriette', 13, 7),
('Herbert', 20, 3),
('Hermance', 28, 8),
('Hermann', 25, 9),
('Hermeline', 16, 8),
('Hermès', 28, 8),
('Hermès', 28, 8),
('Hermine', 9, 7),
('Hervé', 17, 6),
('Hilaire', 13, 1),
('Hilda', 17, 11),
('Hildebrand', 25, 5),
('Hippolyte', 13, 8),
('Honoré', 16, 5),
('Honorine', 27, 2),
('Hortance', 5, 10),
('Hubert', 3, 11),
('Hugolina', 1, 4),
('Hugues', 1, 4),
('Huguette', 1, 4),
('Humbert', 25, 3),
('Hyacinthe', 17, 8),
('Ibrahim', 20, 12),
('Ida', 13, 4),
('Ignace', 31, 7),
('Igor', 5, 6),
('Ildefonce', 23, 1),
('Inès', 10, 9),
('Ingrid', 2, 9),
('Iold', 17, 12),
('Ionel', 11, 4),
('Iphigénie', 9, 7),
('Irène', 5, 4),
('Irénée', 28, 6),
('Iricha', 16, 4),
('Irma', 9, 7),
('Irma', 4, 9),
('Irmine', 24, 12),
('Isaac', 20, 12),
('Isabelle', 22, 2),
('Isaïc', 9, 5),
('Isaure', 17, 6),
('Isidor', 4, 4),
('Isolde', 17, 12),
('Ivan', 27, 12),
('J.Baptiste', 7, 4),
('J.Baptiste', 24, 6),
('J.Eudes', 19, 8),
('J.François-Régis', 16, 6),
('J.Marie', 4, 8),
('Jacinthe', 30, 1),
('Jack', 27, 12),
('Jacky', 25, 7),
('Jacob', 20, 12),
('Jacqueline', 8, 2),
('Jacques', 3, 5),
('Jacques', 25, 7),
('James', 25, 7),
('Janick', 30, 5),
('Janine', 27, 12),
('Jaouen', 2, 3),
('Jasmine', 5, 10),
('Jean', 27, 12),
('Jeanne', 12, 12),
('Jeannine', 12, 12),
('Jef', 19, 7),
('Jehanne', 27, 12),
('Jennifer', 12, 12),
('Jenny', 12, 12),
('Jérémie', 1, 5),
('Jérôme', 30, 9),
('Jessica', 4, 11),
('Jessy', 4, 11),
('Jim', 3, 5),
('Joachim', 26, 7),
('Jocelin', 7, 9),
('Jocelyne', 17, 12),
('Joël', 13, 7),
('Joëlle', 13, 7),
('Johanne', 30, 5),
('John', 27, 12),
('Johnny', 27, 12),
('Jonathan', 24, 6),
('Jordane', 13, 2),
('Joris', 26, 7),
('José', 19, 3),
('Joseph', 19, 3),
('Joséphine', 19, 3),
('Josette', 19, 3),
('Josiane', 19, 3),
('Josselin', 13, 12),
('Josseline', 13, 12),
('Josué', 1, 9),
('Juanita', 12, 12),
('Jude', 28, 10),
('Judicaël', 17, 12),
('Judith', 5, 5),
('Jules', 12, 4),
('Julie', 8, 4),
('Julien', 2, 8),
('Julienne', 16, 2),
('Juliette', 18, 5),
('Juliette', 30, 7),
('Justin', 1, 6),
('Justine', 12, 3),
('Kalinka', 23, 11),
('Karel', 4, 11),
('Karelle', 7, 11),
('Karen', 7, 11),
('Karin', 7, 11),
('Karina', 7, 11),
('Karine', 7, 11),
('Kassia', 13, 8),
('Katarina', 25, 11),
('Katel', 25, 11),
('Kathlène', 25, 11),
('Katia', 25, 11),
('Katiouchka', 25, 11),
('Katy', 25, 11),
('Ketty', 25, 11),
('Kevin', 3, 6),
('Kird', 31, 1),
('Klaus', 6, 12),
('Klimka', 23, 11),
('Kolia', 6, 12),
('Konrad', 26, 11),
('Kurd', 26, 11),
('Kyrill', 18, 3),
('Ladislas', 27, 7),
('Laetitia', 18, 8),
('Lambert', 17, 9),
('Larissa', 26, 3),
('Laure', 10, 8),
('Laurence', 10, 8),
('Laurent', 10, 8),
('Laurette', 10, 8),
('Lazare', 23, 2),
('Léa', 22, 3),
('Leila', 22, 3),
('Léna', 18, 8),
('Léocadie', 9, 12),
('Léon', 10, 11),
('Léonard', 6, 11),
('Léonce', 18, 6),
('Léone', 10, 11),
('Léonilde', 10, 11),
('Léontine', 10, 11),
('Léopoldine', 15, 11),
('Léopole', 15, 11),
('Leslie', 17, 11),
('Lewis', 25, 8),
('Lia', 22, 3),
('Liane', 4, 7),
('Lidwine', 14, 4),
('Lilian', 4, 4),
('Liliane', 4, 4),
('Lily', 17, 11),
('Lin', 28, 8),
('Linda', 28, 8),
('Line', 20, 10),
('Lionel', 10, 11),
('Lisbeth', 17, 11),
('Lise', 17, 11),
('Liselotte', 17, 11),
('Lisette', 17, 11),
('Lizzie', 17, 11),
('Loïc', 25, 8),
('Loïs', 21, 6),
('Lola', 15, 9),
('Lolita', 15, 9),
('Lora', 25, 6),
('Lorelei', 25, 6),
('Loren', 10, 8),
('Lothaire', 7, 4),
('Louis', 25, 8),
('Louis-Marie', 28, 8),
('Louise', 15, 3),
('Loup', 29, 7),
('Luc', 18, 10),
('Lucas', 18, 10),
('Lucette', 13, 12),
('Lucie', 13, 12),
('Lucien', 8, 1),
('Lucienne', 8, 1),
('Lucille', 16, 2),
('Lucrèce', 15, 3),
('Ludmilla', 16, 9),
('Ludovic', 25, 8),
('Ludwig', 25, 8),
('Lydia', 3, 8),
('Lydiane', 3, 8),
('Lydie', 3, 8),
('M.Dominique', 14, 5),
('M.Flore', 24, 11),
('M.France', 6, 10),
('M.Françoise', 6, 10),
('M.Josèphe', 7, 10),
('M.Madeline', 22, 7),
('M.Rose', 23, 8),
('M.Thérèse', 7, 6),
('Macha', 15, 8),
('Macolm', 21, 11),
('Macrine', 6, 7),
('Maddy', 22, 7),
('Madeleine', 22, 7),
('Maël', 24, 5),
('Maëlle', 24, 5),
('Magali', 20, 7),
('Magda', 22, 7),
('Maggy', 20, 7),
('Maïté', 20, 2),
('Malika', 7, 9),
('Mandy', 4, 11),
('Manech', 27, 12),
('Manfred', 28, 1),
('Manolita', 25, 12),
('Manoulia', 27, 3),
('Manuel', 25, 12),
('Marc', 25, 4),
('Marceau', 16, 1),
('Marcel', 16, 1),
('Marcelle', 31, 1),
('Marcellin', 6, 4),
('Marcelline', 17, 7),
('Marco', 25, 4),
('Margaret', 10, 6),
('Margarita', 16, 11),
('Margrit', 16, 11),
('Marguerite', 16, 11),
('Maria', 15, 8),
('Marianne', 9, 7),
('Mariannick', 15, 8),
('Marie', 15, 8),
('Marie-Ange', 16, 12),
('Marielle', 15, 8),
('Marietta', 6, 7),
('Mariette', 6, 7),
('Marikel', 15, 8),
('Marin', 4, 9),
('Marina', 20, 7),
('Marine', 20, 7),
('Marinette', 20, 7),
('Marion', 15, 8),
('Maritchu', 15, 8),
('Marius', 19, 1),
('Marjolaine', 15, 8),
('Marjorie', 20, 7),
('Marlène', 18, 7),
('Marthe', 19, 7),
('Martial', 30, 6),
('Martin', 11, 11),
('Martine', 30, 1),
('Maryline', 15, 8),
('Marylise', 15, 8),
('Maryse', 15, 8),
('Maryvonne', 15, 8),
('Mathilde', 14, 3),
('Mathurin', 1, 11),
('Matthias', 14, 5),
('Matthieu', 21, 9),
('Maud', 14, 3),
('Maureen', 15, 8),
('Maurice', 22, 9),
('Mauricette', 22, 9),
('Maxence', 20, 11),
('Maxime', 14, 4),
('Maximilien', 12, 3),
('Maximin', 29, 5),
('Mayeul', 11, 5),
('Médard', 8, 6),
('Médéric', 8, 6),
('Melaine', 6, 1),
('Mélanie', 26, 1),
('Mélissa', 1, 11),
('Mélusine', 6, 1),
('Mercedes', 24, 9),
('Mériadec', 7, 6),
('Michel', 29, 9),
('Michèle', 29, 9),
('Micheline', 19, 6),
('Mikaël', 29, 9),
('Milène', 18, 8),
('Millie', 14, 7),
('Milly', 19, 9),
('Mireille', 15, 8),
('Modeste', 24, 2),
('Moïse', 4, 9),
('Mona', 4, 5),
('Monessa', 4, 9),
('Monique', 27, 8),
('Morgan', 23, 10),
('Morgane', 23, 10),
('Morvan', 22, 9),
('Moshé', 4, 9),
('Muriel', 15, 8),
('Myriam', 15, 8),
('Myrtil', 5, 10),
('Nadège', 18, 9),
('Nadette', 18, 2),
('Nadia', 18, 9),
('Nadine', 18, 9),
('Nahum', 1, 12),
('Nancy', 26, 7),
('Narcisse', 29, 10),
('Natacha', 26, 8),
('Nataline', 27, 7),
('Natan', 24, 8),
('Natasha', 27, 7),
('Nathalie', 27, 7),
('Nathanaël', 24, 8),
('Nelly', 18, 8),
('Nestor', 26, 2),
('Nicolas', 6, 12),
('Nicole', 6, 3),
('Nicoletta', 6, 12),
('Nikita', 31, 1),
('Nikoucha', 8, 8),
('Nina', 14, 1),
('Nine', 25, 11),
('Ninon', 15, 12),
('Noé', 10, 11),
('Noël', 25, 12),
('Noëlle', 25, 12),
('Noémi', 14, 12),
('Nora', 25, 6),
('Norbert', 6, 6),
('Océane', 20, 7),
('Octave', 20, 11),
('Octavien', 6, 8),
('Odette', 20, 4),
('Odile', 14, 12),
('Odilon', 4, 1),
('Olaf', 29, 7),
('Olga', 11, 7),
('Olive', 5, 3),
('Olivette', 5, 3),
('Olivia', 5, 3),
('Olivier', 12, 7),
('Ombeline', 21, 8),
('Omer', 9, 9),
('Ondine', 20, 7),
('Ophélie', 1, 11),
('Oscar', 3, 2),
('Oswald', 5, 8),
('Otto', 28, 12),
('P.Damien', 21, 2),
('Pablo', 29, 6),
('Paco', 9, 3),
('Pacome', 9, 5),
('Paméla', 16, 2),
('Paola', 26, 1),
('Paquita', 9, 3),
('Paquito', 9, 3),
('Parfait', 18, 4),
('Pascal', 17, 5),
('Pascale', 17, 5),
('Patrice', 17, 3),
('Patricia', 17, 3),
('Patrick', 17, 3),
('Paul', 29, 6),
('Paula', 26, 1),
('Paule', 26, 1),
('Paulette', 26, 1),
('Paulin', 11, 1),
('Pauline', 26, 1),
('Peggy', 8, 10),
('Pélagie', 8, 11),
('Pénélope', 1, 11),
('Perrine', 31, 5),
('Pervenche', 5, 10),
('Peter', 29, 6),
('Pétronille', 31, 5),
('Philibert', 20, 8),
('Philiberte', 20, 8),
('Philippe', 3, 5),
('Philomène', 5, 7),
('Pia', 11, 7),
('Pierre', 29, 6),
('Pierrette', 31, 5),
('Pierrick', 29, 6),
('Placide', 5, 10),
('Polly', 9, 2),
('Pomeline', 20, 10),
('Prisca', 18, 1),
('Priscilla', 16, 1),
('Prosper', 25, 6),
('Prudence', 6, 5),
('Quentin', 31, 10),
('Rachel', 15, 1),
('Rachilde', 23, 11),
('Rainier', 17, 6),
('Raissa', 5, 9),
('Ralph', 21, 6),
('Raoul', 7, 7),
('Raphaël', 29, 9),
('Raphaëlla', 29, 9),
('Raymond', 7, 1),
('Raymonde', 7, 1),
('Raynald', 9, 2),
('Rebecca', 23, 3),
('Réginald', 17, 9),
('Régine', 7, 9),
('Régis', 16, 6),
('Reine', 7, 9),
('Réjane', 7, 9),
('Rémi', 15, 1),
('Rénald', 17, 9),
('Renaud', 17, 9),
('René', 19, 10),
('Rénée', 19, 10),
('Richard', 3, 4),
('Rita', 22, 5),
('Robert', 30, 4),
('Roberte', 30, 4),
('Roch', 16, 8),
('Rodolphe', 21, 6),
('Rodrigue', 13, 3),
('Roger', 30, 12),
('Roland', 15, 9),
('Rolande', 13, 5),
('Romain', 28, 2),
('Romaric', 10, 12),
('Roméo', 25, 2),
('Romuald', 19, 6),
('Ronald', 17, 9),
('Rosalie', 4, 9),
('Rosalyn', 25, 8),
('Rose', 23, 8),
('Roseline', 17, 1),
('Rosemary', 15, 8),
('Rosemonde', 30, 4),
('Rosette', 23, 8),
('Rosine', 11, 3),
('Rosita', 23, 8),
('Roxane', 23, 8),
('Rozenn', 23, 8),
('Rudy', 21, 6),
('Rufin', 14, 6),
('Ruper', 30, 4),
('Sabin', 29, 8),
('Sabine', 29, 8),
('Sabrina', 29, 8),
('Sacha', 30, 8),
('Sally', 9, 10),
('Salomé', 22, 10),
('Salomon', 26, 6),
('Salvatore', 18, 3),
('Sammy', 20, 8),
('Samson', 28, 7),
('Samuel', 20, 8),
('Sandie', 2, 4),
('Sandra', 2, 4),
('Sandrine', 2, 4),
('Sara', 9, 10),
('Saturnin', 29, 11),
('Sébastien', 20, 1),
('Sébastienne', 20, 1),
('Ségolène', 24, 7),
('Sélim', 26, 6),
('Selma', 21, 4),
('Séraphin', 12, 10),
('Serge', 7, 10),
('Sergine', 7, 10),
('Servais', 13, 5),
('Servan', 1, 7),
('Servane', 1, 7),
('Séveriane', 27, 11),
('Séverin', 27, 11),
('Séverine', 27, 11),
('Sheila', 22, 11),
('Sibilli', 9, 10),
('Sidoine', 14, 11),
('Sidonie', 14, 11),
('Siegfried', 22, 8),
('Silvère', 20, 6),
('Simon', 28, 10),
('Simone', 28, 10),
('Sisley', 22, 11),
('Sissie', 22, 11),
('Soizic', 9, 3),
('Solange', 10, 5),
('Soledad', 10, 10),
('Solène', 17, 10),
('Soméon', 18, 2),
('Sonia', 18, 9),
('Sophie', 25, 5),
('Stacey', 10, 3),
('Stanislas', 11, 4),
('Stella', 11, 5),
('Stéphane', 26, 12),
('Stéphanie', 26, 12),
('Stève', 26, 12),
('Suzanne', 11, 8),
('Suzel', 11, 8),
('Suzette', 11, 8),
('Suzy', 11, 8),
('Sveltana', 20, 3),
('Sven', 5, 12),
('Sybille', 19, 3),
('Sydney', 10, 12),
('Sylvain', 4, 5),
('Sylvaine', 4, 5),
('Sylvestre', 31, 12),
('Sylvette', 5, 11),
('Sylvia', 5, 11),
('Sylviane', 5, 11),
('Sylvic', 5, 11),
('Sylvie', 5, 11),
('Symphorien', 22, 8),
('Tamara', 1, 5),
('Tanguy', 19, 11),
('Tania', 12, 1),
('Tatiana', 12, 1),
('Teddy', 5, 1),
('Térésa', 15, 10),
('Thaddée', 28, 10),
('Thaïs', 10, 5),
('Thècle', 24, 9),
('Théodore', 9, 11),
('Théodule', 17, 2),
('Théophane', 2, 2),
('Théophile', 20, 12),
('Thérèse', 1, 10),
('Thibault', 8, 7),
('Thibaut', 8, 7),
('Thiébaud', 8, 7),
('Thierry', 1, 7),
('Thomas', 28, 1),
('Thomas', 3, 7),
('Tiburce', 11, 8),
('Tiffany', 6, 1),
('Tilda', 14, 3),
('Tino', 14, 2),
('Tiphaine', 6, 1),
('Toinette', 28, 2),
('Tony', 13, 6),
('Tracie', 15, 10),
('Tristan', 12, 11),
('Ulla', 10, 7),
('Ulrich', 10, 7),
('Ulrika', 10, 7),
('Urbain', 19, 12),
('Ursula', 21, 10),
('Ursule', 21, 10),
('Valentin', 14, 2),
('Valentine', 25, 7),
('Valère', 14, 6),
('Valériane', 28, 4),
('Valérie', 28, 4),
('Valéry', 1, 4),
('Vanessa', 4, 2),
('Vania', 27, 12),
('Vanina', 12, 12),
('Vassili', 2, 1),
('Vassily', 2, 1),
('Venceslas', 28, 9),
('Véra', 18, 9),
('Vérane', 11, 11),
('Véronique', 4, 2),
('Vianney', 4, 8),
('Vicky', 25, 8),
('Victoire', 15, 11),
('Victor', 21, 7),
('Victoria', 17, 11),
('Victorien', 23, 3),
('Victorine', 22, 1),
('Vincent', 22, 1),
('Vincent', 27, 9),
('Vincente', 4, 6),
('Vinciane', 11, 9),
('Violaine', 5, 10),
('Violaine', 25, 3),
('Violette', 5, 10),
('Virgile', 10, 10),
('Virginie', 7, 1),
('Viridiana', 1, 2),
('Viviane', 2, 12),
('Vivien', 10, 3),
('Vlassia', 11, 2),
('Walter', 9, 4),
('Werner', 19, 4),
('Wilfried', 12, 10),
('Wilhelmine', 10, 1),
('Wilhemine', 10, 1),
('William', 10, 1),
('Willy', 10, 1),
('Winceslas', 28, 9),
('Wladimir', 15, 7),
('Wolfgang', 31, 10),
('Xavérine', 22, 12),
('Xavier', 3, 12),
('Yaël', 3, 11),
('Yaëlle', 3, 11),
('Yann', 27, 12),
('Yannick', 27, 12),
('Yasmine', 1, 11),
('Yohann', 27, 12),
('Yola', 17, 12),
('Yolaine', 17, 1),
('Yolande', 11, 6),
('Youri', 23, 4),
('Youssef', 19, 7),
('Yseult', 17, 12),
('Ysolde', 17, 12),
('Yvan', 27, 12),
('Yveline', 19, 5),
('Yves', 19, 5),
('Yvette', 13, 1),
('Yvon', 19, 5),
('Yvonne', 19, 5),
('Zacharie', 5, 11),
('Zélie', 17, 10),
('Zita', 27, 4),
('Zoé', 2, 5),
('Wilhem', 10, 1);
