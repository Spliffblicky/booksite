-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 11:37 AM
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
-- Database: `booksite`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `userid` int(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`userid`, `street`, `city`, `state`, `zip`) VALUES
(1101, 'Powen Street', 'Accra', 'Greater Accra', 'P.O Box 2920');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `bookname` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  `supplier_id` int(100) NOT NULL,
  `date_added` datetime NOT NULL,
  `quantity` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bookname`, `author`, `description`, `price`, `image_path`, `book_id`, `supplier_id`, `date_added`, `quantity`) VALUES
('The Bright Book of Life', 'Brian Cranston', 'This books tells of the wonders and trials in life that are meant to make you a better person', 200.00, 'images/real1.jfif', 1, 103, '2025-11-19 11:54:49', 46),
('How To Read A Book', 'Andy Gray', 'A book to give you better reading skills and enhance your reading comprehension', 150.00, 'images/real2.jfif', 2, 103, '2025-11-19 11:57:28', 50),
('The Book That Can Read Your Mind', 'Jeffery D Morgan', 'A book that expands your mental capabilities through your reading style and  speed to help you understand your mind through its ability to read your mind', 75.00, 'images/real3.jfif', 3, 103, '2025-12-11 11:39:42', 70),
('What Doesnt Kill You', 'Wolfgang Mozart', 'A book that educates people on what it is that can take your life also teaching you some first aid skills', 80.00, 'images/real4.jfif', 4, 103, '2025-12-11 11:39:42', 55),
('The Maid', 'Nita Prose', 'A book that tells you how what it is a young person goes through as a ,aid for one of the richest family in the neighbourhood the struggles upsets and controversies that come with it', 65.00, 'images/real5.jfif', 5, 103, '2025-12-11 11:47:07', 85),
('A Court of Thornes and Roses', 'Sarah J Maas', 'This book tell you about a young lady in a court in the old times what it took to be a lady at court the struggles of not knowing whether the decison you took on someones life was the right one or not', 45.00, 'images/real6.jfif', 6, 103, '2025-12-11 11:47:07', 55),
('The Shortest Way Home', 'Miriam Parker', 'A group of children lose their parents in a tragic accident in the will of their parents they were asked to spread the ashes at home not knowing what that means the children now have to find HOME', 75.00, 'images/real7.jfif', 7, 103, '2025-12-11 11:53:57', 92);

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `supplierchecker` BEFORE INSERT ON `books` FOR EACH ROW IF NEW.supplier_id IS NOT NULL AND
   (SELECT role FROM users WHERE userid = NEW.supplier_id) <> 'supplier' THEN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'supplier_id must reference a user with role = supplier';
END IF
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update` BEFORE INSERT ON `books` FOR EACH ROW IF NEW.supplier_id IS NOT NULL AND
       (SELECT role FROM users WHERE userid = NEW.supplier_id) <> 'supplier' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'supplier_id must reference a user with role = supplier';
    END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(100) NOT NULL,
  `order_type` enum('admin','user') NOT NULL,
  `userid` int(100) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','completed','rejected') NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_type`, `userid`, `order_date`, `status`, `amount`) VALUES
(1, 'user', 1101, '2025-12-12 16:23:38', 'pending', 1200.00),
(2, 'user', 1101, '2025-12-12 16:25:21', 'pending', 1200.00),
(3, 'user', 1101, '2025-12-12 16:40:30', 'pending', 800.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_items_id` int(100) NOT NULL,
  `order_id` int(100) NOT NULL,
  `book_id` int(100) NOT NULL,
  `oi_quantity` int(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_items_id`, `order_id`, `book_id`, `oi_quantity`, `price`) VALUES
(1, 3, 1, 4, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `userid` int(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` int(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','supplier') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `userid`, `email`, `phone`, `password`, `role`) VALUES
('Ebenezer', 1, 'lotreresty@gmail.com', 591701507, '$2y$10$4G9/iSBPt1dg2eE3l0M24u5RGbYp0CBYgLfqIuYVkLwT51QVlXNDy', 'admin'),
('World books', 103, 'worldbooks@gmail.com', 509660643, '$2y$10$zpwB4ZCwzIvWOWq8bnDaqO/pTh1Ft9bbOSM52LOpCBzEbsd6vQ6My', 'supplier'),
('eashiley1', 1101, 'tungstenashman@gmail.com', 268458239, '$2y$10$tIxMe758yuE7/SPfsEJg6O4ZCC4jLEovVYGe6kCIIcMM9MJkI4UCC', 'user'),
('well ultimate', 1102, 'earwoman73@gmail.com', 591701507, '$2y$10$kgEKASIYYwU5Q9VWpWlxyu.jHduXabjEd1QhDw6eDxdBfkvr/s6Ia', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD KEY `a.userid` (`userid`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `o.user_id` (`userid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_items_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_items_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1103;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`userid`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
