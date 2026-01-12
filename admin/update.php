<?php 
    session_start();
    include("config.php");

    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
    if(!isset($_GET['isbn']) || empty($_GET['isbn'])){
        header("location:dashboard.php");
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
    
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/update.css">
    
    <title>Modifier Livre - <?php echo htmlspecialchars($_SESSION['email']);?></title>
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
            <li><a href="dashboard.php" class="active"><i class="fa fa-chart-pie"></i> <span class="label">Vue d'ensemble</span></a></li>
            <li><a href="addbook.php"><i class="fa fa-plus-circle"></i> <span class="label">Ajouter un livre</span></a></li>
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
            <li><a href="borrowedBooks.php"><i class="fa fa-book-reader"></i> <span class="label">Livres Empruntés</span></a></li>
            <li><a href="history.php"><i class="fa fa-clock-rotate-left"></i> <span class="label">Historique</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="index.php?log" class="logout-btn"><i class="fa fa-arrow-right-from-bracket"></i> <span>Déconnexion</span></a>
        </div>
    </nav>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="page-title">
                <h1>Modifier le Livre</h1>
                <p>Mettre à jour les informations de l'ouvrage.</p>
            </div>
            <a href="dashboard.php" class="btn-back"><i class="fa fa-arrow-left"></i> Retour</a>
        </header>

        <div class="content-body center-content">
            <?php 
                $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                $getBook->execute(array(htmlspecialchars($_GET['isbn'])));

                if($getBook->rowCount() == 1){ 
                    while($book = $getBook->fetch()){ 
            ?>
            
            <div class="update-card">
                
                <div class="update-header">
                    <div class="title-edit-group">
                        <label for="book_title" class="icon-label titre_btn" title="Modifier le titre">
                            <i class="fa fa-pen-to-square"></i>
                        </label>
                        <input type="text" name="book_title" id="book_title" disabled 
                               value="<?php echo $book["titre_livres"]; ?>" 
                               class="title-input">
                    </div>
                </div>

                <div class="update-body">
                    
                    <div class="update-info-col">
                        
                        <div class="input-edit-group">
                            <div class="label-row">
                                <label for="auteur">Auteur</label>
                                <span class="edit-trigger nomauteur" id="<?php echo $book["ISBN_livres"]; ?>">
                                    <i class="fa fa-pen"></i> Modifier
                                </span>
                            </div>
                            <input type="text" id="auteur" value="<?php echo $book["auteur_livres"]; ?>" disabled>
                        </div>

                        <div class="input-edit-group">
                            <label for="isbn">ISBN (Lecture seule)</label>
                            <input type="text" id="isbn" value="<?php echo $book["ISBN_livres"]; ?>" disabled class="readonly-input">
                        </div>

                        <div class="input-edit-group">
                            <div class="label-row">
                                <label for="exemplaire">Stock</label>
                                <span class="edit-trigger nbrelivre" id="<?php echo $book["ISBN_livres"]; ?>">
                                    <i class="fa fa-pen"></i> Modifier
                                </span>
                            </div>
                            <input type="number" id="exemplaire" value="<?php echo $book["nbre_livres"]; ?>" disabled>
                        </div>

                        <div class="updateErrorMsg hidden">
                            <i class="fa fa-circle-exclamation"></i> Une erreur est survenue
                        </div>

                    </div>

                    <div class="update-image-col" style="background-image: url(<?php echo $book["couverture_livres"];?>)">
                        <div class="overlay">
                            <label for="couverture" class="upload-btn" title="Changer la couverture">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file" id="couverture" accept="image/*" hidden>
                        </div>
                    </div>

                </div>
            </div>

            <?php 
                    }
                } 
            ?>
        </div>
    </main>

    <script src="js/update.js"></script>
</body>
</html>