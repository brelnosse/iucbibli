-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour iucbibli
DROP DATABASE IF EXISTS `iucbibli`;
CREATE DATABASE IF NOT EXISTS `iucbibli` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `iucbibli`;

-- Listage de la structure de table iucbibli. admin
DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `email_admin` varchar(255) NOT NULL,
  `ucode_admin` varchar(50) NOT NULL,
  PRIMARY KEY (`email_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.admin : ~0 rows (environ)
INSERT INTO `admin` (`email_admin`, `ucode_admin`) VALUES
	('brelnosse2@gmail.com', 'IUC23E0081654');

-- Listage de la structure de table iucbibli. caution
DROP TABLE IF EXISTS `caution`;
CREATE TABLE IF NOT EXISTS `caution` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mat_etu` char(13) DEFAULT NULL,
  `nom_etu` varchar(255) DEFAULT NULL,
  `caution` float DEFAULT NULL,
  `date_dernier_ajout` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.caution : ~0 rows (environ)

-- Listage de la structure de table iucbibli. emprunt
DROP TABLE IF EXISTS `emprunt`;
CREATE TABLE IF NOT EXISTS `emprunt` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule_etudiant` varchar(13) NOT NULL,
  `isbn_livres` char(13) NOT NULL,
  `nom_etudiant` varchar(155) NOT NULL DEFAULT '',
  `numero_etudiant` bigint NOT NULL DEFAULT '0',
  `mode` varchar(10) NOT NULL DEFAULT '',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `isok` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'false',
  `viewedbyhost` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'false',
  `viewedbystudent` varchar(5) DEFAULT 'false',
  `repliedDate` date DEFAULT NULL,
  `rendu` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`matricule_etudiant`,`isbn_livres`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.emprunt : ~0 rows (environ)

-- Listage de la structure de table iucbibli. etudiant
DROP TABLE IF EXISTS `etudiant`;
CREATE TABLE IF NOT EXISTS `etudiant` (
  `matricule_etudiant` char(13) NOT NULL,
  `nom_etudiant` varchar(150) NOT NULL,
  `filiere_etudiant` varchar(150) NOT NULL,
  `niveau_etudiant` int NOT NULL,
  `nbre_emprunt_etudiant` bigint NOT NULL,
  `carte_etu_etudiant` varchar(100) NOT NULL DEFAULT '',
  `numero_etudiant` bigint NOT NULL,
  `email_etudiant` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `isChecked` int DEFAULT NULL,
  PRIMARY KEY (`matricule_etudiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.etudiant : ~8 rows (environ)
INSERT INTO `etudiant` (`matricule_etudiant`, `nom_etudiant`, `filiere_etudiant`, `niveau_etudiant`, `nbre_emprunt_etudiant`, `carte_etu_etudiant`, `numero_etudiant`, `email_etudiant`, `isChecked`) VALUES
	('iuc2120345831', 'TCHEMMEGNE ANGE', '3iac', 1, 0, 'pas d\'info', 678960012, 'zeus@gmail.com', 0),
	('iuc23e0081653', 'brel nosse', '3iac', 1, 0, 'pas d\'info', 673809889, '', 0),
	('iuc23e0081654', 'brel nosse', '3iac', 1, 15, 'pas d\'info', 673809889, '', 0),
	('iuc23e0081655', 'john doe', 'istdi', 1, 0, 'pas d\'info', 650194960, 'brelnosse2@gmail.com', 0),
	('iuc23e0081656', 'john lenon', '3iac', 2, 1, 'pas d\'info', 673809889, 'jfk19735@gmail.com', 0),
	('iuc23e0081658', 'naouel', '3iac', 1, 1, 'pas d\'info', 673809889, '', 0),
	('iuc23e0081659', 'brel nosse', '3iac', 1, 1, 'pas d\'info', 650194960, '', 0),
	('IUC23E0082179', 'TCHEMMEGNE ANGE', '3iac', 1, 2, 'pas d\'info', 674500052, '', 0);

-- Listage de la structure de table iucbibli. historique
DROP TABLE IF EXISTS `historique`;
CREATE TABLE IF NOT EXISTS `historique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `isbn_livres` char(13) NOT NULL,
  `nom_etudiant` varchar(155) NOT NULL,
  `numero_etudiant` bigint NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `repliedDate` date DEFAULT NULL,
  `accorder` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.historique : ~2 rows (environ)
INSERT INTO `historique` (`id`, `isbn_livres`, `nom_etudiant`, `numero_etudiant`, `date_debut`, `date_fin`, `repliedDate`, `accorder`) VALUES
	(15, '2746017016123', 'brel nosse', 673809889, '2024-06-10', '2024-06-10', '2024-06-10', 'Accorder'),
	(16, '2744018600123', 'brel nosse', 673809889, '2024-06-10', '2024-06-12', '2024-06-10', 'Rejeter'),
	(17, '2746017016123', 'brel nosse', 673809889, '2025-04-27', '2025-04-17', '2025-04-17', 'Accorder');

-- Listage de la structure de table iucbibli. livres
DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `ISBN_livres` varchar(13) NOT NULL,
  `titre_livres` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `auteur_livres` varchar(200) NOT NULL,
  `cote_livres` bigint NOT NULL,
  `date_ajout_livres` date NOT NULL,
  `nbre_livres` int DEFAULT NULL,
  `email_admin` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `couverture_livres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`ISBN_livres`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.livres : ~12 rows (environ)
INSERT INTO `livres` (`ISBN_livres`, `titre_livres`, `auteur_livres`, `cote_livres`, `date_ajout_livres`, `nbre_livres`, `email_admin`, `couverture_livres`) VALUES
	('0131426443123', 'c how to program', 'H. M. DEITEL - P. J. DEITEL', 2, '2024-06-04', 10, 'brelnosse2@gmail.com', 'assets/uploads/9780131426443-uk.jpg'),
	('21000067508', 'ecrire du code securise', 'michael howard et david leblanc', 1, '2024-06-10', 2, 'brelnosse2@gmail.com', 'assets/uploads/1718015240172.jpg'),
	('2100067508123', 'TOO GOOD TO LEAVE TOO BAD TO STAY', 'Mira kirschenbaum', 18, '2024-06-03', 6, 'brelnosse2@gmail.com', 'assets/uploads/toogoodtoleaveto00mira_0001.jpg'),
	('2738101453', 'Donnant donnat', 'robert axelrod', 0, '2024-06-10', 2, 'brelnosse2@gmail.com', 'assets/uploads/1718015673711.jpg'),
	('2744018600', 'php  et  Mysql', 'luke welling  , laura thomson', 0, '2024-06-10', 2, 'brelnosse2@gmail.com', 'assets/uploads/1718015499927.jpg'),
	('2744018600123', 'L&#039;interpretation du reve', 'luke welling ', 14, '2024-06-03', 5, 'brelnosse2@gmail.com', 'assets/uploads/415SNDDqPDL.jpg'),
	('2746017016123', 'Coldfusion mx: developper un site web dynamique', 'philippe chatellier', 59, '2024-06-04', 17, 'brelnosse2@gmail.com', 'assets/uploads/images_LE_auto_x2.jpg'),
	('2870099258123', 'Les emotions : une memoire individuelle et collective', 'ahmed channouf', 0, '2024-06-04', 2, 'brelnosse2@gmail.com', 'assets/uploads/images.jpg'),
	('2912877407123', 'Le theatre de l&#039;effroi', 'rene-marc pille', 124, '2024-06-04', 4, 'brelnosse2@gmail.com', 'assets/uploads/3244618.jpg'),
	('2927417392123', 'windows 2000 professionnel', 't. weltner', 0, '2024-06-04', 3, 'brelnosse2@gmail.com', 'assets/uploads/51eJIocAMNL._AC_UF1000,1000_QL80_.jpg'),
	('9782100598625', 'traitements et revetements de surface des metaux', 'robert leveque', 0, '2024-06-10', 5, 'brelnosse2@gmail.com', 'assets/uploads/1718015839935.jpg'),
	('9782710107132', 'psychologie actuelle et developpement de l&#039;enfant', 'pierre vayer et charles roncin', 0, '2024-06-04', 2, 'brelnosse2@gmail.com', 'assets/uploads/ascodocpsy_record_42585.png');

-- Listage de la structure de table iucbibli. messages
DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text NOT NULL,
  `date_sent` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.messages : ~0 rows (environ)

-- Listage de la structure de table iucbibli. notification
DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_mat` char(13) DEFAULT NULL,
  `viewed` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'false',
  `date_fin` date DEFAULT NULL,
  `day_left` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.notification : ~0 rows (environ)
INSERT INTO `notification` (`id`, `student_mat`, `viewed`, `date_fin`, `day_left`) VALUES
	(27, 'iuc23e0081654', 'true', '2024-06-09', 1);

-- Listage de la structure de table iucbibli. vue
DROP TABLE IF EXISTS `vue`;
CREATE TABLE IF NOT EXISTS `vue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `book_isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_matricule` char(13) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table iucbibli.vue : ~18 rows (environ)
INSERT INTO `vue` (`id`, `book_isbn`, `user_matricule`) VALUES
	(23, '2744018600123', 'iuc23e0081654'),
	(24, '2100067508123', 'iuc23e0081654'),
	(25, '2927417392123', 'iuc23e0081654'),
	(26, '2912877407123', 'iuc23e0081654'),
	(27, '9782710107132', 'iuc23e0081654'),
	(28, '2746017016123', 'iuc23e0081654'),
	(29, '0131426443123', 'iuc23e0081654'),
	(30, '2870099258123', 'iuc23e0081654'),
	(31, '2744018600123', 'iuc23e0081655'),
	(32, '2746017016123', 'iuc23e0081655'),
	(33, '2100067508123', 'iuc23e0081655'),
	(34, '2746017016123', 'iuc23e0081656'),
	(35, '2912877407123', 'iuc23e0081656'),
	(36, '2927417392123', 'iuc23e0081656'),
	(37, '2912877407123', 'IUC23E0082179'),
	(38, '2746017016123', 'IUC23E0082179'),
	(39, '2746017016123', 'iuc23e0081658'),
	(40, '9782100598625', 'iuc23e0081654');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
