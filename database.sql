/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: p2pmonero
-- ------------------------------------------------------
-- Server version	10.11.13-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `balance_ledger`
--

DROP TABLE IF EXISTS `balance_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `balance_ledger` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `related_type` enum('deposit','withdrawal','escrow_lock','escrow_release','fee') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `amount` decimal(20,12) NOT NULL,
  `direction` enum('credit','debit') NOT NULL,
  `status` enum('locked','unlocked') NOT NULL DEFAULT 'locked',
  `balance_after` decimal(20,12) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_ledger_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `balance_ledger`
--

LOCK TABLES `balance_ledger` WRITE;
/*!40000 ALTER TABLE `balance_ledger` DISABLE KEYS */;
INSERT INTO `balance_ledger` VALUES
(1,2,'deposit',1,0.001000000000,'credit','unlocked',0.001000000000,'2026-01-26 06:57:14'),
(2,2,'deposit',2,0.001653922900,'credit','unlocked',0.002653922900,'2026-01-26 06:57:14'),
(3,1,'deposit',3,0.001000000000,'credit','unlocked',0.001000000000,'2026-01-26 07:17:05');
/*!40000 ALTER TABLE `balance_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposits`
--

DROP TABLE IF EXISTS `deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subaddress_id` int(11) DEFAULT NULL,
  `txid` varchar(100) NOT NULL,
  `amount` decimal(20,12) NOT NULL,
  `confirmations` int(11) NOT NULL DEFAULT 0,
  `credited` tinyint(1) NOT NULL DEFAULT 0,
  `height` int(11) DEFAULT NULL,
  `unlock_height` int(11) DEFAULT NULL,
  `blocks_left` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','locked','confirmed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `seen_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `txid` (`txid`),
  KEY `user_id` (`user_id`),
  KEY `fk_deposits_subaddress` (`subaddress_id`),
  CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_deposits_subaddress` FOREIGN KEY (`subaddress_id`) REFERENCES `subaddresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
INSERT INTO `deposits` VALUES
(1,2,2,'e7973df817a3f9a0f97ed4ea9d95b8551c869d6fbb9295c237df8a8198c6635d',0.001000000000,368,1,3595746,NULL,0,'confirmed','2026-01-26 06:57:14','2026-01-26 06:57:14'),
(2,2,2,'04185002ea27720ae5c1a3bc7fcd8ceb074d58f8b3df58d9b2705f65327819f2',0.001653922900,678,1,3595436,NULL,0,'confirmed','2026-01-26 06:57:14','2026-01-26 06:57:14'),
(3,1,3,'70554949b06cc50e192be4bfee85e77ffc291990d86bd97f35a9c1b762e08cc0',0.001000000000,10,1,3596118,NULL,0,'confirmed','2026-01-26 07:07:25','2026-01-26 07:07:25');
/*!40000 ALTER TABLE `deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listings`
--

DROP TABLE IF EXISTS `listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `listings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('buy','sell') NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` decimal(20,8) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `payment_method` varchar(100) NOT NULL,
  `min_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `status` enum('active','paused','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_listings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listings`
--

LOCK TABLES `listings` WRITE;
/*!40000 ALTER TABLE `listings` DISABLE KEYS */;
/*!40000 ALTER TABLE `listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subaddresses`
--

DROP TABLE IF EXISTS `subaddresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subaddresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` varchar(120) NOT NULL,
  `index_no` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `address` (`address`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `subaddresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subaddresses`
--

LOCK TABLES `subaddresses` WRITE;
/*!40000 ALTER TABLE `subaddresses` DISABLE KEYS */;
INSERT INTO `subaddresses` VALUES
(1,2,'88Z6Xz3e11m2Borr98s55vCbReWgWGY2hAHaaKS76Qnuc9L3Cw15KLNE3eiHgxcYArLz6B2MSpqsSMx1MTEL8PmZKS4NZwJ',3,'2026-01-23 11:43:49'),
(2,2,'8A5ngL5UZ6weiERjkfCFUMWH5iPJcR8FuWWecwiGdMrmVzXMTooStocHwuu4JR3Xvp7EDZWg5UTvQYB9gTsuUV2gUzDejxw',4,'2026-01-25 08:21:49'),
(3,1,'8AwycLJkpim6v95Lpz2BTa11oSz2Nd98MJ1hevZBk68jda82Y6SjE6YedArUnuppVqRwNYkEwso5fj5sKU12ADRyGSkVZnh',5,'2026-01-26 06:59:31');
/*!40000 ALTER TABLE `subaddresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `pgp_public` text DEFAULT NULL,
  `recovery_code_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `backup_completed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Habibi','$argon2id$v=19$m=65536,t=4,p=1$MHQycFhOWmVCZkMwWnVCSQ$ruWWljCgHMvFBNNatLdOO/LkoWW43Q2k5BaooP57lfk','-----BEGIN PGP PUBLIC KEY BLOCK-----\n\nmDMEaXMz5xYJKwYBBAHaRw8BAQdApXsjnCHHLL4MJPzXCQwJI5NWFu5J/GOXQ1IB\nY6tuUFe0H0hhYmliaSA8SGFiaWJpQHAycG1vbmVyby5sb2NhbD6IkwQTFgoAOxYh\nBLLl7cBaYo3xjBHkZJ0nYWXwNi4OBQJpczPnAhsDBQsJCAcCAiICBhUKCQgLAgQW\nAgMBAh4HAheAAAoJEJ0nYWXwNi4OJfMBAI/gIUYe0hVShRY00xUJshE1OAd2Ob7H\nxHFlk4XzwJRYAQDvT5nTSLgIIB59+EbMJpPdZODUHIsOZwCbdT9cwF+ZDbg4BGlz\nM+cSCisGAQQBl1UBBQEBB0BzHnm1vaaXMdK/4Y04VU9Neq27b1DGREJV0DLONwjo\nUQMBCAeIeAQYFgoAIBYhBLLl7cBaYo3xjBHkZJ0nYWXwNi4OBQJpczPnAhsMAAoJ\nEJ0nYWXwNi4Ou60A/j4WZ+WVW4IIr879lkQqhafmZhvPbsa6Wdrl806MxR7OAQCV\nunQu+CIEuxcul9/QH1sTlGRSxw0BVeMXFfWQirjyD5gzBGlzTNgWCSsGAQQB2kcP\nAQEHQFKhkgLHU+NcCra2xIy2ECe+5Pwu1ElrRdk9br3uz9FJtB9IYWJpYmkgPEhh\nYmliaUBwMnBtb25lcm8ubG9jYWw+iJMEExYKADsWIQTwsZU85lZwZJRBfscDHxVR\n/zddFQUCaXNM2AIbAwULCQgHAgIiAgYVCgkICwIEFgIDAQIeBwIXgAAKCRADHxVR\n/zddFU2tAQC3ARKTIPkJytcPBpKV5vGPzykihdHpw1UtmS58SmtRJwD9Fho0DRFj\n51nOib3viOPSr7Vp+D71/gUHOzgFprNwPgm4OARpc0zYEgorBgEEAZdVAQUBAQdA\nUNGPRx3tZWPWUX46CYkYYNtLsnE2fTuewy82rq/FnlMDAQgHiHgEGBYKACAWIQTw\nsZU85lZwZJRBfscDHxVR/zddFQUCaXNM2AIbDAAKCRADHxVR/zddFToaAQDqYICM\nhFY6OJT7/jvst2vI447OFbSXAoRZQpGaUovepwEAz5UAUPmf1ff71eFh0mTy+hr7\nUTpyGSvnAmyrslWw5ASYMwRpc1HCFgkrBgEEAdpHDwEBB0Dz2BlNvHWvqDPF2Uwe\nMYzMkzmXH06/9hHvyKkzJvp6VLQfSGFiaWJpIDxIYWJpYmlAcDJwbW9uZXJvLmxv\nY2FsPoiTBBMWCgA7FiEES+iLq5WKOO9WBI4S1dI5vzVHjTYFAmlzUcICGwMFCwkI\nBwICIgIGFQoJCAsCBBYCAwECHgcCF4AACgkQ1dI5vzVHjTYzUAEArRey3Mki79W7\nYxSrspZzJpNTTjwfETRYNIrwhN3tJ6cBAKJQ97YClK2VOfcgmukTo+iiA3hZ2aiP\nNGFiPSJ11zQAuDgEaXNRwhIKKwYBBAGXVQEFAQEHQHAFSYXBoq8PkR4jEzP661bk\nrWrsbLmwCMQNN8zdErsOAwEIB4h4BBgWCgAgFiEES+iLq5WKOO9WBI4S1dI5vzVH\njTYFAmlzUcICGwwACgkQ1dI5vzVHjTbJ8wD/RNc15pgNYGxD8EKtmJWieZ1sSRFs\nA134whOZjta2f6QBAM7eXMQy+dQTo5ogVCtYmyD+SNsbdFTazR8SjgQJF/EM\n=iU3l\n-----END PGP PUBLIC KEY BLOCK-----','$2y$10$LCUJMMSi08KuECgyVhy7aOOhs57/AALxmhMIM9h7x4gzk01AdU.Wm','2026-01-17 13:12:20',1),
(2,'anonwan','$argon2id$v=19$m=65536,t=4,p=1$dmo1dDliNlpBWndjblFidg$HIXtQqIVb5m+hK+UJ82Jq8OBWwNAT70MUWOWlyCZHkA','-----BEGIN PGP PUBLIC KEY BLOCK-----\n\nmDMEaXNSVxYJKwYBBAHaRw8BAQdAP2lHWTViI0d/0h09UVOqdgaYej9rdSp7Ymoo\nIgxB5w+0IWFub253YW4gPGFub253YW5AcDJwbW9uZXJvLmxvY2FsPoiTBBMWCgA7\nFiEEh1/sovWzy1mzVMLoJ7W21U6FMbwFAmlzUlcCGwMFCwkIBwICIgIGFQoJCAsC\nBBYCAwECHgcCF4AACgkQJ7W21U6FMbzD0QEAiKwtrLLP8JeIVXVRAGSU4shtit/d\nSXXJdA2fGiPFo7YBAKVXmm736FoNCyGcAWsMZkGTakmhXAqKFCA1DTJALlQNuDgE\naXNSVxIKKwYBBAGXVQEFAQEHQBo05fwVYHuY9WNKsYX6T5Ryb7dtJsl03/ds5jh+\nGchdAwEIB4h4BBgWCgAgFiEEh1/sovWzy1mzVMLoJ7W21U6FMbwFAmlzUlcCGwwA\nCgkQJ7W21U6FMbxw7AEAqVdiqBbj83ILpp8FNiaZQnG2tpjRMXUrtPFOMAOVxg8B\nAII3CLOJKpEEhvGD54aYACGCtIViJZrCFXzakwiOpsED\n=NxnJ\n-----END PGP PUBLIC KEY BLOCK-----','$2y$10$4ZGOTHQEQxR0Wql1iq81NOxtXREc3o1IMLmcgwdkdU4OI5a3B858q','2026-01-23 10:25:04',1),
(3,'sawiti','$argon2id$v=19$m=65536,t=4,p=1$MHFURjBxQklPZ0cwek9iZA$1htnYi50g9B5SvjccxMl0GECfGF9VVRuJyjdJGEdHPI',NULL,NULL,'2026-01-24 21:51:47',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawals`
--

DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` varchar(120) NOT NULL,
  `amount` decimal(20,12) NOT NULL,
  `txid` varchar(100) DEFAULT NULL,
  `status` enum('pending','broadcast','confirmed','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `txid` (`txid`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_withdrawals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawals`
--

LOCK TABLES `withdrawals` WRITE;
/*!40000 ALTER TABLE `withdrawals` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdrawals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-26  8:52:29
