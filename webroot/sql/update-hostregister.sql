ALTER TABLE `hostsregister` ADD `failcounter` int(10) NOT NULL ;
ALTER TABLE `hostsregister` ADD `checked` tinyint(1) NOT NULL ;
ALTER TABLE `hostsregister` CHANGE `lastcheck` `nextcheck` INT( 10 ) NOT NULL ;
