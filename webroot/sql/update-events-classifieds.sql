-- phpMyAdmin SQL Dump
-- version 2.7.0-beta1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generatie Tijd: 26 Oct 2008 om 21:49
-- Server versie: 5.0.51
-- PHP Versie: 5.2.4-2ubuntu5.3
-- 
-- Database: `ossearch`
-- 

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `classifieds`
-- 

DROP TABLE IF EXISTS `classifieds`;
CREATE TABLE `classifieds` (
  `classifieduuid` char(36) NOT NULL,
  `creatoruuid` char(36) NOT NULL,
  `creationdate` int(20) NOT NULL,
  `expirationdate` int(20) NOT NULL,
  `category` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `parceluuid` char(36) NOT NULL,
  `parentestate` int(11) NOT NULL,
  `snapshotuuid` char(36) NOT NULL,
  `simname` varchar(255) NOT NULL,
  `posglobal` varchar(255) NOT NULL,
  `parcelname` varchar(255) NOT NULL,
  `classifiedflags` int(8) NOT NULL,
  `priceforlisting` int(5) NOT NULL,
  PRIMARY KEY  (`classifieduuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Tabel structuur voor tabel `events`
-- 

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `owneruuid` char(40) NOT NULL,
  `name` varchar(255) NOT NULL,
  `eventid` int(11) NOT NULL,
  `creatoruuid` char(40) NOT NULL,
  `category` int(2) NOT NULL,
  `description` text NOT NULL,
  `dateUTC` int(10) NOT NULL,
  `duration` int(10) NOT NULL,
  `covercharge` int(10) NOT NULL,
  `coveramount` int(10) NOT NULL,
  `simname` varchar(255) NOT NULL,
  `globalPos` varchar(255) NOT NULL,
  `eventflags` int(10) NOT NULL,
  `mature` enum('true','false') NOT NULL,
  PRIMARY KEY  (`eventid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
