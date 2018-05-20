USE `countries`;

DELETE FROM `countries`;


INSERT INTO `countries` (`id`, `text`, `parent_id`) VALUES (1,'Countries',0),(2,'Europe',1),(3,'Australia',1),(4,'South America',1),(5,'North America',1),(6,'Asia',1),(7,'Africa',1),(8,'Poland',2),(9,'Warszawa',8),(10,'Lublin',8),(11,'Kraków',8),(12,'Canada',5),(13,'Mexico',5),(14,'Argentina',4),(15,'Brazil',4),(16,'Chile',4),(17,'Germany',2),(18,'France',2),(19,'Canberra',3),(20,'Sydney',3),(21,'Hong Kong',6),(22,'Japan',6),(23,'India',6),(24,'Tanzania',7),(25,'Egypt',7),(26,'Ethiopia',7),(27,'United States',5),(28,'New York',27),(29,'Washington',27),(30,'Dairut',25),(31,'Giza',25);