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
    <link rel="stylesheet" href="css/inscription.css">
    <title>IUCBibli</title>
</head>
<body>
    <div class="redBall"></div>
    <header class="menu">
        <a href="inscription.php" class="active">S'inscrire</a>
        <a href="index.php">Se connecter</a>
    </header>
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
            <div class="form__body--input_box">
                <label for="ecole">&eacute;cole<exp>*</exp></label>
                <select name="ecole" id="ecole" class="form__body--input">
                    <option value="none">S&eacute;lectionner votre &eacute;cole</option>
                    <option value="3iac">3IAC</option>
                    <option value="istdi">ISTDI</option>
                    <option value="pisti">PISTI</option>
                    <option value="icia">ICIA</option>
                    <option value="seas">SEAS</option>
                </select>
            </div>
            <div class="form__body--input_box">
                <label for="niveau">Niveau<exp>*</exp></label>
                <select name="niveau" id="niveau" class="form__body--input">
                    <option value="none">S&eacute;lectionner votre niveau</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form__body--input_box">
                <label for="email">E-mail (optionel)</label>
                <input type="email" id="email" class="form__body--input" placeholder="E-mail">
            </div>
            <div class="form__body--input_box">
                <label for="phone">Num&eacute;ro de t&eacute;l&eacute;phone<exp>*</exp></label>
                <input type="phone" id="phone" class="form__body--input" placeholder="Ex: 6xxxxxxxx">
            </div>
        </div>
        <div class="form__footer">
            <button class="form__footer--button">S'inscrire</button>
        </div>
    </div>
    <div class="connexionErrorMsg">
        une erreur est survenue
    </div>   
    <script src="js/inscription.js"></script> 
</body>
</html>