# 📚 iucBiBli - Gestion de Bibliothèque

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)

[![Live Demo](https://img.shields.io/badge/🌐_Démo_en_ligne-Voir_le_projet-success?style=for-the-badge)](https://sevux.alwaysdata.net/index.php)

Application web de gestion des emprunts de livres (réservations, retours, pénalités) développée en PHP natif avec architecture MVC.

---

## 📸 Aperçu

| Espace Étudiant (Accueil) | Espace Admin (Dashboard) |
|:---:|:---:|
| ![Accueil Étudiant](screenshots/accueil_etudiant.png) | ![Dashboard Admin](screenshots/dashboard_admin.png) |
| *Recherche et réservation de livres* | *Statistiques et gestion des retours* |

---

## ✨ Fonctionnalités

### 👤 Espace Étudiant
- 🔍 Recherche et consultation du catalogue
- 📦 Panier et demande d'emprunt
- 📋 Historique des emprunts
- 💳 Gestion des pénalités

### 🔐 Espace Bibliothécaire
- 📊 Dashboard avec statistiques en temps réel
- ✅ Validation des demandes d'emprunt
- 📚 Gestion du catalogue (ajout/modification/suppression)
- 👥 Gestion des utilisateurs
- 📈 Graphiques des livres populaires (Chart.js)

---

## 🛠️ Stack Technique

- **Backend** : PHP 8 (Architecture MVC, PDO)
- **Base de données** : MySQL
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Visualisation** : Chart.js
- **Conception** : Diagrammes UML

---

## 🚀 Installation Locale

### Prérequis

![Laragon](https://img.shields.io/badge/Laragon-0E83CD?style=flat&logo=laragon&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=flat&logo=xampp&logoColor=white)
![WAMP](https://img.shields.io/badge/WAMP-EC4E3D?style=flat)

- Serveur local (Laragon, XAMPP, WAMP, ou MAMP)
- PHP 8.0+
- MySQL 5.7+

### Étapes d'installation

1. **Cloner le projet**
```bash
   git clone https://github.com/brelnosse/iucBiBli.git
   cd iucBiBli
```

2. **Configurer la base de données**
```bash
   # Créer la base de données
   mysql -u root -p -e "CREATE DATABASE iucbibli;"
   
   # Importer le schéma
   mysql -u root -p iucbibli < database.sql
```

3. **Configuration**
   - Ouvrez `config.php`
   - Modifiez les identifiants si nécessaire :
```php
   <?php   
       $bdd = new PDO("mysql:host=localhost;dbname=iucbibli", "root", "");
```

4. **Lancer le projet**
   - Démarrez votre serveur local
   - Accédez à `http://localhost/iucBiBli`

---

## 🧪 Comptes de Test

### 👤 Compte Étudiant
```
Nom complet : Brel Nosse
Matricule : iuc23e0081654
```

### 🔐 Compte Bibliothécaire
```
Email : jfk19736@gmail.com
Matricule : IUC23E0081654
```

---

## 📖 Scénarios de Test

### Test Étudiant

1. Cliquez sur **"S'inscrire"** depuis l'accueil
2. Remplissez le formulaire :
   - Matricule (format : IUCXXEXXXXXXX - 13 caractères)
   - École, Niveau, etc.
3. Une fois connecté :
   - Recherchez un livre dans le catalogue
   - Ajoutez-le au panier
   - Validez votre demande d'emprunt
   - Consultez l'historique de vos emprunts

### Test Bibliothécaire

1. Cliquez sur **"Se connecter en tant qu'administrateur"**
2. Utilisez les identifiants admin ci-dessus
3. Testez les fonctionnalités :
   - **Dashboard** : Consultez les statistiques
   - **Demandes en attente** : Validez ou refusez les emprunts
   - **Catalogue** : Ajoutez/modifiez des livres
   - **Retours** : Traitez les retours et calculez les pénalités

---

## 🎓 Contexte Académique

Projet réalisé dans le cadre du cursus **Ingénieur en Informatique** à l'**IUC Douala** (Cameroun).

### Objectifs pédagogiques

- ✅ Modélisation UML (cas d'utilisation, diagrammes de classes)
- ✅ Gestion d'une base de données relationnelle
- ✅ Sécurité web (injection SQL, sessions)
- ✅ Interface utilisateur responsive

---

## 📊 Statistiques du Projet

![GitHub repo size](https://img.shields.io/github/repo-size/ton-username/iucBiBli?style=flat-square)
![GitHub last commit](https://img.shields.io/github/last-commit/ton-username/iucBiBli?style=flat-square)
![GitHub stars](https://img.shields.io/github/stars/ton-username/iucBiBli?style=social)

---

## 👨‍💻 Auteur

**Brel nosse**  
📧 brelnosse2@gmail.com  
🔗 [LinkedIn](https://www.linkedin.com/in/brel-nosse-88a3a2377/) | [GitHub](https://github.com/brelnosse)

---

## 🙏 Remerciements

- 🎓 **IUC Douala** pour l'encadrement académique
- 📚 **Chart.js** pour la visualisation de données
- 🌐 **Alwaysdata** pour l'hébergement de la démo

---

<div align="center">

⭐ **N'oubliez pas de mettre une étoile si ce projet vous a été utile !** ⭐

[![GitHub stars](https://img.shields.io/github/stars/ton-username/iucBiBli?style=social)](https://github.com/ton-username/iucBiBli/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/ton-username/iucBiBli?style=social)](https://github.com/ton-username/iucBiBli/network/members)

</div>
