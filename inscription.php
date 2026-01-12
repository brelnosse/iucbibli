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
    
    <link rel="stylesheet" href="css/inscription.css">
    <title>Inscription - IUCBibli</title>
</head>
<body>
    
    <nav class="auth-nav">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i> <span>IucBibli</span>
        </div>
        <div class="nav-links">
            <span class="nav-text">Déjà un compte ?</span>
            <a href="index.php" class="btn-ghost">Se connecter</a>
        </div>
    </nav>

    <main class="auth-container">
        <div class="auth-card">
            <div class="form-header">
                <h2>Créer un compte <span class="badge">Etudiant</span></h2>
                <p>Remplissez le formulaire pour accéder à la bibliothèque.</p>
            </div>

            <div class="form-body">
                <div class="form-grid">
                    
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
                            <input type="text" id="matricule" placeholder="IUC23E0081254">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="ecole">École <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fa fa-graduation-cap input-icon"></i>
                            <select name="ecole" id="ecole">
                                <option value="none" disabled selected>Sélectionner...</option>
                                <option value="3iac">3IAC</option>
                                <option value="istdi">ISTDI</option>
                                <option value="pisti">PISTI</option>
                                <option value="icia">ICIA</option>
                                <option value="seas">SEAS</option>
                            </select>
                            <i class="fa fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="niveau">Niveau <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fa fa-layer-group input-icon"></i>
                            <select name="niveau" id="niveau">
                                <option value="none" disabled selected>Sélectionner...</option>
                                <option value="1">Niveau 1</option>
                                <option value="2">Niveau 2</option>
                                <option value="3">Niveau 3</option>
                                <option value="4">Niveau 4</option>
                                <option value="5">Niveau 5</option>
                            </select>
                            <i class="fa fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">E-mail <span class="optional">(Optionnel)</span></label>
                        <div class="input-wrapper">
                            <i class="fa fa-envelope input-icon"></i>
                            <input type="email" id="email" placeholder="exemple@email.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="phone">Téléphone <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fa fa-phone input-icon"></i>
                            <input type="phone" id="phone" placeholder="Ex: 6xxxxxxxx">
                        </div>
                    </div>

                </div> <div class="form-footer">
                    <button class="btn-submit form__footer--button">
                        S'inscrire maintenant <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <div class="connexionErrorMsg hidden">
        <i class="fa fa-circle-exclamation"></i>
        <span>Une erreur est survenue</span>
    </div> 

    <script src="js/inscription.js"></script> 
</body>
</html>