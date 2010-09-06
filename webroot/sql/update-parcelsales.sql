ALTER TABLE `parcels` ADD `mature` VARCHAR( 10 ) NOT NULL DEFAULT 'PG';
ALTER TABLE `parcelsales` CHANGE `mature` `mature` VARCHAR( 10 ) NOT NULL DEFAULT 'PG';
