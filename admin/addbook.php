<?php 
    session_start();
    include("config.php");

    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/addbook.css">
    <title><?php echo htmlspecialchars($_SESSION['email']);?></title>
</head>
<body>
    <div class="toolsmenu">
        <div class="toolsmenu__header">
            <span class="toolsmenu__header--userpp">
                <i class="fa-solid fa-user"></i>
            </span>
            <span class="toolsmenu__header--useremail">
                <?php
                    echo htmlspecialchars($_SESSION['email']);
                ?>
            </span>
        </div>
        <div class="toolsmenu__body">
            <a href="addbook.php" class="active"><i class="fa fa-plus"></i> <span class="label">Ajouter un livre</span></a>
            <a href="dashboard.php"><i class="fa fa-eye"></i> <span class="label">Afficher les livres</span></a>
            <a href="gereremprunt.php">
                <i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span>
                <?php
                $getunreadEmprunt = $bdd->query("SELECT * FROM emprunt WHERE viewedbyhost = false");
                $getunread = $getunreadEmprunt->fetch();

                if($getunreadEmprunt->rowCount() > 0){ ?>
                 <b style="display:inline-flex; height:10px; width: 10px; border-radius: 50px; background-color: #b80000; margin-left: 8px"></b>
                <?php
                    }
                ?>
            </a>
            <a href="borrowedBooks.php"><i class="fa fa-bullseye"></i> <span class="label">Afficher les livres emprunter</span></a>
            <a href="history.php"><i class="fa fa-history"></i> <span class="label">Historique des emprunts</span></a>
        </div>
        <div class="toolsmenu__footer">
            <a href="index.php?log"><i class="fa fa-sign-out-alt"></i></a>
        </div>
    </div>
    <div class="container">
        <div class="container__title">
            <span>Tableau de bord / </span>
            <a href="addbook.php" style="margin-left: 2px"> nouveau livre</a>
        </div>
        <div class="container__body">
            <div class="redBall"></div>
            <div class="redBallp"></div>
            <div class="newBook__form">
                <label for="titre">Titre<exp>*</exp></label>
                <input type="text" id="titre" placeholder="Titre du livre">
                <label for="auteur">Auteur<exp>*</exp></label>
                <input type="text" id="auteur" placeholder="Nom de(s) auteur(s)">
                <label for="isbn">ISBN du livre (13) chiffres<exp>*</exp></label>
                <input type="text" id="isbn" placeholder="ISBN du livre">
                <label for="expl">Nombre d'exemplaire<exp>*</exp></label>
                <input type="number" id="expl" value="0">
                <label for="nothing">Premier de couverture<exp>*</exp></label>
                <label for="image" class="imagereview">
                    <i class="fa fa-camera-alt"></i>
                </label>
                <input type="file" id="image" style="display: none">
                <button class="addBookBtn">
                    <i class="fa fa-plus-circle"></i> Ajouter le livre 
                </button>
            </div>
            <div class="updateErrorMsg">
                une erreur est survenue
            </div>
        </div>
    </div>  
    <script src="js/addbook.js"></script>  
</body>
</html>