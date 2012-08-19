--
-- Table structure for table `formations_platal`
--
CREATE TABLE IF NOT EXISTS `formations_platal` (
  `formation_id` int(10) NOT NULL,
  `year` year(4) NOT NULL,
  PRIMARY KEY (`formation_id`, `year`),
  KEY `formation_id` (`formation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

