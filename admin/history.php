<?php
    session_start();
    include("config.php");
    if(!isset($_SESSION['email'])){
        header("location:index.php");
    }
    
    // --- LOGIQUE EMPRUNTS & RETOURS (Inchangée) ---
    // Update statuts lus
    $getunread = $bdd->query("UPDATE emprunt SET viewedbyhost = true");

    // Confirmation Emprunt
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
    
    // Confirmation Retour
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
                echo "<script>alert('Erreur ID')</script>";
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
    
    <link rel="stylesheet" href="css/dashboard.css"> <link rel="stylesheet" href="css/history.css">   <title>Historique - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
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
            <li><a href="borrowedBooks.php"><i class="fa fa-book-reader"></i> <span class="label">Livres Empruntés</span></a></li>
            <li><a href="history.php" class="active"><i class="fa fa-clock-rotate-left"></i> <span class="label">Historique</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <a href="index.php?log" class="logout-btn"><i class="fa fa-arrow-right-from-bracket"></i> <span>Déconnexion</span></a>
        </div>
    </nav>

    <main class="main-content">
        
        <header class="top-bar">
            <div class="page-title">
                <h1>Historique des Emprunts</h1>
                <p>Consultez l'archive de toutes les transactions passées.</p>
            </div>
        </header>

        <div class="content-body">
            <div class="card table-card">
                <div class="card-header">
                    <h2>Archives</h2>
                    </div>

                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Livre</th>
                                <th>Etudiant</th>
                                <th>Contact</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th>Retourné le</th>
                                <th>Décision</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $getHistory = $bdd->query("SELECT * FROM historique ORDER BY repliedDate DESC");

                            if($getHistory->rowCount() > 0){
                                while($row = $getHistory->fetch()){ 
                                    // Récupération Titre Livre
                                    $getBookTitle = $bdd->prepare("SELECT titre_livres FROM livres WHERE ISBN_livres = ?");
                                    $getBookTitle->execute(array($row["isbn_livres"]));
                                    $bookTitle = $getBookTitle->fetch()['titre_livres'] ?? 'Livre inconnu';
                        ?> 
                            <tr>
                                <td class="fw-bold text-primary">
                                    <a href="book.php?isbn=<?php echo $row["isbn_livres"]; ?>" class="book-link">
                                        <i class="fa fa-book"></i> <?php echo $bookTitle; ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <span class="user-name"><?php echo $row["nom_etudiant"]; ?></span>
                                        <small class="user-id"><?php echo $row["numero_etudiant"]; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $row["numero_etudiant"]; // Si c'est le téléphone, sinon ajuster ?></td>
                                <td>
                                    <div class="date-range">
                                        <span class="date-start"><i class="fa fa-play"></i> <?php echo $row["date_debut"]; ?></span>
                                        <span class="date-end"><i class="fa fa-stop"></i> <?php echo $row["date_fin"]; // Assumed column name ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-green"><i class="fa fa-check-circle"></i> Terminé</span>
                                </td>
                                <td>
                                    <span class="date-return"><?php echo $row["repliedDate"]; ?></span>
                                </td>
                                <td>
                                    <?php if($row["accorder"] == "Accorder" || $row["accorder"] == "true"){ ?>
                                        <span class="status-text success"><i class="fa fa-circle-check"></i> Accordé</span>
                                    <?php }else{ ?>
                                        <span class="status-text danger"><i class="fa fa-circle-xmark"></i> Refusé</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php 
                                }
                            } else { 
                        ?>
                            <tr>
                                <td colspan="7" class="empty-row">
                                    <img src="assets/img/undraw_No_data_re_kwbl.png" alt="Vide">
                                    <p>Aucun historique disponible pour le moment.</p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
</html>