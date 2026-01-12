<?php
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: home.php");
    }
    
    $checkIfUserRequestedBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND date_fin < CURDATE() AND isok='false'");
    $checkIfUserRequestedBorrow->execute(array($_SESSION['stu_mat']));
    if($checkIfUserRequestedBorrow->rowCount() == 1){
        $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
        $deleteRequestedBorrow->execute(array($_SESSION['stu_mat']));
    }

    if(isset($_GET["del"], $_GET['stu']) AND !empty($_GET['del']) AND !empty($_GET['stu'])){
        $del = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isbn_livres = ?");
        $del->execute(array(htmlspecialchars($_GET["stu"]), htmlspecialchars($_GET["del"])));
        $set = $bdd->prepare("UPDATE livres SET nbre_livres = nbre_livres + 1 WHERE isbn_livres = ?");
        $set->execute(array(htmlspecialchars($_GET["del"])));      
        header("location: panier.php");
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="css/panier.css">
    <title>Mon panier - <?php echo htmlspecialchars($_SESSION['stu_name']); ?></title>
</head>
<body>

    <nav class="top-navbar">
        <div class="nav-left">
            <a href="home.php?cote=all" class="back-btn"><i class="fa fa-arrow-left"></i> Aller à l'accueil</a>
        </div>
        
        <div class="nav-actions">
            <a href="notification.php" class="menu-item relative">
                <i class="fa-regular fa-bell"></i>
                <?php
                    $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
                    $getnotif->execute(array($_SESSION['stu_mat']));
                    if($getnotif->rowCount() == 1){ 
                        echo '<span class="notif-dot"></span>';
                    }
                ?>
            </a>

            <a href="panier.php" class="menu-item active-cart"><i class="fa-solid fa-shopping-cart"></i></a>

            <div class="profile-dropdown">
                <div class="profile-trigger">
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['stu_name'], 0, 1)); ?></div>
                    <i class="fa fa-chevron-down"></i>
                </div>
                <div class="dropdown-content">
                    <span class="user-name">Salut, <?php echo $_SESSION['stu_name']; ?></span>
                    <a href="panier.php?disconnect" class="disconnect-btn"><i class="fa fa-arrow-right-from-bracket"></i> Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <header class="page-header">
            <h1>Vos emprunts <span class="highlight">Panier</span></h1>
            <p>Suivez vos demandes et les dates de retour.</p>
        </header>

        <div class="cart-container">
            <?php
                $getUser  = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ?");
                $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
                $fetchUser = $getUser->fetch();

                if($getUser->rowCount() == 1){ 
                    $getbookdetails = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                    $getbookdetails->execute(array($fetchUser['isbn_livres']));
                    $getBook = $getbookdetails->fetch();
            ?>
            
            <div class="cart-actions">
                <?php if($fetchUser["isok"] == "true"){ ?>
                    <button class="btn-disabled" title="Action impossible pendant l'emprunt">
                        <i class="fa fa-lock"></i> Vider le panier
                    </button>
                <?php }else{ ?>
                    <a href="#" id="<?php echo $_SESSION['stu_mat']; ?>" class="btn-ghost removefrompanier">
                        <i class="fa-regular fa-trash-can"></i> Annuler
                    </a>
                <?php } ?>
            </div>

            <div class="book-card-horizontal" id="<?php echo $getBook["ISBN_livres"]; ?>">
                <div class="book-cover-wrapper">
                    <img src="admin/<?php echo $getBook["couverture_livres"]; ?>" alt="Cover" class="couverture">
                </div>
                
                <div class="book-info">
                    <div class="info-header">
                        <?php 
                            if($fetchUser["isok"] == "false"){
                                echo "<span class='badge warning'><i class='fa fa-clock'></i>  En attente d'approbation</span>";
                            } elseif($fetchUser["isok"] == "true"){
                                echo "<span class='badge success'><i class='fa fa-check'></i> Approvée</span>";
                            }
                        ?>
                        <h2 class="bookTitle"><?php echo $getBook["titre_livres"]; ?></h2>
                        <span class="bookAuth">Par <?php echo $getBook["auteur_livres"]; ?></span>
                    </div>

                    <div class="info-status">
                        <?php 
                            if($fetchUser["isok"] == "true"){
                                $today = new DateTime($fetchUser["date_debut"]); 
                                $end = new DateTime($fetchUser["date_fin"]); 
                                $diff = $today->diff($end);
                                $diffday = $diff->days;
            
                                if($diffday == 0){
                                    echo "<div class='alert-box danger'><i class='fa fa-circle-exclamation'></i> Le prêt a expiré <b>Aujourd'hui</b>.</div>";
                                }else{
                                    $dateToday = date("Y/m/d");
                                    $datetime = new DateTime($dateToday);
                                    $lefts = $end->diff($datetime);

                                    if($end < $datetime){
                                        echo "<div class='alert-box danger'><i class='fa fa-triangle-exclamation'></i> En retard de <b>".$lefts->days." Jours</b>. Merci de revenir immédiatement.</div>";
                                    }else{
                                        if($lefts->days == 1){
                                             // Logique de notif interne conservée (Update view)
                                            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
                                            $getnotif->execute(array($_SESSION['stu_mat']));
                                            if($getnotif->rowCount() == 0){
                                                $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                                                $addnotif->execute(array('student_mat' => $_SESSION['stu_mat'], 'date_fin' => $fetchUser['date_fin'], 'day_left' => $lefts->days));
                                            } else {
                                                $upd = $bdd->prepare("UPDATE notification SET viewed = 'false' WHERE student_mat = ?");
                                                $upd->execute(array($_SESSION['stu_mat']));
                                            }
                                            echo "<div class='alert-box warning'><i class='fa fa-clock'></i> Expire dans <b>1 jour</b>.</div>";
                                        }
                                        elseif($lefts->days == 0){
                                            // Logique notif jour J
                                            $getnotif = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'true'");
                                            $getnotif->execute(array($_SESSION['stu_mat']));
                                            if($getnotif->rowCount() == 0){
                                                $addnotif = $bdd->prepare("INSERT INTO notification (student_mat, date_fin, day_left) VALUES(:student_mat, :date_fin, :day_left)");
                                                $addnotif->execute(array('student_mat' => $_SESSION['stu_mat'], 'date_fin' => $fetchUser['date_fin'], 'day_left' => $lefts->days));
                                            }
                                            echo "<div class='alert-box danger'><i class='fa fa-triangle-exclamation'></i> Retour attendu <b>Aujourd'hui</b>.</div>";
                                        }
                                        else{
                                            // Période normale
                                            if($today == $datetime){
                                                echo "<div class='alert-box info'><i class='fa fa-calendar-check'></i> Débuté aujourd'hui. Durée : <b>".$diffday." Jours</b>.</div>";
                                            } elseif($today < $datetime){
                                                echo "<div class='alert-box success'><i class='fa fa-hourglass-half'></i> <b>".$lefts->days." Days</b> restant.</div>";
                                            } else {
                                                echo "<div class='alert-box info'><i class='fa fa-calendar'></i> débute le: <b>".$fetchUser["date_debut"]."</b>.</div>";
                                            }
                                        }
                                    }
                                }
                            } else {
                                echo "<div class='alert-box neutral'><i class='fa fa-spinner'></i> En attente de la validation du bibliothécaire.</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>

            <?php
                } else {
                    if($getUser->rowCount() == 0){ ?>
                        <div class="empty-state">
                            <img src="assets/undraw_bibliophile_re_xarc.svg" alt="Empty Cart">
                            <h2>Votre panier est vide</h2>
                            <p>Il semblerait que vous n'ayez encore emprunté aucun livre.</p>
                            <a href="home.php?cote=all" class="btn-primary">Parcourir la bibliothèque</a>
                        </div>
            <?php   }
                }
            ?>
        </div>
    </main>

    <?php if(isset($_GET['disconnect'])){ ?>
        <div class="confirmBorrow active-backdrop">
            <div class="dialog-card">
                <div class="dialog-icon danger"><i class="fa fa-power-off"></i></div>
                <h3>Se déconnecter?</h3>
                <p>Êtes-vous sur de vouloir vous déconnecter?</p>
                <form method="post" class="dialog-actions">
                    <a href="home.php?cote=all" class="btn-ghost">Annuler</a>
                    <input type="submit" value="Oui" name="confirmDisconnect" class="btn-danger">
                </form>
            </div>
        </div>
    <?php } ?>

    <script>
        const viderPanierBtn = document.querySelector(".removefrompanier");
        const bookCard = document.querySelector(".book-card-horizontal");
        
        if(viderPanierBtn) {
            viderPanierBtn.addEventListener("click", function(e){
                e.preventDefault();
                // Si le bouton est disabled (classe CSS), on ne fait rien
                if(this.classList.contains('btn-disabled')) return;

                const isbnLivre = bookCard.id;
                const studentId = this.id;
                
                // On stylise la confirmation native ou on pourrait faire une modale custom
                if(confirm("Êtes-vous sûr de vouloir annuler cette demande ? Cette action est irréversible.")){
                    window.location = "panier.php?del="+isbnLivre+"&stu="+studentId;
                }
            });
        }
    </script>
</body>
</html>