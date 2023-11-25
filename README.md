# Wega-stranka
Na spúštanie stránky používam XAMPP, pričom používam SQL databazu s menom "comments" a nasledujúcov tabuľkov:

CREATE TABLE IF NOT EXISTS `comments` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`page_id` int(11) NOT NULL,
	`parent_id` int(11) NOT NULL DEFAULT '-1',
	`name` varchar(255) NOT NULL,
	`content` text NOT NULL,
	`submit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
