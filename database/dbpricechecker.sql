-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 11:29 AM
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
(10, 1, 'Long bondpaper', 1.00),
(18, 1, 'Ballpen', 10.00),
(19, 1, 'Short Brown Envelope', 10.00),
(20, 1, 'Long Brown Envelope', 12.00),
(21, 1, 'Short Plastic Envelope', 20.00),
(22, 1, 'Long Plastic Envelope', 15.00),
(23, 1, 'Short Folder', 10.00),
(24, 1, 'Long Folder', 12.00),
(25, 1, 'Expandable Folder', 20.00),
(26, 1, 'Graphing Paper', 3.00),
(27, 1, 'Glue 240g', 50.00),
(28, 1, 'Glue Stick', 15.00),
(29, 1, 'Masking Tape', 30.00),
(30, 1, 'Scotch Tape', 30.00),
(31, 1, 'Double-sided Tape', 60.00),
(32, 1, 'Permanent Marker (Black)', 15.00),
(33, 1, 'Permanent Marker (Blue)', 15.00),
(34, 1, 'Permanent Marker (Red)', 15.00),
(35, 1, 'Whiteboard Marker (Black)', 12.00),
(36, 1, 'Whiteboard Marker (Red)', 12.00),
(37, 1, 'Whiteboard Marker (Blue)', 12.00),
(38, 4, 'Cheese Burger', 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblshop`
--

CREATE TABLE `tblshop` (
  `sid` int(11) NOT NULL,
  `accid` int(11) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `shop_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblshop`
--

INSERT INTO `tblshop` (`sid`, `accid`, `sname`, `shop_description`) VALUES
(1, 1, 'School Supplies', 'Selling papers, stationeries, etc.'),
(3, 3, 'Coffito', 'Hot and Cold Beverages'),
(4, 4, 'Burger Shop', ''),
(5, 5, 'Fruit Shop', 'Fruit shakes, mangoes, etc.');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `accid` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`accid`, `fname`, `lname`, `email`, `password`) VALUES
(1, 'test', 'test', 'test@gmail.com', 'test'),
(3, 'Josh', 'Shop', 'joshshop@gmail.com', '1234'),
(4, 't', 't', 't@gmail.com', 't'),
(5, 'a', 'a', 'a@gmail.com', 'a');

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
-- Indexes for table `tblshop`
--
ALTER TABLE `tblshop`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `fk_shop_owner_direct` (`accid`);

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
  MODIFY `itemid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tblshop`
--
ALTER TABLE `tblshop`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `accid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblitem`
--
ALTER TABLE `tblitem`
  ADD CONSTRAINT `fk_shop_id` FOREIGN KEY (`sid`) REFERENCES `tblshop` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblshop`
--
ALTER TABLE `tblshop`
  ADD CONSTRAINT `fk_shop_owner_direct` FOREIGN KEY (`accid`) REFERENCES `tbluser` (`accid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
