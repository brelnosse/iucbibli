<?php 
    session_start();
    include("admin/config.php");
    $isbn;
    if(!isset($_GET['isbn']) || empty($_GET['isbn']) || !isset($_SESSION['stu_mat']) || empty($_SESSION['stu_mat'])){
        header("location:home.php");
    }
    
    $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
    $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
    $fetchUser = $getUser->fetch();
    
    if($getUser->rowCount() > 0){
        $dateToday = date("Y/m/d");
        $datetime = new DateTime($dateToday);
        $end = new DateTime($fetchUser["date_fin"]); 
        $lefts = $end->diff($datetime);

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

    if(isset($_GET['isbn']) AND !empty($_GET['isbn']) AND isset($_SESSION['stu_mat']) AND !empty($_SESSION['stu_mat'])){
        $getViewer = $bdd->prepare("SELECT COUNT(*) FROM vue WHERE book_isbn = ? AND user_matricule = ?");
        $getViewer->execute(array(htmlspecialchars($_GET['isbn']), htmlspecialchars($_SESSION['stu_mat'])));

        $vue = $getViewer->fetch();
        if($vue[0] == 0){
            $getViewer = $bdd->prepare("INSERT INTO vue(book_isbn, user_matricule) VALUES(?, ?)");
            $getViewer->execute(array(htmlspecialchars($_GET['isbn']), htmlspecialchars($_SESSION['stu_mat'])));            
        }
        $isbn = htmlspecialchars($_GET['isbn']);
    }

    // --- DECONNEXION ---
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="css/book.css">
    <title>Détails du Livre</title>
</head>
<body>

    <nav class="top-navbar">
        <div class="nav-left">
            <a href="home.php?cote=all" class="back-btn"><i class="fa fa-arrow-left"></i> Retour</a>
        </div>
        
        <div class="nav-actions">
            <a href="notification.php" class="menu-item relative">
                <i class="fa-regular fa-bell"></i>
                <?php
                    $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
                    $getnotif->execute(array($_SESSION['stu_mat']));
                    if($getnotif->rowCount() == 1){ echo '<span class="notif-dot"></span>'; }
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
                    <a href="book.php?isbn=<?php echo $isbn; ?>&disconnect" class="disconnect-btn"><i class="fa fa-arrow-right-from-bracket"></i> Deconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <?php 
            $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
            $getBook->execute(array(htmlspecialchars($_GET['isbn'])));

            if($getBook->rowCount() == 1){ 
                while($book = $getBook->fetch()){ 
        ?>
            <div class="book-detail-card">
                <div class="book-cover-large">
                    <img src="admin/<?php echo $book['couverture_livres']; ?>" alt="Couverture">
                </div>

                <div class="book-info-large">
                    <div class="info-header">
                        <h1 class="book__title"><?php echo $book['titre_livres']; ?></h1>
                        <p class="book__author">par <span id="auteur"><?php echo $book['auteur_livres']; ?></span></p>
                    </div>

                    <div class="meta-grid">
                        <div class="meta-item">
                            <label>ISBN</label>
                            <span id="isbn"><?php echo $book['ISBN_livres']; ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Disponibilité</label>
                            <span class="stock-badge">
                                <i class="fa fa-layer-group"></i> <span id="expl"><?php echo $book['nbre_livres']; ?></span> en stock
                            </span>
                        </div>
                        <div class="meta-item">
                            <label>Popularité</label>
                            <span class="views-badge">
                                <i class="fa fa-eye"></i>
                                <span id="vue">
                                    <?php 
                                        $getViews = $bdd->prepare("SELECT * FROM vue WHERE book_isbn = ?");
                                        $getViews->execute(array($book['ISBN_livres']));
                                        echo $getViews->rowCount();
                                    ?>
                                </span> vues
                            </span>
                        </div>
                    </div>

                    <div class="action-area">
                        <?php
                            $d = $bdd->prepare("SELECT COUNT(matricule_etudiant) FROM emprunt WHERE matricule_etudiant = ?");
                            $d->execute(array($_SESSION['stu_mat']));
                            $dfetch = $d->fetch();
                            
                            if($dfetch[0] == 0){ ?>
                                <button class="btn-primary reserv" id="<?php echo htmlspecialchars($book['ISBN_livres']); ?>">
                                    <i class="fa fa-bookmark"></i> Faire une reservation
                                </button>
                            <?php } else { ?>
                                <button class="btn-disabled reser" disabled>
                                    <i class="fa fa-clock"></i> Emprunt en cours
                                </button>
                            <?php } ?>
                    </div>
                </div>
            </div>
        <?php 
                }
            } else { 
        ?>
            <div class="empty-state">
                <img src="assets/undraw_bibliophile_re_xarc.svg" alt="Introuvable">
                <h2>Livre introuvable</h2>
                <a href="home.php?cote=all" class="btn-ghost">Retour à l'accueil</a>
            </div>
        <?php } ?>
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
                <button class="btn-ghost d_cancel">Annuler</button>
                <?php if($dfetch[0] == 0){ ?>
                    <button class="btn-primary d_add">Valider l'emprunt</button>
                <?php } else { ?>
                    <button class="btn-disabled d_addd" disabled>Non autorisé</button>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['disconnect'])){ ?>
        <div class="confirmBorrow active-backdrop">
            <div class="dialog-card">
                <div class="dialog-icon danger"><i class="fa fa-power-off"></i></div>
                <h3>Deconnexion ?</h3>
                <p>Etes-vous sur de vouloir vous deconnecter ?</p>
                <form method="post" class="dialog-actions">
                    <a href="book.php?isbn=<?php echo $isbn; ?>" class="btn-ghost">annuler</a>
                    <input type="submit" value="Oui" name="confirmDisconnect" class="btn-danger">
                </form>
            </div>
        </div>
    <?php } ?>

    <script src="js/book.js"></script>
</body>
</html>