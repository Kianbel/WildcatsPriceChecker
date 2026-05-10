-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 12:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbpricechecker`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblitem`
--

CREATE TABLE `tblitem` (
  `itemid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblitem`
--

INSERT INTO `tblitem` (`itemid`, `sid`, `itemname`, `price`) VALUES
(8, 1, 'Pencil', 10.50),
(9, 1, 'Short bondpaper', 1.00),
(10, 1, 'Long bondpaper', 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblpersonnel`
--

CREATE TABLE `tblpersonnel` (
  `empid` int(11) NOT NULL,
  `accid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpersonnel`
--

INSERT INTO `tblpersonnel` (`empid`, `accid`) VALUES
(1, 1),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `tblshop`
--

CREATE TABLE `tblshop` (
  `sid` int(11) NOT NULL,
  `empid` int(11) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `shop_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblshop`
--

INSERT INTO `tblshop` (`sid`, `empid`, `sname`, `shop_description`) VALUES
(1, 1, 'School Supplies', 'Essential stationery, papers, and campus gear.'),
(3, 2, 'Coffito', 'Hot and Cold Beverages');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `studid` int(11) NOT NULL,
  `accid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`studid`, `accid`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `accid` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`accid`, `fname`, `lname`, `email`, `password`, `usertype`) VALUES
(1, 'test', 'test', 'test@gmail.com', 'test', 'P'),
(2, 'aaa', 'aaa', 'aaa@gmail.com', 'aaa', 'S'),
(3, 'Josh', 'Shop', 'joshshop@gmail.com', '1234', 'P');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblitem`
--
ALTER TABLE `tblitem`
  ADD PRIMARY KEY (`itemid`),
  ADD KEY `fk_shop_id` (`sid`);

--
-- Indexes for table `tblpersonnel`
--
ALTER TABLE `tblpersonnel`
  ADD PRIMARY KEY (`empid`),
  ADD KEY `fk_userid` (`accid`);

--
-- Indexes for table `tblshop`
--
ALTER TABLE `tblshop`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `fk_shop_owner` (`empid`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`studid`),
  ADD KEY `fk_accid` (`accid`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`accid`),
  ADD UNIQUE KEY `unqemail` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblitem`
--
ALTER TABLE `tblitem`
  MODIFY `itemid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblpersonnel`
--
ALTER TABLE `tblpersonnel`
  MODIFY `empid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblshop`
--
ALTER TABLE `tblshop`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `studid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `accid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblitem`
--
ALTER TABLE `tblitem`
  ADD CONSTRAINT `fk_shop_id` FOREIGN KEY (`sid`) REFERENCES `tblshop` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblpersonnel`
--
ALTER TABLE `tblpersonnel`
  ADD CONSTRAINT `fk_userid` FOREIGN KEY (`accid`) REFERENCES `tbluser` (`accid`);

--
-- Constraints for table `tblshop`
--
ALTER TABLE `tblshop`
  ADD CONSTRAINT `fk_shop_owner` FOREIGN KEY (`empid`) REFERENCES `tblpersonnel` (`empid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD CONSTRAINT `fk_accid` FOREIGN KEY (`accid`) REFERENCES `tbluser` (`accid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
