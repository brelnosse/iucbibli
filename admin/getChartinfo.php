<?php
    include("config.php");
    $getBooksInfo = $bdd->query("SELECT titre_livres, cote_livres FROM livres WHERE cote_livres > 5 ORDER BY cote_livres DESC");

    $dt = "";
    while($data = $getBooksInfo->fetch()){
        $dt .= $data['titre_livres']."(.*)".$data['cote_livres']."(*.)";
    }
    echo $dt;