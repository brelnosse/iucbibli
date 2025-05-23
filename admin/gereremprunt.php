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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/fontawesome.css">
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/brands.css"/>
    <link rel="stylesheet" href="assets/fontawesome-free-6.5.2-web/css/solid.css"/>
    <link rel="stylesheet" href="css/emprunt.css"/>
    <title><?php 
        if(isset($_SESSION['email'])){
            echo htmlspecialchars($_SESSION['email']);
        }
    ?></title>
</head>
<body>
    <div class="toolsmenu">
        <div class="toolsmenu__header">
            <span class="toolsmenu__header--userpp">
                <i class="fa-solid fa-user"></i>
            </span>
            <span class="toolsmenu__header--useremail">
                <?php
                    echo htmlspecialchars($_SESSION['email']);
                ?>
            </span>
        </div>
        <div class="toolsmenu__body">
            <a href="addbook.php"><i class="fa fa-plus"></i> <span class="label">Ajouter un livre</span></a>
            <a href="dashboard.php"><i class="fa fa-eye"></i> <span class="label">Afficher les livres</span></a>
            <a href="gereremprunt.php" class="active"><i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span></a>
            <a href="borrowedBooks.php"><i class="fa fa-bullseye"></i> <span class="label">Afficher les livres emprunter</span></a>
            <a href="history.php"><i class="fa fa-history"></i> <span class="label">Historique des emprunts</span></a>
        </div>
        <div class="toolsmenu__footer">
            <a href="index.php?log"><i class="fa fa-sign-out-alt"></i></a>
        </div>
    </div>
    <div class="container">
        <div class="container__title">
            <span>Tableau de bord / </span>
            <a href="dashboard.php" style="margin-left: 2px"> Tous les emprunts</a>
        </div>
        <div class="container__option">
        <form method="post" class="container__option">
            <div class="row">
                <p>Rechercher un emprunt</p>
                <div class="searchContainer">
                    <input type="text" name="search" placeholder="Entrez un nom ou num&eacute;ro de telephone" value="<?php 
                        if(isset($_POST['search'])){
                            echo htmlspecialchars($_POST['search']);
                        }
                    ?>">
                    <input type="submit" name="research" value="Rechercher">
                </div>
            </div>
            <div class="row">
                <p>Rechercher un emprunt en fonction d'une date</p>
                <div class="searchContainer">
                    <input type="date" name="date" placeholder="Entrez une date" value="<?php 
                        if(isset($_POST['date'])){
                            echo htmlspecialchars($_POST['date']);
                        }
                    ?>">
                    <input type="submit" name="apply" value="Appliquer">
                </div>
            </div>
        </form>
        </div>
        <div class="container__body">
            <table class="container__table">
                <tr class="container__body--titles-container">
                    <th class="container__body--title">Titre</th>
                    <th class="container__body--title">nom etudiant</th>
                    <th class="container__body--title">Numero de telephone</th>
                    <th class="container__body--title">Date de debut</th>
                    <th class="container__body--title">Date de fin</th>
                    <th class="container__body--title">Statut</th>
                    <th class="container__body--title">Action</th>
                </tr>
                <?php 
                    $count = 0;
                    $getBooks;
                    if(isset($_POST['research'])){
                        if(isset($_POST['search']) AND !empty($_POST['search'])){
                            $getBooks = $bdd->query("SELECT * FROM emprunt WHERE nom_etudiant LIKE '%".htmlspecialchars($_POST['search'])."%' OR numero_etudiant LIKE '%".htmlspecialchars($_POST['search'])."%'");
                        }else{
                            $getBooks = $bdd->query("SELECT * FROM emprunt");
                        }
                    }else{
                        if(isset($_POST['apply'])){
                            if(isset($_POST['date']) AND !empty($_POST['date'])){
                                $getBooks = $bdd->prepare("SELECT * FROM emprunt WHERE date_debut = ? OR date_fin = ?");
                                $getBooks->execute(array(htmlspecialchars($_POST['date']), htmlspecialchars($_POST['date'])));
                            }else{
                                $getBooks = $bdd->query("SELECT * FROM emprunt");
                            }
                        }else{
                            $getBooks = $bdd->query("SELECT * FROM emprunt");
                        }
                    }

                    if($getBooks->rowCount() > 0){
                        while($book = $getBooks->fetch()){ ?> 
                    <tr class="save">
                        <td class="book_title">
                            <a href="book.php?isbn=<?php echo $book["isbn_livres"]; ?>" style="text-decoration: none; color: #b80000">
                                <?php
                                    $getnom = $bdd->prepare("SELECT titre_livres FROM livres WHERE ISBN_livres = ?");
                                    $getnom->execute(array(htmlentities($book["isbn_livres"])));
                                    $getBookTitle = $getnom->fetch();

                                    echo $getBookTitle['titre_livres'];
                                ?>
                            </a>
                        </td>
                        <td class="auth">
                           <span><?php echo $book["nom_etudiant"]; ?></span>
                        </td>
                        <td>
                            <?php 
                                echo $book["numero_etudiant"];
                            ?>
                        </td>
                        <td>
                            <?php echo $book["date_debut"]; ?>
                        </td>
                        <td class="num">
                            <?php 
                                if($book['isok'] == 'true'){
                                    $dateFin = new DateTime($book["date_fin"]);
                                    $todayDate = new DateTime(date("Y/m/d"));
                                    $interval = $dateFin->diff($todayDate);
                                    
                                    if($dateFin == $todayDate){
                                        echo "<span style='color: rgba(150,0,0)'><i class='fa fa-triangle-exclamation'></i> L'emprunt prend fin aujourd'hui</span> <br>- <i style='font-weight: 100; font-size: 0.8em'>".$interval->days." jour(s) restant</i>";
                                    }elseif($dateFin < $todayDate){
                                        $getUserCaution = $bdd->prepare("SELECT * FROM caution WHERE mat_etu = ?");
                                        $getUserCaution->execute(array($book['matricule_etudiant']));
                            
                                        if($getUserCaution->rowCount() == 0){
                                            $AddCaution = $bdd->prepare("INSERT INTO caution(mat_etu, nom_etu, caution, date_dernier_ajout) VALUES(?, ?, ?, CURDATE())");
                                            $AddCaution->execute(array($book['matricule_etudiant'], $book["nom_etudiant"], 500*$interval->days));
                                        }else{
                                            $updateCaution = $bdd->prepare("UPDATE caution SET caution =  caution* ".$interval->days.", date_dernier_ajout = CURDATE() WHERE mat_etu = ? AND date_dernier_ajout != CURDATE()");
                                            $updateCaution->execute(array($book['matricule_etudiant']));
                                        }
                                        echo "<s tyle='color: rgba(100,0,0)><i class='fa fa-info-circle'></i> Emprunt terminer depuis le ".$book["date_fin"]."</s><br>";
                                    }else{
                                        if($interval->days == 1){
                                            echo "<span style='color: rgba(100,0,0)'>Il manque 1 jours</span><br>- <i style='font-weight: 100; font-size: 0.8em'>".$interval->days." jour(s) restant</i>";
                                        }else{
                                            echo "<span>Se termine le ".$book["date_fin"]."</span><br>- <i style='font-weight: 100; font-size: 0.8em'>".$interval->days." jour(s) restant</i>"; 
                                        }
                                    }
                                }else{
                                    echo "<span>".$book["date_fin"]."</span>"; 
                                    
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if($book['isok'] == 'false'){ ?>
                                    <span class="badge" style="background-color: #b80000">En attente...</span>
                            <?php
                                }else{ ?>
                                    <span class="badge" style="background-color: rgba(0,150,0)">Emprunter <i class="fa fa-check-circle" style="margin-left: 10px"></i></span>
                            <?php
                                }
                            ?>
                        </td>
                        <td class="container__table--button-container">
                            <?php
                                if($book['isok'] == 'false'){ ?>
                                    <a href="gereremprunt.php?msg_type=<?php echo sha1($book["id"]); ?>&stu_mat=<?php echo $book["matricule_etudiant"]; ?>"  class="accept" id="<?php echo $book["id"]; ?>"><i class="fa fa-check-circle"></i></a>
                                    <a href="gereremprunt.php?action=delete&id=<?php echo sha1($book["id"]); ?>&isbn=<?php echo sha1($book["isbn_livres"]); ?>&nom=<?php echo $book["nom_etudiant"]; ?>&phone=<?php echo $book["numero_etudiant"]; ?>&debut=<?php echo $book["date_debut"]; ?>&fin=<?php echo $book["date_fin"]; ?>"  class="denied" id="<?php echo $book["id"]; ?>"><i class="fa fa-times-circle"></i></a>
                            <?php
                                }else{ ?>
                                    <a href="book.php?isbn=<?php echo $book["isbn_livres"]; ?>" ><i class="fa fa-eye"></i></a>
                                    <?php
                                        $endDate = new DateTime($book["date_fin"]);
                                        $today = new DateTime(date("Y/m/d"));

                                        if($today == $endDate){ ?>
                                            <a href="gereremprunt.php?id=<?php echo sha1($book["id"]); ?>&isbn=<?php echo sha1($book["isbn_livres"]); ?>&nom=<?php echo $book["nom_etudiant"]; ?>&phone=<?php echo $book["numero_etudiant"]; ?>&debut=<?php echo $book["date_debut"]; ?>&fin=<?php echo $book["date_fin"]; ?>" style="background-color: rgb(0,90,0)" title="Confirmer le retour"><i class="fa fa-share"></i></a>
                                    <?php
                                        }elseif($today > $endDate){ ?>
                                            <a href="gereremprunt.php?id=<?php echo sha1($book["id"]); ?>&isbn=<?php echo sha1($book["isbn_livres"]); ?>&nom=<?php echo $book["nom_etudiant"]; ?>&phone=<?php echo $book["numero_etudiant"]; ?>&debut=<?php echo $book["date_debut"]; ?>&fin=<?php echo $book["date_fin"]; ?>&caution=<?php echo $book["id"]; ?>" style="background-color: rgb(0,90,0)" title="Confirmer le retour avec caution"><i class="fa fa-share"></i></a>
                                    <?php
                                        }
                                    ?>
                            <?php
                                }
                            ?>
                        </td>
                    </tr>
                <?php
                        }
                    }else{ ?>
                <tr>
                    <td colspan="7" class="nothing">
                        <img src="assets/img/undraw_No_data_re_kwbl.png" width="250" alt="">
                        <span style="color: #b60000; margin-top: 15px">aucune nouvelle emprunt .</span>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
    <?php
        if(isset($_GET['msg_type']) AND !empty($_GET['msg_type'])){ ?>
            <form method="post" class="confirmBorrow">
                <div class="c1">
                    <p>Une fois que vous avez accepter l'emprunt, vous ne pouvez plus revenir dessus.</p>
                    <div class="sc1">
                        <input type="submit" value="confirmer" name="confirmEmprunt">
                        <a href="gereremprunt.php">annuler</a>
                    </div>
                </div>
            </form>
        <?php
        }
        if(isset($_GET['id']) AND !empty($_GET['id'])){
            $msg = "";
            if(isset($_GET['caution']) AND !empty($_GET['caution'])){ 
                $getCaution = $bdd->prepare("SELECT caution FROM caution WHERE mat_etu IN (SELECT matricule_etudiant FROM emprunt  WHERE id = ?)");
                $getCaution->execute(array($_GET['caution']));

                if($getCaution->rowCount() > 0){
                    $getCautionAmount = $getCaution->fetch();
                    $msg = "Ce livre a ete rendu avec un retard, assurer vous de pecevoir un montant de ".$getCautionAmount['caution']." FCFA avant de remettre la carte d'identite et de confirmer le retour du livre.";
                }
            }else{
                $msg = "Etes-vous vraiment sur que le livre a ete rendu ?";
            }
            ?>
                <form method="POST" class="confirmBorrow">
                    <div class="c1" style="height: auto">
                        <p style="background-color: transparent; display: flex;align-items: center"><?= $msg ?></p>
                        <div class="sc1">
                            <input type="submit" value="confirmer le retour" name="confirmReturn">
                            <a href="gereremprunt.php">annuler</a>
                        </div>
                    </div>
                </form>
        <?php
        }
        if(isset($_GET['action'], $_GET['id']) AND !empty($_GET['action']) AND !empty($_GET['id'])){ ?>
            <form method="POST" class="confirmBorrow">
                <div class="c1">
                    <p style="background-color: transparent; display: flex;align-items: center">Etes-vous vraiment sur de vouloir refuser ?</p>
                    <div class="sc1">
                        <input type="submit" value="supprimer" name="confirmSupp">
                        <a href="gereremprunt.php">annuler</a>
                    </div>
                </div>
            </form>
        <?php
        }        
    ?>
    <script src="js/emprunt.js"></script>
</body>
</html>