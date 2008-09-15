-- Host: localhost:3308
-- Generatie Tijd: 15 Sept 2008 om 21:27
-- Server versie: 5.0.27
-- PHP Versie: 5.2.3
--
-- Database: `ossearch`
--

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `hostsregister`
--

CREATE TABLE `hostsregister` (
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `register` int(10) NOT NULL,
  `lastcheck` int(10) NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY  (`host`,`port`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

