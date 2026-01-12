<?php 
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: index.php");
    }
    $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
    $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
    $fetchUser = $getUser->fetch();
    
    if($getUser->rowCount() > 0){
        $dateToday = date("Y/m/d");
        $datetime = new DateTime($dateToday);
        $end = new DateTime($fetchUser["date_fin"]); 
        $lefts = $end->diff($datetime);

        if($datetime > $end){
            $getUserCaution = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
            $getUserCaution->execute(array($_SESSION['stu_mat']));

            if($getUserCaution->rowCount() == 0){
                $AddCaution = $bdd->prepare("INSERT INTO caution(mat_etu, nom_etu, caution, date_dernier_ajout) VALUES(?, ?, ?, CURDATE())");
                $AddCaution->execute(array($_SESSION['stu_mat'], $_SESSION['stu_name'], 500*$lefts->days));
            }else{
                $updateCaution = $bdd->prepare("UPDATE caution SET caution =  caution* ".$lefts->days.", date_dernier_ajout = CURDATE() WHERE mat_etu = ? AND date_dernier_ajout != CURDATE()");
                $updateCaution->execute(array($_SESSION['stu_mat']));
            }
        }
        if($lefts->days == 1){
            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
            $getnotif->execute(array($_SESSION['stu_mat']));

            if($getnotif->rowCount() == 0){
                $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                $addnotif->execute(array(
                    'student_mat' => $_SESSION['stu_mat'],
                    'date_fin' => $fetchUser['date_fin'],
                    'day_left' => $lefts->days
                ));
            }
        }
        if($lefts->days == 0){
            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
            $getnotif->execute(array($_SESSION['stu_mat']));

            if($getnotif->rowCount() == 0){
                $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                $addnotif->execute(array(
                    'student_mat' => $_SESSION['stu_mat'],
                    'date_fin' => $fetchUser['date_fin'],
                    'day_left' => $lefts->days
                ));
            }
        }
    }

    $checkIfUserRequestedBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND date_fin < CURDATE() AND isok='false'");
    $checkIfUserRequestedBorrow->execute(array($_SESSION['stu_mat']));
    if($checkIfUserRequestedBorrow->rowCount() == 1){
        $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
    }

    $checkIfUserGaveBackBook = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND isok='false' AND date_fin < CURDATE() AND matricule_etudiant IN (SELECT matricule_etudiant FROM etudiant WHERE nbre_emprunt_etudiant > 0)");
    $checkIfUserGaveBackBook->execute(array($_SESSION['stu_mat']));

    if($checkIfUserGaveBackBook->rowCount() > 0){
        //on ajoute dans la table de l'historique
        // $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        // $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
        //on retire de la table emprunt
        $deleteRequestedBack = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBack->execute(array($_SESSION['stu_mat']));
        echo "Vous pouvez de nouveaux effectuer des emprunts";
    }
    if(isset($_POST['confirmDisconnect'])){
        session_destroy();
        header("location: index.php");
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/home.css">
    <title>IucBibli</title>
</head>
<body>

    <nav class="top-navbar">
        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i> <span>IucBibli</span>
        </div>
        
        <form method="post" class="nav-actions">
             <div class="search-bar">
                <i class="fa fa-search"></i>
                <input type="search" placeholder="Rechercher des livres, auteurs..." name="q" value="<?php if(isset($_POST['q'])) echo htmlspecialchars($_POST['q']); ?>">
            </div>

            <div class="user-menu">
                <a href="home.php?action=showCautionInfo&cote=all" class="menu-item wallet" title="Ma Caution">
                    <i class="fa-solid fa-wallet"></i>
                    <span class="amount">
                        <?php 
                            $getAmount = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
                            $getAmount->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
                            if($getAmount->rowCount() == 0){ echo "0 FCFA"; }
                            else{ $getAmountfetched = $getAmount->fetch(); echo $getAmountfetched['caution']." FCFA"; }
                        ?>
                    </span>
                </a>

                <a href="notification.php" class="menu-item relative">
                    <i class="fa-regular fa-bell"></i>
                    <?php
                        $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
                        $getnotif->execute(array($_SESSION['stu_mat']));
                        $getnotif2 = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND viewedbystudent = 'false' AND repliedDate IS NOT NULL");
                        $getnotif2->execute(array($_SESSION['stu_mat']));

                        if($getnotif->rowCount() == 1 || $getnotif2->rowCount() == 1){ 
                            echo '<span class="notif-dot"></span>';
                        }
                    ?>
                </a>

                <a href="panier.php" class="menu-item"><i class="fa-solid fa-cart-shopping"></i></a>

                <div class="profile-dropdown">
                    <div class="profile-trigger">
                        <div class="avatar"><?php echo strtoupper(substr($_SESSION['stu_name'], 0, 1)); ?></div>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                    <div class="dropdown-content">
                        <span class="user-name">Salut, <?php echo $_SESSION['stu_name']; ?></span>
                        <a href="home.php?cote=all&disconnect" class="disconnect-btn"><i class="fa fa-arrow-right-from-bracket"></i> Déconnexion</a>
                    </div>
                </div>
            </div>
        </form>
    </nav>

    <header class="hero-section">
        <div class="hero-content">
            <h1>Vous recherchez <span class="highlight">un livre particulier</span> ?</h1>
            <p>Explorez notre bibliotèque et trouvez votre prochaine aventure.</p>
        </div>
        
        <div class="custom-shape-divider-bottom-1689">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
            </svg>
        </div>
    </header>

    <div class="tabs-container">
        <div class="tabs-wrapper">
            <?php
                $activeAll = (!isset($_GET['cote']) || $_GET['cote'] == 'all') ? 'active' : '';
                $activePop = (isset($_GET['cote']) && $_GET['cote'] == 'populaire') ? 'active' : '';
                $activeVeryPop = (isset($_GET['cote']) && $_GET['cote'] == 'trespopulaire') ? 'active' : '';
            ?>
            <a href="home.php?cote=all" class="tab-pill <?php echo $activeAll; ?>">Tout</a>
            <a href="home.php?cote=populaire" class="tab-pill <?php echo $activePop; ?>">Populaires</a>
            <a href="home.php?cote=trespopulaire" class="tab-pill <?php echo $activeVeryPop; ?>">Très populaire</a>
        </div>
    </div>

    <main class="container">
        
        <?php
        if((!isset($_GET['cote']) || ($_GET['cote'] != 'populaire' && $_GET['cote'] != 'trespopulaire')) && !isset($_POST['q'])){
            $getPopu = $bdd->query("SELECT * FROM livres WHERE cote_livres > (SELECT AVG(cote_livres) FROM livres) AND nbre_livres > 0 ORDER BY cote_livres DESC LIMIT 3");
            if($getPopu->rowCount() > 0){ ?>
                <section class="featured-section">
                    <h2 class="section-title">Les plus populaire <i class="fas fa-fire"></i></h2>
                    <div class="featured-grid">
                    <?php while($data = $getPopu->fetch()){ ?>
                        <div class="featured-card">
                            <img src="admin/<?php echo $data["couverture_livres"]?>" alt="Cover">
                            <div class="featured-info">
                                <h3><?php echo $data['titre_livres']; ?></h3>
                                <p><?php echo $data['auteur_livres']; ?></p>
                                <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="btn-circle"><i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </section>
        <?php } } ?>

        <section class="catalog-section">
            <h2 class="section-title">Collection de livres</h2>
            <div class="books-grid">
                <?php
                $getBooks;
                if(isset($_POST['q']) && !empty($_POST['q'])){
                    $getBooks = $bdd->query("SELECT * FROM livres WHERE titre_livres LIKE '%".htmlspecialchars($_POST['q'])."%' OR auteur_livres LIKE '%".htmlspecialchars($_POST['q'])."%' AND nbre_livres > 0 ");
                } else {
                    $cote = $_GET['cote'] ?? 'all';
                    switch($cote){
                        case "populaire": $sql = "SELECT * FROM livres WHERE cote_livres > 30 AND cote_livres < 100 AND nbre_livres > 0"; break;
                        case "trespopulaire": $sql = "SELECT * FROM livres WHERE cote_livres > 100 AND nbre_livres > 0"; break;
                        default: $sql = "SELECT * FROM livres WHERE nbre_livres > 0"; break;
                    }
                    $getBooks = $bdd->query($sql);
                }

                if($getBooks->rowCount() > 0){ 
                    while($data = $getBooks->fetch()){ ?>
                        <div class="book-card">
                            <div class="card-image">
                                <img src="admin/<?php echo $data["couverture_livres"]?>" loading="lazy">
                                <div class="card-overlay">
                                    <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="btn-view">Details</a>
                                </div>
                            </div>
                            <div class="card-details">
                                <span class="badge-category">Livre</span>
                                <h3 title="<?php echo $data['titre_livres']; ?>"><?php echo $data['titre_livres']; ?></h3>
                                <h4 class="author"><i class="fa fa-pen-nib"></i> <?php echo $data["auteur_livres"]; ?></h4>
                                <div class="stock-info">
                                    <i class="fa fa-layer-group"></i> <?php echo $data["nbre_livres"]; ?> Disponible
                                </div>
                                
                                <div class="card-actions">
                                    <?php
                                    $d = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                                    $d->execute(array($_SESSION['stu_mat']));
                                    $dfetch = $d->fetch();
                                    
                                    if($dfetch[0] == 0){ ?>
                                        <button class="btn-primary reservbtnshow" id="<?php echo $data["ISBN_livres"]; ?>">
                                            Emprunter
                                        </button>
                                    <?php } else { ?>
                                        <button class="btn-disabled reservbtnshows" id="<?php echo $data["ISBN_livres"]; ?>" disabled>
                                            En attente
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="empty-state">
                        <img src="assets/undraw_bibliophile_re_xarc.svg" alt="Empty">
                        <p>Pas de livres trouvés.</p>
                        <a href="home.php?cote=all" class="btn-primary">Rafraîchir</a>
                    </div> 
                <?php } ?>
            </div>
        </section>
    </main>

    <div class="reservationFen">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Reserver ce livre</h3>
                <span class="closeReservationPopup"><i class="fa fa-xmark"></i></span>
            </div>

            <div class="modal-body">
                <div class="book-summary-modal">
                    <img src="" alt="" id="book-img">
                    <div class="summary-text">
                        <span class="bookTitle"></span>
                        <span class="bookAuteur"></span>
                    </div>
                </div>

                <div class="borrow-options">
                   <label class="option-card">
                       <input type="radio" name="mode" id="emporter" checked>
                       <div class="option-content">
                           <i class="fa fa-house"></i>
                           <span>A emporter</span>
                       </div>
                   </label>
                   <label class="option-card">
                       <input type="radio" name="mode" id="lire">
                       <div class="option-content">
                           <i class="fa fa-book-open"></i>
                           <span>Lire sur place</span>
                       </div>
                   </label>
                </div>

                <div class="info-alert">
                    <i class="fa fa-circle-info"></i>
                    <small>Une pièce d'identité sera demandée. Durée max : 72H (3 jours).</small>
                </div>

                <div class="date-inputs">
                    <div class="input-grp">
                        <label>Date de debut</label>
                        <input type="date" id="date_debut">
                    </div>
                    <div class="input-grp">
                        <label>Date de retour</label>
                        <input type="date" id="date_fin" class="emp">
                    </div>
                </div>

                <div class="hidden-inputs">
                    <input type="text" value="<?php echo $_SESSION['stu_name']; ?>" id="student_name" class="<?php echo htmlspecialchars($_SESSION['stu_mat']); ?>" disabled>
                    <input type="text" value="<?php echo $_SESSION['stu_numero']; ?>" id="student_phone" disabled>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-ghost d_cancel" style="opacity: 0">Anunler</button>
                <?php if($dfetch[0] == 0){ ?>
                    <button class="btn-primary d_add">Valider l'emprunt</button>
                <?php } else { ?>
                    <button class="btn-disabled d_addd" disabled>Non autorisé</button>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['disconnect']) || (isset($_GET['action']) && $_GET['action'] == "showCautionInfo")) { 
        $isCaution = (isset($_GET['action']) && $_GET['action'] == "showCautionInfo");
    ?>
    <div class="confirmBorrow active-backdrop">
        <div class="dialog-card">
            <?php if($isCaution) { ?>
                <div class="dialog-icon warning"><i class="fa fa-circle-exclamation"></i></div>
                <h3>Règle</h3>
                <p>Votre pénalité augmente de <strong>500 FCFA</strong> par jour.</p>
                <div class="dialog-actions">
                    <a href="home.php?cote=all" class="btn-primary full-width">compris ?</a>
                </div>
            <?php } else { ?>
                <div class="dialog-icon danger"><i class="fa fa-power-off"></i></div>
                <h3>Se déconnecter?</h3>
                <p>Êtes-vous sur de vouloir vous déconnecter?</p>
                <form method="post" class="dialog-actions">
                    <a href="home.php?cote=all" class="btn-ghost">Annuler</a>
                    <input type="submit" value="Oui" name="confirmDisconnect" class="btn-danger">
                </form>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <script src="js/home.js"></script>
</body>
</html>