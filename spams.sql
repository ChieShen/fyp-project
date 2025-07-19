-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 01:04 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spams`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `attachName` varchar(255) NOT NULL,
  `displayName` varchar(255) NOT NULL,
  `uploader` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `attachID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatroom`
--

CREATE TABLE `chatroom` (
  `chatID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `groupID` int(11) DEFAULT NULL,
  `isGroupChat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groupmember`
--

CREATE TABLE `groupmember` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `isLeader` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `messageID` int(11) NOT NULL,
  `senderID` int(11) NOT NULL,
  `chatID` int(11) NOT NULL,
  `content` text NOT NULL,
  `sentTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `isPinned` int(11) NOT NULL,
  `editTime` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `projectID` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `deadline` datetime NOT NULL,
  `joinCode` varchar(50) NOT NULL,
  `maxMem` int(11) NOT NULL,
  `numGroup` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectgroups`
--

CREATE TABLE `projectgroups` (
  `groupID` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `groupName` varchar(255) NOT NULL,
  `submitted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `roleID` int(11) NOT NULL,
  `roleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roommember`
--

CREATE TABLE `roommember` (
  `chatID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `submissionID` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  `fileURL` varchar(255) NOT NULL,
  `submissionTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submissionName` varchar(255) NOT NULL,
  `displayName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `taskID` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `taskName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taskcontributor`
--

CREATE TABLE `taskcontributor` (
  `userID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `roleID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`attachID`),
  ADD KEY `idx_attachment_uploader` (`uploader`),
  ADD KEY `idx_attachment_project` (`projectID`);

--
-- Indexes for table `chatroom`
--
ALTER TABLE `chatroom`
  ADD PRIMARY KEY (`chatID`),
  ADD KEY `fk_group_id` (`groupID`);

--
-- Indexes for table `groupmember`
--
ALTER TABLE `groupmember`
  ADD PRIMARY KEY (`groupID`,`userID`),
  ADD KEY `idx_groupmember_groupID` (`groupID`),
  ADD KEY `idx_groupmember_userID` (`userID`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `idx_message_senderID` (`senderID`),
  ADD KEY `idx_message_chatID` (`chatID`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`projectID`);

--
-- Indexes for table `projectgroups`
--
ALTER TABLE `projectgroups`
  ADD PRIMARY KEY (`groupID`),
  ADD KEY `idx_projectgroups_projectID` (`projectID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `roommember`
--
ALTER TABLE `roommember`
  ADD KEY `idx_roommember_userID` (`userID`),
  ADD KEY `idx_roommember_chatID` (`chatID`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`submissionID`),
  ADD KEY `idx_submission_projectID` (`projectID`),
  ADD KEY `idx_submission_groupID` (`groupID`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`taskID`),
  ADD KEY `idx_task_projectID` (`projectID`),
  ADD KEY `idx_task_groupID` (`groupID`);

--
-- Indexes for table `taskcontributor`
--
ALTER TABLE `taskcontributor`
  ADD PRIMARY KEY (`userID`,`taskID`),
  ADD KEY `idx_taskcontributor_userID` (`userID`),
  ADD KEY `idx_taskcontributor_taskID` (`taskID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `idx_user_roleID` (`roleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `attachID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatroom`
--
ALTER TABLE `chatroom`
  MODIFY `chatID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `projectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projectgroups`
--
ALTER TABLE `projectgroups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `taskID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachment`
--
ALTER TABLE `attachment`
  ADD CONSTRAINT `fk_attachment_project` FOREIGN KEY (`projectID`) REFERENCES `project` (`projectID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attachment_uploader` FOREIGN KEY (`uploader`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `chatroom`
--
ALTER TABLE `chatroom`
  ADD CONSTRAINT `fk_group_id` FOREIGN KEY (`groupID`) REFERENCES `projectgroups` (`groupID`) ON DELETE CASCADE;

--
-- Constraints for table `groupmember`
--
ALTER TABLE `groupmember`
  ADD CONSTRAINT `fk_groupmember_group` FOREIGN KEY (`groupID`) REFERENCES `projectgroups` (`groupID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_groupmember_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_chat` FOREIGN KEY (`chatID`) REFERENCES `chatroom` (`chatID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_message_sender` FOREIGN KEY (`senderID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `projectgroups`
--
ALTER TABLE `projectgroups`
  ADD CONSTRAINT `fk_projectgroups_project` FOREIGN KEY (`projectID`) REFERENCES `project` (`projectID`) ON DELETE CASCADE;

--
-- Constraints for table `roommember`
--
ALTER TABLE `roommember`
  ADD CONSTRAINT `fk_roommember_chat` FOREIGN KEY (`chatID`) REFERENCES `chatroom` (`chatID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_roommember_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `submission`
--
ALTER TABLE `submission`
  ADD CONSTRAINT `fk_submission_project` FOREIGN KEY (`projectID`) REFERENCES `project` (`projectID`) ON DELETE CASCADE;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_project` FOREIGN KEY (`projectID`) REFERENCES `project` (`projectID`) ON DELETE CASCADE;

--
-- Constraints for table `taskcontributor`
--
ALTER TABLE `taskcontributor`
  ADD CONSTRAINT `fk_taskcontributor_task` FOREIGN KEY (`taskID`) REFERENCES `task` (`taskID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_taskcontributor_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`roleID`) REFERENCES `role` (`roleID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
