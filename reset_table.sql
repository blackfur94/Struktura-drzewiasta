USE `countries`;

DELETE FROM `countries`;


INSERT INTO `countries` (`id`, `text`, `parent_id`) VALUES (1,'Countries',0),(2,'Europe',1),(3,'Australia',1),(4,'South America',1),(5,'North America',1),(6,'Asia',1),(7,'Africa',1),(8,'Poland',2),(9,'Warszawa',8),(10,'Lublin',8),(11,'Kraków',8),(30,'Canada',5),(31,'Mexico',5),(32,'Argentina',4),(33,'Brazil',4),(34,'Chile',4),(36,'Germany',2),(37,'France',2),(38,'Canberra',3),(39,'Sydney',3),(40,'Hong Kong',6),(41,'Japan',6),(42,'India',6),(43,'Tanzania',7),(44,'Egypt',7),(45,'Ethiopia',7),(46,'United States',5),(47,'New York',46),(48,'Washington',46),(49,'Dairut',44),(50,'Giza',44);