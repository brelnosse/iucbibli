<?php 
    session_start();
    include("config.php");

    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
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
    
    <link rel="stylesheet" href="css/dashboard.css"> <link rel="stylesheet" href="css/addbook.css"> <title>Ajouter un Livre - <?php echo htmlspecialchars($_SESSION['email']);?></title>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fa-solid fa-book-open-reader"></i> <span>IucBibli</span>
            </div>
            <div class="admin-profile">
                <div class="avatar"><i class="fa-solid fa-user-tie"></i></div>
                <span class="email-text"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fa fa-chart-pie"></i> <span class="label">Vue d'ensemble</span>
                </a>
            </li>
            <li>
                <a href="addbook.php" class="active">
                    <i class="fa fa-plus-circle"></i> <span class="label">Ajouter un livre</span>
                </a>
            </li>
            <li>
                <a href="gereremprunt.php" class="notif-link">
                    <i class="fa fa-list-check"></i> <span class="label">Gérer Emprunts</span>
                    <?php
                        $getunreadEmprunt = $bdd->query("SELECT * FROM emprunt WHERE viewedbyhost = false");
                        if($getunreadEmprunt->rowCount() > 0){
                            echo '<span class="badge-count">'.$getunreadEmprunt->rowCount().'</span>';
                        }
                    ?>
                </a>
            </li>
            <li>
                <a href="borrowedBooks.php">
                    <i class="fa fa-book-reader"></i> <span class="label">Livres Empruntés</span>
                </a>
            </li>
            <li>
                <a href="history.php">
                    <i class="fa fa-clock-rotate-left"></i> <span class="label">Historique</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="index.php?log" class="logout-btn">
                <i class="fa fa-arrow-right-from-bracket"></i> <span>Déconnexion</span>
            </a>
        </div>
    </nav>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="page-title">
                <h1>Nouveau Livre</h1>
                <p>Ajouter une nouvelle référence au catalogue.</p>
            </div>
        </header>

        <div class="content-body center-content">
            <div class="form-card">
                <div class="form-header-card">
                    <i class="fa fa-book-medical"></i>
                    <h2>Informations du Livre</h2>
                </div>

                <div class="form-grid">
                    <div class="form-column">
                        <div class="input-group">
                            <label for="titre">Titre du livre <span class="required">*</span></label>
                            <input type="text" id="titre" placeholder="Ex: Le Petit Prince">
                        </div>

                        <div class="input-group">
                            <label for="auteur">Auteur(s) <span class="required">*</span></label>
                            <input type="text" id="auteur" placeholder="Ex: Antoine de Saint-Exupéry">
                        </div>

                        <div class="row-inputs">
                            <div class="input-group">
                                <label for="isbn">ISBN (13 chiffres) <span class="required">*</span></label>
                                <input type="text" id="isbn" placeholder="Ex: 9782070408504" maxlength="13">
                            </div>
                            <div class="input-group">
                                <label for="expl">Stock <span class="required">*</span></label>
                                <input type="number" id="expl" value="1" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-column image-column">
                        <label>Couverture du livre <span class="required">*</span></label>
                        
                        <div class="image-upload-wrapper">
                            <label for="image" class="imagereview" id="previewContainer">
                                <div class="upload-content">
                                    <i class="fa fa-cloud-arrow-up"></i>
                                    <span>Cliquez pour ajouter</span>
                                    <small>JPG, PNG (Max 2Mo)</small>
                                </div>
                                <img src="" alt="Aperçu" id="imagePreview" class="hidden">
                            </label>
                            <input type="file" id="image" accept="image/*" hidden>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="addBookBtn btn-primary-lg">
                        <i class="fa fa-plus"></i> Enregistrer le livre
                    </button>
                </div>
            </div>
        </div>
    </main>

    <div class="updateErrorMsg hidden">
        <i class="fa fa-circle-exclamation"></i>
        <span>Une erreur est survenue</span>
    </div>

    <script src="js/addbook.js"></script>
    
    <script>
        const imageInput = document.getElementById('image');
        const previewImg = document.getElementById('imagePreview');
        const uploadContent = document.querySelector('.upload-content');

        if(imageInput){
            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('hidden');
                        uploadContent.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
</body>
</html>