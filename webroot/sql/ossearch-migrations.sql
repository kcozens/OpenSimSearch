#This file updates the tables used by OpenSimSearch to the latest schema.
#Use this file if you are updating an existing installation of the search
#module. If you are doing a first time install, use the ossearch.sql file.

#SVN revision 126
BEGIN;
ALTER TABLE `parcelsales` CHANGE `mature` `mature` varchar(32) NOT NULL DEFAULT 'PG';
COMMIT;

#SVN revision 142
BEGIN;
ALTER TABLE `hostsregister` DROP `lastcheck`;
ALTER TABLE `hostsregister` ADD `nextcheck` int(10) NOT NULL AFTER `register`;
ALTER TABLE `hostsregister` ADD `checked` tinyint(1) NOT NULL AFTER `nextcheck`;
ALTER TABLE `hostsregister` CHANGE `failcounter` `failcounter` int(10) NOT NULL;
COMMIT;

#SVN revision 149
BEGIN;
ALTER TABLE `events` DROP `mature`;
ALTER TABLE `events` CHANGE `eventflags` `eventflags` tinyint(1) NOT NULL;
ALTER TABLE `parcels` ADD `mature` VARCHAR( 10 ) NOT NULL;
ALTER TABLE `parcelsales` CHANGE `mature` `mature` VARCHAR( 10 ) NOT NULL DEFAULT 'PG';
ALTER TABLE `popularplaces` CHANGE `has_picture` `has_picture` tinyint(1) NOT NULL;
COMMIT;

#SVN revision 153
BEGIN;
ALTER TABLE `parcels` CHANGE `mature` `mature` VARCHAR( 10 ) NOT NULL DEFAULT 'PG';
COMMIT;

#SVN revision 154
BEGIN;
ALTER TABLE `events` CHANGE `dateUTC` `dateUTC` int(10) NOT NULL;
ALTER TABLE `events` CHANGE `covercharge` `covercharge` tinyint(1) NOT NULL;
COMMIT;
