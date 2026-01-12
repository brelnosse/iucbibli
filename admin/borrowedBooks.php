<?php
    session_start();
    include("config.php");
    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
    // on modifie le statut des emprunts non lu
    $getunread = $bdd->query("UPDATE emprunt SET viewedbyhost = true");

    if(isset($_POST['confirmEmprunt'])){
        if(isset($_GET['msg_type'], $_GET['stu_mat']) AND !empty($_GET['msg_type']) AND !empty($_GET['stu_mat'])){
            $getEmpruntsIDs = $bdd->query("SELECT id FROM emprunt");

            $arr;
            $id = null;
            while($getEmpruntsIDSFetch = $getEmpruntsIDs->fetch()){
                $arr[] = $getEmpruntsIDSFetch['id'];
            }
            for($i = 0; $i < count($arr); $i++){
                if($_GET['msg_type'] == sha1($arr[$i])){
                    $id = $arr[$i];
                    break;
                }
            }
            if($id != null){
                $getBorrow = $bdd->prepare("SELECT * FROM emprunt WHERE isok='false' AND date_fin < CURDATE() AND matricule_etudiant = ?");
                $getBorrow->execute(array(htmlspecialchars($_GET['stu_mat'])));

                if($getBorrow->rowCount() > 0){
                    $deleteRequestedBorrow = $bdd->prepare("DELETE FROM emprunt WHERE matricule_etudiant = ? AND isok='false'");
                    $deleteRequestedBorrow->execute(array($_GET['stu_mat']));

                    echo "<script>alert('une erreur est survenue')</script>";
                    header("location: gereremprunt.php");
                }else{
                    $validate = $bdd->prepare("UPDATE emprunt SET isok = 'true', repliedDate = CURDATE() WHERE id = ?");
                    $validate->execute(array($id));
                    $updatenbre = $bdd->query("UPDATE etudiant SET nbre_emprunt_etudiant = nbre_emprunt_etudiant + 1 WHERE matricule_etudiant = '".htmlspecialchars($_GET['stu_mat'])."'");
                    header("location: gereremprunt.php");

                }                
            }
        }
    }
    if(isset($_POST['confirmReturn'])){
        if(isset($_GET['id'], $_GET['isbn']) AND !empty($_GET['id']) AND !empty($_GET['isbn'])){
            $getReturnsIDs = $bdd->query("SELECT id FROM emprunt");

            $arr;
            $id = null;
            while($getReturnsIDSFetch = $getReturnsIDs->fetch()){
                $arr[] = $getReturnsIDSFetch['id'];
            }
            for($i = 0; $i < count($arr); $i++){
                if($_GET['id'] == sha1($arr[$i])){
                    $id = $arr[$i];
                    break;
                }
            }
            if($id != null){
                $validate = $bdd->prepare("UPDATE livres SET nbre_livres = nbre_livres + 1 WHERE ISBN_livres = ?");
                $validate->execute(array(htmlspecialchars($_GET['isbn_livres'])));

                $updateValidate = $bdd->prepare("DELETE FROM emprunt WHERE id = ?");
                $updateValidate->execute(array($id));

                header("location: gereremprunt.php");
            }else{
                echo "<script>alert('bonjour bull')</script>";
            }
        }
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
    <link rel="stylesheet" href="css/allemprunts.css"> <title>Livres Empruntés - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
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
            <li><a href="dashboard.php"><i class="fa fa-chart-pie"></i> <span class="label">Vue d'ensemble</span></a></li>
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
            <li>
                <a href="borrowedBooks.php" class="active">
                    <i class="fa fa-book-reader"></i> <span class="label">Livres Empruntés</span>
                </a>
            </li>
            <li><a href="history.php"><i class="fa fa-clock-rotate-left"></i> <span class="label">Historique</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="index.php?log" class="logout-btn"><i class="fa fa-arrow-right-from-bracket"></i> <span>Déconnexion</span></a>
        </div>
    </nav>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="page-title">
                <h1>Livres en Circulation</h1>
                <p>Liste des ouvrages actuellement empruntés.</p>
            </div>
        </header>

        <div class="content-body">
            <div class="borrowed-grid">
                <?php
                // On récupère les livres qui sont actuellement empruntés (isok='true')
                // DISTINCT pour éviter les doublons si un livre est emprunté plusieurs fois (bien que la requête cible la table livres)
                $getPopu = $bdd->query("SELECT DISTINCT * FROM livres WHERE ISBN_livres IN (SELECT isbn_livres FROM emprunt WHERE isok='true')");
                
                if($getPopu->rowCount() > 0){
                    while($data = $getPopu->fetch()){ 
                        // Compter combien d'exemplaires de CE livre sont sortis
                        $countOut = $bdd->prepare("SELECT COUNT(*) FROM emprunt WHERE isbn_livres = ? AND isok='true'");
                        $countOut->execute(array($data['ISBN_livres']));
                        $nbSortis = $countOut->fetchColumn();
                ?>
                    <div class="book-card-borrowed">
                        <div class="card-img-top">
                            <img src="<?php echo $data["couverture_livres"]?>" alt="Cover">
                            <span class="badge-borrowed">
                                <i class="fa fa-users"></i> <?php echo $nbSortis; ?> sorti(s)
                            </span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" title="<?php echo $data['titre_livres']; ?>"><?php echo $data['titre_livres']; ?></h3>
                            <p class="card-author"><?php echo $data['auteur_livres']; ?></p>
                            
                            <a href="book.php?isbn=<?php echo $data["ISBN_livres"]; ?>" class="btn-details">
                                <i class="fa fa-eye"></i> Détails
                            </a>
                        </div>
                    </div>
                <?php
                    }                  
                }else{ ?>
                    <div class="empty-state-card">
                        <img src="assets/img/undraw_No_data_re_kwbl.png" alt="Rien">
                        <h3>Aucun livre emprunté</h3>
                        <p>Tous les ouvrages sont actuellement disponibles en rayon.</p>
                        <a href="borrowedBooks.php" class="btn-refresh"><i class="fa fa-rotate-right"></i> Actualiser</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script src="js/emprunt.js"></script>
</body>
</html>