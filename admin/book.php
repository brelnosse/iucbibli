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
    <link rel="stylesheet" href="css/book.css">
    
    <title>Détails Livre - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
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
                <h1>Détails du Livre</h1>
                <p>Informations complètes sur l'ouvrage.</p>
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
                <div class="book-card-detail">
                    
                    <div class="book-cover-col">
                        <div class="cover-wrapper">
                            <img src="<?php echo $book['couverture_livres']; ?>" alt="Couverture">
                        </div>
                    </div>

                    <div class="book-info-col">
                        <div class="info-header">
                            <span class="badge-isbn">ISBN: <?php echo $book['ISBN_livres']; ?></span>
                            <h2 class="book-title"><?php echo $book['titre_livres']; ?></h2>
                            <p class="book-author">par <span><?php echo $book['auteur_livres']; ?></span></p>
                        </div>

                        <div class="info-stats">
                            <div class="stat-item">
                                <i class="fa fa-layer-group"></i>
                                <div>
                                    <span class="stat-value"><?php echo $book['nbre_livres']; ?></span>
                                    <span class="stat-label">En Stock</span>
                                </div>
                            </div>
                            </div>

                        <div class="info-actions">
                            <a href="update.php?isbn=<?php echo $book['ISBN_livres']; ?>" class="btn-edit">
                                <i class="fa fa-pen-to-square"></i> Modifier les informations
                            </a>
                            </div>
                    </div>

                </div>
            <?php
                    }
                } else {
            ?>
                <div class="empty-state">
                    <img src="assets/img/undraw_No_data_re_kwbl.png" alt="Introuvable">
                    <p>Livre introuvable.</p>
                    <a href="dashboard.php" class="btn-primary">Retour au tableau de bord</a>
                </div>
            <?php } ?>
        </div>
    </main>

</body>
</html>