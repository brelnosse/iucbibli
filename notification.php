<?php
    session_start();
    include("admin/config.php");
    if(!isset($_SESSION['stu_mat'])){
        header("location: home.php");
    }
    
    // Logique de déconnexion
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

    <link rel="stylesheet" href="css/notification.css">
    <title>Notifications - <?php echo htmlspecialchars($_SESSION['stu_name']); ?></title>
</head>
<body>

    <nav class="top-navbar">
        <div class="nav-left">
            <a href="home.php?cote=all" class="back-btn"><i class="fa fa-arrow-left"></i> Retour</a>
        </div>
        
        <div class="nav-actions">
            <a href="notification.php" class="menu-item active-notif">
                <i class="fa-solid fa-bell"></i>
            </a>

            <a href="panier.php" class="menu-item"><i class="fa-solid fa-cart-shopping"></i></a>

            <div class="profile-dropdown">
                <div class="profile-trigger">
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['stu_name'], 0, 1)); ?></div>
                    <i class="fa fa-chevron-down"></i>
                </div>
                <div class="dropdown-content">
                    <span class="user-name">Salut, <?php echo $_SESSION['stu_name']; ?></span>
                    <a href="notification.php?disconnect" class="disconnect-btn"><i class="fa fa-arrow-right-from-bracket"></i> Deconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <header class="page-header">
            <h1>Centre de <span class="highlight">Notifications</span></h1>
            <p>Restez informé sur vos emprunts et demandes.</p>
        </header>

        <div class="notif-container">
            
            <section class="notif-section">
                <h3 class="section-title"><i class="fa fa-circle-exclamation"></i> Message important</h3>
                
                <?php 
                    $get = $bdd->prepare("SELECT * FROM notification WHERE student_mat = ? AND viewed = 'false'");
                    $get->execute(array(htmlspecialchars($_SESSION['stu_mat'])));  

                    if($get->rowCount() == 1){
                        $getd = $get->fetch();
                ?>
                    <div class="notif-card warning">
                        <div class="icon-box">
                            <i class="fa fa-triangle-exclamation"></i>
                        </div>
                        <div class="content-box">
                            <h4>Attention</h4>
                            <p>Votre emprunt arrive a terme dans <b><?php echo $getd["day_left"]; ?> Jours</b></p>
                        </div>
                    </div>
                <?php
                    }else{
                        echo "<div class='empty-message'>Aucun message important pour le moment .</div>";
                    }
                ?>
            </section>

            <section class="notif-section">
                <h3 class="section-title"><i class="fa fa-envelope-open-text"></i> Message de l'administrateur</h3>
                
                <div class="notif-list">
                <?php
                    $getUser = $bdd->prepare("SELECT * FROM emprunt WHERE matricule_etudiant = ? AND isok != 'false'");
                    $getUser->execute(array(htmlspecialchars($_SESSION['stu_mat'])));
                    
                    if($getUser->rowCount() > 0){
                        while($fetchUser = $getUser->fetch()){
                            $getBook = $bdd->prepare("SELECT * FROM livres WHERE ISBN_livres = ?");
                            $getBook->execute(array(htmlspecialchars($fetchUser['isbn_livres'])));
            
                            if($getBook->rowCount() == 1){ 
                                $book = $getBook->fetch();
                                
                                // CAS: REFUS
                                if($fetchUser['isok'] == "refus"){ ?>
                                    <div class="notif-card danger">
                                        <div class="icon-box">
                                            <i class="fa fa-times"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="notif-header">
                                                <h4>Demande Refusée</h4>
                                                <span class="date-badge"><?php echo $fetchUser["repliedDate"]; ?></span>
                                            </div>
                                            <p>Votre demande d'emprunt pour le livre <b><?php echo $book["titre_livres"]; ?></b> a ete accepter refuser<?php echo $fetchUser['date_debut'];?></p>
                                        </div>
                                    </div>
                                <?php
                                } else { 
                                    // CAS: ACCEPTE (Non vu ou Vu)
                                    $isNew = ($fetchUser['viewedbystudent'] == 'false') ? 'new-glow' : '';
                                ?>
                                    <div class="notif-card success <?php echo $isNew; ?>">
                                        <div class="icon-box">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="notif-header">
                                                <h4>Demande Approuvée</h4>
                                                <span class="date-badge"><?php echo $fetchUser["repliedDate"]; ?></span>
                                            </div>
                                            <p>Votre demande d'emprunt pour le livre <b>"<?php echo $book["titre_livres"]; ?>"</b> a ete accepter et debute le <?php echo $fetchUser['date_debut'];?></p>
                                        </div>
                                    </div>
                                <?php
                                }
                            }
                        }
                    } else { ?>
                        <div class="empty-state">
                            <img src="assets/undraw_bibliophile_re_xarc.svg" alt="Rien">
                            <p>Vous n'avez pas encore de notifications.</p>
                        </div>
                    <?php } ?>
                </div>
            </section>
        </div>
    </main>

    <?php if(isset($_GET['disconnect'])){ ?>
        <div class="confirmBorrow active-backdrop">
            <div class="dialog-card">
                <div class="dialog-icon danger"><i class="fa fa-power-off"></i></div>
                <h3>Deconnexion ?</h3>
                <p>Etes-vous sur de vouloir vous deconnecter ?</p>
                <form method="post" class="dialog-actions">
                    <a href="notification.php" class="btn-ghost">annuler</a>
                    <input type="submit" value="Oui" name="confirmDisconnect" class="btn-danger">
                </form>
            </div>
        </div>
    <?php } ?>

</body>
</html>

<?php
    $getnotif = $bdd->prepare("UPDATE notification SET viewed = 'true' WHERE student_mat = ?");
    $getnotif->execute(array($_SESSION['stu_mat']));

    $setView = $bdd->prepare("UPDATE emprunt SET viewedbystudent = 'true' WHERE matricule_etudiant = ?");
    $setView->execute(array($_SESSION['stu_mat']));
?>