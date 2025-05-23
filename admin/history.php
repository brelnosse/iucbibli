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
    if(isset($_GET['action'], $_GET['id']) AND !empty($_GET['action']) AND !empty($_GET['id'])){

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
    <link rel="stylesheet" href="css/history.css"/>
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
            <a href="gereremprunt.php"><i class="fa fa-cloud-download"></i> <span class="label">Gerer les emprunts</span></a>
            <a href="borrowedBooks.php"><i class="fa fa-bullseye"></i> <span class="label">Afficher les livres emprunter</span></a>
            <a href="history.php" class="active"><i class="fa fa-history"></i> <span class="label">Historique des emprunts</span></a>
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
        <div class="container__option"></div>
        <div class="container__body">
            <table class="container__table">
                <tr class="container__body--titles-container">
                    <th class="container__body--title">Titre</th>
                    <th class="container__body--title">nom etudiant</th>
                    <th class="container__body--title">Numero de telephone</th>
                    <th class="container__body--title">Date de debut</th>
                    <th class="container__body--title">Statut</th>
                    <th class="container__body--title">Date de fin</th>
                    <th class="container__body--title">date de retour</th>
                    <th class="container__body--title">Decision</th>
                </tr>
                <?php 
                    $count = 0;
                    $getBooks = $bdd->query("SELECT * FROM historique");

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
                            <span class="green"><i class="fa fa-check-circle"></i> Terminer</span>
                        </td>
                        <td>
                            <?php echo $book["date_debut"]; ?>
                        </td>
                        <td>
                            <?php echo $book["repliedDate"]; ?>
                        </td>
                        <td>
                            <?php
                                if($book["accorder"] == "Accorder"){
                                    echo "<b style='color: green'><i class='fa fa-check-double'></i> ".$book["accorder"]."</b>";
                                }else{
                                    echo "<b style='color: rgba(150,0,0)'><i class='fa fa-times'></i> ".$book["accorder"]."</b>";
                                }
                            ?>
                        </td>                        
                    </tr>
                <?php
                        }
                    }else{ ?>
                <tr>
                    <td colspan="7" class="nothing" style="">
                        <img src="assets/img/undraw_No_data_re_kwbl.png" width="250" alt="">
                        <span style="color: #b60000; margin-top: 15px; font-size: 1.5em">Pas d'historique</span>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
  
</body>
</html>