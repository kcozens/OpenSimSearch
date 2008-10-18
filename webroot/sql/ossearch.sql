-- phpMyAdmin SQL Dump
-- version 2.7.0-beta1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 17 Oct 2008 om 23:42
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.3
-- 
-- Database: `ossearch`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `allparcels`
-- 

CREATE TABLE `allparcels` (
  `regionUUID` varchar(255) NOT NULL,
  `parcelname` varchar(255) NOT NULL,
  `ownerUUID` char(36) NOT NULL default '00000000-0000-0000-0000-000000000000',
  `groupUUID` char(36) NOT NULL default '00000000-0000-0000-0000-000000000000',
  `landingpoint` varchar(255) NOT NULL,
  `parcelUUID` char(36) NOT NULL default '00000000-0000-0000-0000-000000000000',
  `infoUUID` char(36) NOT NULL default '00000000-0000-0000-0000-000000000000',
  PRIMARY KEY  (`regionUUID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `classifieds`
-- 

CREATE TABLE `classifieds` (
  `ClassifiedID` int(11) NOT NULL,
  `CreatorID` varchar(20) NOT NULL,
  `CreationDate` int(20) NOT NULL,
  `ExpirationDate` int(20) NOT NULL,
  `Category` varchar(20) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Desc` text NOT NULL,
  `ParcelID` varchar(20) NOT NULL,
  `ParentEstate` int(11) NOT NULL,
  `SnapshotID` varchar(20) NOT NULL,
  `SimName` varchar(255) NOT NULL,
  `PosGlobal` varchar(255) NOT NULL,
  `ClassifiedFlags` int(8) NOT NULL,
  `PriceForListing` int(5) NOT NULL,
  PRIMARY KEY  (`ClassifiedID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `events`
-- 

CREATE TABLE `events` (
  `OwnerID` varchar(36) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `EventID` int(11) NOT NULL,
  `Creator` varchar(255) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `Desc` text NOT NULL,
  `Date` varchar(20) NOT NULL,
  `DateUTC` int(10) NOT NULL,
  `Duration` int(10) NOT NULL,
  `Cover` int(10) NOT NULL,
  `Amount` int(10) NOT NULL,
  `SimName` varchar(255) NOT NULL,
  `GlobalPos` varchar(255) NOT NULL,
  `UnixTime` int(10) NOT NULL,
  `EventFlags` int(10) NOT NULL,
  `Mature` enum('false','true') NOT NULL,
  PRIMARY KEY  (`EventID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `hostsregister`
-- 

CREATE TABLE `hostsregister` (
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `register` int(10) NOT NULL,
  `lastcheck` int(10) NOT NULL,
  PRIMARY KEY  (`host`,`port`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `objects`
-- 

CREATE TABLE `objects` (
  `objectuuid` varchar(255) NOT NULL,
  `parceluuid` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `regionuuid` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`objectuuid`,`parceluuid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `parcels`
-- 

CREATE TABLE `parcels` (
  `regionUUID` varchar(255) NOT NULL,
  `parcelname` varchar(255) NOT NULL,
  `parcelUUID` varchar(255) NOT NULL,
  `landingpoint` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `searchcategory` varchar(50) NOT NULL,
  `build` enum('true','false') NOT NULL,
  `script` enum('true','false') NOT NULL,
  `public` enum('true','false') NOT NULL,
  `dwell` float NOT NULL default '0',
  `infouuid` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`regionUUID`,`parcelUUID`),
  KEY `name` (`parcelname`),
  KEY `description` (`description`),
  KEY `searchcategory` (`searchcategory`),
  KEY `dwell` (`dwell`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `parcelsales`
-- 

CREATE TABLE `parcelsales` (
  `regionUUID` varchar(255) NOT NULL,
  `parcelname` varchar(255) NOT NULL,
  `parcelUUID` varchar(255) NOT NULL,
  `area` int(6) NOT NULL,
  `saleprice` int(11) NOT NULL,
  `landingpoint` varchar(255) NOT NULL,
  `infoUUID` char(36) NOT NULL default '00000000-0000-0000-0000-000000000000',
  `dwell` int(11) NOT NULL,
  `parentestate` int(11) NOT NULL default '1',
  `mature` varchar(32) NOT NULL default 'false',
  PRIMARY KEY  (`regionUUID`,`parcelUUID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `popularplaces`
-- 

CREATE TABLE `popularplaces` (
  `parcelUUID` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dwell` float NOT NULL,
  `infoUUID` char(36) NOT NULL,
  `has_picture` tinyint(4) NOT NULL,
  `mature` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `regions`
-- 

CREATE TABLE `regions` (
  `regionname` varchar(255) NOT NULL,
  `regionuuid` varchar(255) NOT NULL,
  `regionhandle` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `owneruuid` varchar(255) NOT NULL,
  PRIMARY KEY  (`regionuuid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
