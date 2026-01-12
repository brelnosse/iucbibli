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
        if(isset($_GET['id'], $_GET['isbn'], $_GET['nom'], $_GET['phone'], $_GET['debut'], $_GET['fin']) AND !empty($_GET['id']) AND !empty($_GET['isbn']) AND !empty($_GET['debut']) AND !empty($_GET['fin']) AND !empty($_GET['phone']) AND !empty($_GET['nom'])){
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
                $validate->execute(array(htmlspecialchars($_GET['isbn'])));
                $getIsbnLivre = $bdd->query("SELECT isbn_livres FROM emprunt WHERE id = ".$id);
                $ds = $getIsbnLivre->fetch();
                $addToHistory = $bdd->prepare("INSERT INTO historique(isbn_livres, nom_etudiant, numero_etudiant, date_debut, date_fin, repliedDate, accorder) VALUES(?, ?, ?, ?, ?, CURDATE(), ?)");
                $addToHistory->execute(array(
                    $ds['isbn_livres'],
                    htmlspecialchars($_GET['nom']),
                    htmlspecialchars($_GET['phone']),
                    htmlspecialchars($_GET['debut']),
                    htmlspecialchars($_GET['fin']),
                    'Accorder'
                ));
                $delValidate = $bdd->prepare("DELETE FROM caution WHERE mat_etu IN (SELECT matricule_etudiant FROM emprunt WHERE id = ?)");
                $delValidate->execute(array($id));
                $updateValidate = $bdd->prepare("DELETE FROM emprunt WHERE id = ?");
                $updateValidate->execute(array($id));
                header("location: gereremprunt.php");
            }else{
                echo "<script>alert('bonjour bull')</script>";
            }
        }
    }
    if(isset($_POST['confirmSupp'])){
        if(isset($_GET['id'], $_GET['action'], $_GET['isbn'], $_GET['nom'], $_GET['phone'], $_GET['debut'], $_GET['fin']) AND !empty($_GET['id']) AND !empty($_GET['action']) AND !empty($_GET['isbn']) AND !empty($_GET['debut']) AND !empty($_GET['fin']) AND !empty($_GET['phone']) AND !empty($_GET['nom'])){
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
                $validate = $bdd->prepare("UPDATE livres SET nbre_livres = nbre_livres + 1 WHERE ISBN_livres IN (SELECT isbn_livres FROM emprunt WHERE id = ?)");
                $validate->execute(array($id));

                $getIsbnLivre = $bdd->query("SELECT isbn_livres FROM emprunt WHERE id = ".$id);
                $ds = $getIsbnLivre->fetch();
                $addToHistory = $bdd->prepare("INSERT INTO historique(isbn_livres, nom_etudiant, numero_etudiant, date_debut, date_fin, repliedDate, accorder) VALUES(?, ?, ?, ?, ?, CURDATE(), ?)");
                $addToHistory->execute(array(
                    $ds['isbn_livres'],
                    htmlspecialchars($_GET['nom']),
                    htmlspecialchars($_GET['phone']),
                    htmlspecialchars($_GET['debut']),
                    htmlspecialchars($_GET['fin']),
                    'Rejeter'
                ));
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
    
    <link rel="stylesheet" href="css/dashboard.css"/> <link rel="stylesheet" href="css/emprunt.css"/>   <title>Gérer Emprunts - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
    <link rel="stylesheet" href="css/emprunt.css"/> <link rel="stylesheet" href="css/emprunt.css"/>   <title>Gérer Emprunts - <?php echo htmlspecialchars($_SESSION['email']); ?></title>
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
                <a href="gereremprunt.php" class="active notif-link">
                    <i class="fa fa-list-check"></i> <span class="label">Gérer Emprunts</span>
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
                <h1>Gestion des Emprunts</h1>
                <p>Validez les demandes et suivez les retours.</p>
            </div>
        </header>

        <div class="content-body">
            
            <div class="filter-card">
                <form method="post" class="filter-form">
                    <div class="filter-group">
                        <label><i class="fa fa-magnifying-glass"></i> Par Nom/Tél</label>
                        <div class="input-wrapper">
                            <input type="text" name="search" placeholder="Ex: Jean Dupont..." value="<?php if(isset($_POST['search'])) echo htmlspecialchars($_POST['search']); ?>">
                            <button type="submit" name="research" class="btn-filter">Chercher</button>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label><i class="fa fa-calendar-days"></i> Par Date</label>
                        <div class="input-wrapper">
                            <input type="date" name="date" value="<?php if(isset($_POST['date'])) echo htmlspecialchars($_POST['date']); ?>">
                            <button type="submit" name="apply" class="btn-filter">Filtrer</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Livre</th>
                                <th>Etudiant</th>
                                <th>Téléphone</th>
                                <th>Début</th>
                                <th>Fin / Statut</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            // Logique de recherche
                            $getBooks;
                            if(isset($_POST['research']) && !empty($_POST['search'])){
                                $getBooks = $bdd->query("SELECT * FROM emprunt WHERE nom_etudiant LIKE '%".htmlspecialchars($_POST['search'])."%' OR numero_etudiant LIKE '%".htmlspecialchars($_POST['search'])."%'");
                            } elseif(isset($_POST['apply']) && !empty($_POST['date'])){
                                $getBooks = $bdd->prepare("SELECT * FROM emprunt WHERE date_debut = ? OR date_fin = ?");
                                $getBooks->execute(array(htmlspecialchars($_POST['date']), htmlspecialchars($_POST['date'])));
                            } else {
                                $getBooks = $bdd->query("SELECT * FROM emprunt ORDER BY date_debut DESC");
                            }

                            if($getBooks->rowCount() > 0){
                                while($book = $getBooks->fetch()){ 
                                    // Recup titre livre
                                    $getnom = $bdd->prepare("SELECT titre_livres FROM livres WHERE ISBN_livres = ?");
                                    $getnom->execute(array(htmlentities($book["isbn_livres"])));
                                    $bookTitle = $getnom->fetch()['titre_livres'];
                        ?> 
                            <tr>
                                <td class="fw-bold text-primary">
                                    <a href="book.php?isbn=<?php echo $book["isbn_livres"]; ?>"><?php echo $bookTitle; ?></a>
                                </td>
                                <td><?php echo $book["nom_etudiant"]; ?></td>
                                <td><?php echo $book["numero_etudiant"]; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($book["date_debut"])); ?></td>
                                
                                <td>
                                    <?php 
                                        if($book['isok'] == 'true'){
                                            $dateFin = new DateTime($book["date_fin"]);
                                            $todayDate = new DateTime(date("Y/m/d"));
                                            $interval = $dateFin->diff($todayDate);
                                            
                                            if($dateFin == $todayDate){
                                                echo '<div class="status-warning"><i class="fa fa-triangle-exclamation"></i> Fin aujourd\'hui</div>';
                                            } elseif($dateFin < $todayDate){
                                                // Gestion Caution (Logique conservée mais affichage simplifié)
                                                echo '<div class="status-danger"><i class="fa fa-circle-xmark"></i> Retard: '.$interval->days.'j</div>';
                                                
                                                // Update caution logic (Keep it invisible/backend or minimal)
                                                $getUserCaution = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
                                                $getUserCaution->execute(array($book['matricule_etudiant']));
                                                if($getUserCaution->rowCount() == 0){
                                                    $AddCaution = $bdd->prepare("INSERT INTO caution(mat_etu, nom_etu, caution, date_dernier_ajout) VALUES(?, ?, ?, CURDATE())");
                                                    $AddCaution->execute(array($book['matricule_etudiant'], $book["nom_etudiant"], 500*$interval->days));
                                                } else {
                                                    $updateCaution = $bdd->prepare("UPDATE caution SET caution = caution* ".$interval->days.", date_dernier_ajout = CURDATE() WHERE mat_etu = ? AND date_dernier_ajout != CURDATE()");
                                                    $updateCaution->execute(array($book['matricule_etudiant']));
                                                }
                                            } else {
                                                echo '<div class="status-ok">Fin: '.date('d/m/Y', strtotime($book["date_fin"])).'<br><small>Reste '.$interval->days.'j</small></div>';
                                            }
                                        } else {
                                            echo date('d/m/Y', strtotime($book["date_fin"]));
                                        }
                                    ?>
                                </td>

                                <td>
                                    <?php if($book['isok'] == 'false'){ ?>
                                        <span class="badge badge-warning">En attente</span>
                                    <?php } else { ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php } ?>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                    <?php if($book['isok'] == 'false'){ ?>
                                        <a href="gereremprunt.php?msg_type=<?php echo sha1($book["id"]); ?>&stu_mat=<?php echo $book["matricule_etudiant"]; ?>" 
                                           class="btn-icon btn-accept" title="Accepter">
                                           <i class="fa fa-check"></i>
                                        </a>
                                        <a href="gereremprunt.php?action=delete&id=<?php echo sha1($book["id"]); ?>&isbn=<?php echo sha1($book["isbn_livres"]); ?>&nom=<?php echo $book["nom_etudiant"]; ?>&phone=<?php echo $book["numero_etudiant"]; ?>&debut=<?php echo $book["date_debut"]; ?>&fin=<?php echo $book["date_fin"]; ?>" 
                                           class="btn-icon btn-reject" title="Refuser">
                                           <i class="fa fa-xmark"></i>
                                        </a>
                                    <?php } else { ?>
                                        <a href="book.php?isbn=<?php echo $book["isbn_livres"]; ?>" class="btn-icon btn-view" title="Voir Livre"><i class="fa fa-eye"></i></a>
                                        
                                        <?php
                                            $endDate = new DateTime($book["date_fin"]);
                                            $today = new DateTime(date("Y/m/d"));
                                            $params = "id=".sha1($book["id"])."&isbn=".sha1($book["isbn_livres"])."&nom=".$book["nom_etudiant"]."&phone=".$book["numero_etudiant"]."&debut=".$book["date_debut"]."&fin=".$book["date_fin"];
                                            
                                            // Bouton Retour Normal ou avec Caution
                                            if($today > $endDate){ $params .= "&caution=".$book["id"]; }
                                        ?>
                                        <a href="gereremprunt.php?<?php echo $params; ?>" 
                                           class="btn-icon btn-return" title="Confirmer le retour">
                                           <i class="fa fa-share-from-square"></i>
                                        </a>
                                    <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                                }
                            } else { 
                        ?>
                            <tr>
                                <td colspan="7" class="empty-row">
                                    <img src="assets/img/undraw_No_data_re_kwbl.png" alt="Vide">
                                    <p>Aucun emprunt trouvé.</p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php if((isset($_GET['msg_type']) && !empty($_GET['msg_type'])) || (isset($_GET['id']) && !empty($_GET['id'])) || (isset($_GET['action']) && !empty($_GET['action']))){ 
        
        $modalTitle = "Confirmation";
        $modalMsg = "Êtes-vous sûr ?";
        $formName = "";
        $btnClass = "btn-primary";
        $btnText = "Confirmer";

        if(isset($_GET['msg_type'])){
            $modalTitle = "Valider l'emprunt";
            $modalMsg = "Une fois accepté, le stock du livre sera décrémenté.";
            $formName = "confirmEmprunt";
            $btnClass = "btn-success";
        } 
        elseif(isset($_GET['action']) && $_GET['action'] == 'delete'){
            $modalTitle = "Refuser la demande";
            $modalMsg = "Êtes-vous sûr de vouloir refuser cet emprunt ?";
            $formName = "confirmSupp";
            $btnClass = "btn-danger";
            $btnText = "Refuser";
        }
        elseif(isset($_GET['id'])){
            $modalTitle = "Retour de livre";
            $formName = "confirmReturn";
            $btnText = "Confirmer le retour";
            $btnClass = "btn-return-confirm";
            
            if(isset($_GET['caution']) && !empty($_GET['caution'])){ 
                $getCaution = $bdd->prepare("SELECT caution FROM caution WHERE mat_etu IN (SELECT matricule_etudiant FROM emprunt  WHERE id = ?)");
                $getCaution->execute(array($_GET['caution']));
                if($getCaution->rowCount() > 0){
                    $amount = $getCaution->fetch()['caution'];
                    $modalMsg = "<div class='alert-box danger'><i class='fa fa-money-bill-wave'></i> Retard détecté !</div><br>Assurez-vous de percevoir <b>".$amount." FCFA</b> de pénalité avant de récupérer le livre.";
                }
            } else {
                $modalMsg = "Le livre a-t-il bien été rendu en bon état ?";
            }
        }
    ?>
    <div class="confirmBorrow active-backdrop">
        <div class="dialog-card">
            <div class="dialog-icon warning"><i class="fa fa-circle-question"></i></div>
            <h3><?php echo $modalTitle; ?></h3>
            <div class="dialog-body-text"><?php echo $modalMsg; ?></div>
            
            <form method="POST" class="dialog-actions">
                <a href="gereremprunt.php" class="btn-ghost">Annuler</a>
                <input type="submit" value="<?php echo $btnText; ?>" name="<?php echo $formName; ?>" class="<?php echo $btnClass; ?>">
            </form>
        </div>
    </div>
    <?php } ?>

    <script src="js/emprunt.js"></script>
</body>
</html>