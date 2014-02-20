CREATE TABLE `nagios` (
  `service` varchar(100) NOT NULL,
  `node_id` int(11) NOT NULL,
  `last_touch` int(11) DEFAULT NULL,
  `footprint` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`service`,`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;