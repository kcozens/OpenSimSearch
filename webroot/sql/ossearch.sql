-- phpMyAdmin SQL Dump
-- version 2.7.0-beta1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 06 Oct 2008 om 03:27
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
  PRIMARY KEY  (`regionUUID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `events`
-- 

CREATE TABLE `events` (
  `EventID` int(4) unsigned NOT NULL,
  `Creator` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `Desc` varchar(255) NOT NULL,
  `Date` int(4) unsigned NOT NULL,
  `DateUTC` int(4) unsigned NOT NULL,
  `Duration` int(4) unsigned NOT NULL,
  `Cover` int(4) unsigned NOT NULL,
  `Amount` int(4) unsigned NOT NULL,
  `SimName` varchar(255) NOT NULL,
  `GlobalPos` varchar(24) NOT NULL default '',
  `EventFlags` int(4) unsigned NOT NULL,
  PRIMARY KEY  (`EventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `build` enum('yes','no') NOT NULL,
  `script` enum('yes','no') NOT NULL,
  `public` enum('yes','no') NOT NULL,
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
  `forsale` int(1) NOT NULL,
  `area` int(6) NOT NULL,
  `salesprice` int(10) NOT NULL,
  PRIMARY KEY  (`regionUUID`,`parcelUUID`)
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
