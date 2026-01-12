<?php 
    session_start();
    include("admin/config.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/index.css">
    <title>Connexion - IUCBibli</title>
</head>
<body>
    
    <nav class="auth-nav">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i> <span>IucBibli</span>
        </div>
        <div class="nav-links">
            <a href="inscription.php" class="btn-ghost">S'inscrire</a>
            <a href="index.php" class="btn-primary">Se connecter</a>
        </div>
    </nav>

    <?php if(isset($_GET['ref']) AND !empty($_GET['ref'])){ ?>
         <div class="alert-box info-float">
            <i class="fa fa-circle-info"></i>
            <span>Connectez-vous pour pouvoir utiliser l'application.</span>
         </div>
    <?php } ?>

    <main class="auth-container">
        
        <div class="auth-illustration">
            <div class="circle-deco"></div>
            <h1>Bienvenue sur<br><span class="highlight">IUCBibli</span></h1>
            <p>Votre portail de bibliothèque numérique.</p>
        </div>

        <div class="auth-form-card">
            <div class="form-header">
                <h2>Connexion <span class="badge">Etudiant</span></h2>
                <p>Entrez vos identifiants pour accéder à votre espace.</p>
            </div>

            <div class="form-body">
                <div class="input-group">
                    <label for="fullname">Nom complet <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa fa-user input-icon"></i>
                        <input type="text" id="fullname" placeholder="Bryan Mafotsing">
                    </div>
                </div>

                <div class="input-group">
                    <label for="matricule">Matricule <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa fa-id-card input-icon"></i>
                        <input type="text" id="matricule" placeholder="IUC23E0081524">
                    </div>
                </div>

                <button class="btn-submit form__footer--button">
                    Se connecter <i class="fa fa-arrow-right"></i>
                </button>
            </div>

            <div class="form-footer-link">
                <a href="admin/index.php" class="admin-link">
                    <i class="fa fa-user-shield"></i> Accès Administrateur
                </a>
            </div>
        </div>
    </main>

    <div class="connexionErrorMsg hidden">
        <i class="fa fa-circle-exclamation"></i>
        <span>Une erreur est survenue</span>
    </div> 

    <script src="js/index.js"></script> 
</body>
</html>