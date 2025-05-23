<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/connexion.css">
    <title>IucBibli - connexion</title>
</head>
<body>
    <div class="greenBall"></div>
    <div class="redBall"></div>
    <div class="form">
        <div class="form__header">
            <h1 class="form__header--title">IUCBibli <sub class="session">Admin</sub></h1>
        </div>
        <div class="form__body">
            <div class="form__body--input_box">
                <label for="email">E-mail</label>
                <input type="email" id="email" class="form__body--input" placeholder="E-mail">
            </div>
            <div class="form__body--input_box">
                <label for="code">Code d'enregistrement (13 chiffres)</label>
                <input type="text" id="ucode" class="form__body--input" placeholder="Code">
            </div>
        </div>
        <div class="form__footer">
            <button class="form__footer--button">Je me connecte !</button>
        </div>
    </div>
    <a href="../index.php" class="admin">se connecter en tant qu'etudiant <i class="fa fa-user-graduate" style="margin: 5px"></i></a>
    <div class="connexionErrorMsg">
        une erreur est survenue
    </div>
    <script src="js/connexion.js"></script>
</body>
</html>