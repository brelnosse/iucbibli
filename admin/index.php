<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="css/connexion.css">
    <title>Administration - IUCBibli</title>
</head>
<body>
    
    <nav class="auth-nav">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i> <span>IucBibli <small>Admin</small></span>
        </div>
        <div class="nav-links">
            <a href="../index.php" class="btn-ghost">
                <i class="fa fa-arrow-left"></i> Retour Etudiant
            </a>
        </div>
    </nav>

    <main class="auth-container">
        
        <div class="auth-form-card">
            <div class="form-header">
                <h2>Portail <span class="badge admin-badge">Admin</span></h2>
            </div>

            <div class="form-body">
                <div class="input-group">
                    <label for="email">Adresse E-mail <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa fa-envelope input-icon"></i>
                        <input type="email" id="email" placeholder="admin@iuc.com">
                    </div>
                </div>

                <div class="input-group">
                    <label for="ucode">Code d'accès <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fa fa-key input-icon"></i>
                        <input type="password" id="ucode" placeholder="Code à 13 chiffres">
                        <i class="fa fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    <small class="hint">Le code doit contenir exactement 13 caractères.</small>
                </div>

                <button class="btn-submit form__footer--button">
                    Connexion sécurisée <i class="fa fa-lock"></i>
                </button>
            </div>
            
        </div>

    </main>

    <div class="connexionErrorMsg hidden">
        <i class="fa fa-circle-exclamation"></i>
        <span>Une erreur est survenue</span>
    </div>

    <script src="js/connexion.js"></script>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#ucode');
        
        if(togglePassword){
            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>