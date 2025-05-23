<?php 
    session_start();
    include("admin/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="admin/assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/index.css">
    <title>IUCBibli</title>
</head>
<body>
    <div class="redBall"></div>
    <header class="menu">
        <a href="inscription.php">S'inscrire</a>
        <a href="index.php" class="active">Se connecter</a>
    </header>
    <?php
        if(isset($_GET['ref']) AND !empty($_GET['ref'])){ ?>
         <div class="pop">
            Connectez-vous pour pouvoir utilisez l'application.
         </div>
    <?php
        }
    ?>
    <div class="form">
        <div class="form__header">
            <h1 class="form__header--title">IUCBibli <sub class="session">Etudiant</sub></h1>
        </div>
        <div class="form__body">
            <div class="form__body--input_box">
                <label for="fullname">Nom complet<exp>*</exp></label>
                <input type="text" id="fullname" class="form__body--input" placeholder="Nom complet">
            </div>
            <div class="form__body--input_box">
                <label for="mat">Matricule<exp>*</exp></label>
                <input type="text" id="matricule" class="form__body--input" placeholder="Matricule">
            </div>
        </div>
        <div class="form__footer">
            <button class="form__footer--button"><i class="fa fa-user" style="margin: 8px; font-size: 0.8em"></i>connexion</button>
        </div>
    </div>
    <a href="admin/index.php" class="admin">se connecter en tant qu'administrateur <i class="fa fa-user" style="margin: 5px"></i></a>
    <div class="connexionErrorMsg">
        une erreur est survenue
    </div> 
    <script src="js/index.js"></script> 
</body>
</html>