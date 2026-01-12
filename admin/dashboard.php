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
    
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    <link rel="stylesheet" href="css/dashboard.css"/>
    <title>Dashboard Admin - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
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
                <a href="dashboard.php" class="active">
                    <i class="fa fa-chart-pie"></i> <span class="label">Vue d'ensemble</span>
                </a>
            </li>
            <li>
                <a href="addbook.php">
                    <i class="fa fa-plus-circle"></i> <span class="label">Ajouter un livre</span>
                </a>
            </li>
            <li>
                <a href="gereremprunt.php" class="notif-link">
                    <i class="fa fa-list-check"></i> <span class="label">Gérer Emprunts</span>
                    <?php
                        $getunreadEmprunt = $bdd->query("SELECT * FROM emprunt WHERE viewedbyhost = false");
                        if($getunreadEmprunt->rowCount() > 0){
                            echo '<span class="badge-count" id="notif-count">'.$getunreadEmprunt->rowCount().'</span>';
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
                <h1>Tableau de bord</h1>
                <p>Gestion de la bibliothèque</p>
            </div>
            
            <form method="post" class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" name="search" placeholder="Rechercher un livre, auteur..." value="<?php if(isset($_POST['search'])) echo htmlspecialchars($_POST['search']); ?>">
            </form>
        </header>

        <div class="content-body">
            
            <div class="stats-cards" data-aos="fade-up">
                <div class="card stat-card">
                    <div class="icon bg-purple"><i class="fa fa-book"></i></div>
                    <div class="info">
                        <h3>Total Livres</h3>
                        <span>
                            <?php 
                                $countBooks = $bdd->query("SELECT SUM(nbre_livres) FROM livres")->fetchColumn();
                                echo $countBooks ? $countBooks : 0; 
                            ?>
                        </span>
                    </div>
                </div>
                <div class="card stat-card">
                    <div class="icon bg-red"><i class="fa fa-hand-holding-hand"></i></div>
                    <div class="info">
                        <h3>Emprunts Actifs</h3>
                        <span>
                            <?php 
                                $countLoan = $bdd->query("SELECT COUNT(*) FROM emprunt WHERE isok = 'true'")->fetchColumn();
                                echo $countLoan; 
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card table-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header">
                    <h2>Inventaire des Livres</h2>
                    <a href="addbook.php" class="btn-sm btn-primary"><i class="fa fa-plus"></i> Nouveau</a>
                </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Popularité</th>
                                <th>Date Ajout</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $getBooks;
                            if(isset($_POST['research']) && !empty($_POST['search'])){
                                $getBooks = $bdd->query("SELECT * FROM livres WHERE titre_livres LIKE '%".htmlspecialchars($_POST['search'])."%' OR auteur_livres LIKE '%".htmlspecialchars($_POST['search'])."%'");
                            }else{
                                $getBooks = $bdd->query("SELECT * FROM livres ORDER BY date_ajout_livres DESC");
                            }

                            if($getBooks->rowCount() > 0){
                                while($book = $getBooks->fetch()){ 
                        ?> 
                            <tr>
                                <td class="fw-bold text-dark"><?php echo $book["titre_livres"]; ?></td>
                                <td class="text-muted"><?php echo $book["auteur_livres"]; ?></td>
                                <td>
                                    <?php if($book["cote_livres"] >= 100){ ?>
                                        <span class="badge badge-purple"><i class="fa fa-star"></i> Top</span>
                                    <?php }elseif($book["cote_livres"] > 30){ ?>
                                        <span class="badge badge-blue"><i class="fa fa-thumbs-up"></i> Populaire</span>
                                    <?php }else{ ?>
                                        <span class="badge badge-gray">Standard</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($book["date_ajout_livres"])); ?></td>
                                <td><span class="stock-num"><?php echo $book["nbre_livres"]; ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="book.php?isbn=<?php echo $book['ISBN_livres']; ?>" class="btn-icon view" title="Voir"><i class="fa fa-eye"></i></a>
                                        <a href="update.php?isbn=<?php echo $book['ISBN_livres']; ?>" class="btn-icon edit" title="Modifier"><i class="fa fa-pen"></i></a>
                                        <a href="" class="btn-icon delete delete-trigger" id="<?php echo $book["ISBN_livres"]; ?>" data-isbn="<?php echo $book["ISBN_livres"]; ?>" title="Supprimer"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                                }
                            } else { 
                        ?>
                            <tr>
                                <td colspan="6" class="empty-row">
                                    <img src="assets/img/undraw_No_data_re_kwbl.png" alt="Aucun livre"> <p>Aucun livre trouvé dans la bibliothèque.</p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card chart-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header">
                    <h2>Statistiques d'Emprunts</h2>
                </div>
                <div class="chart-wrapper">
                    <canvas id="barContainer"></canvas>
                </div>
            </div>
        </div>
    </main>

    <div class="toast-box hidden" id="deleteToast">
        <div class="toast-content">
            <i class="fa fa-circle-exclamation"></i>
            <div>
                <h4>Confirmation</h4>
                <p>Cette action est irréversible.</p>
            </div>
        </div>
        <div class="toast-actions">
            <button class="btn-cancel" id="cancelDelete">Annuler (<span id="counter">5</span>s)</button>
            <button class="btn-confirm-delete" id="confirmDelete">Supprimer</button>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="js/dashboard.js"></script>

</body>
</html>