const fullname = document.getElementById("fullname");
const matricule = document.getElementById("matricule");
const ecole = document.getElementById("ecole");
const niveau = document.getElementById("niveau");
const email = document.querySelector("#email");
const phone = document.querySelector("#phone");
const inscriptionBtn = document.querySelector(".form__footer--button");
const errmsg = document.querySelector(".connexionErrorMsg");

const showErrmsg = (msg, color, time) =>{
    errmsg.innerHTML = msg;
    errmsg.style.bottom = "5%";
    errmsg.style.backgroundColor = color;
    setTimeout(()=>{
        hideErrmsg();
    }, time);
}
const hideErrmsg = () =>{
    errmsg.style.bottom = "-400px";
}

inscriptionBtn.addEventListener("click", (e)=>{
    fullname.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
    matricule.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
    ecole.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0)";
    niveau.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
    email.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
    phone.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";

    if(fullname.value.trim() == "" || fullname.value.trim().length < 4){
        fullname.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Le nom entre incorrect !", "#6d0000", 7000);        
    }else if(matricule.value.trim() == "" || !(/^[a-zA-Z0-9]{13}$/.test(matricule.value.trim()))){
        matricule.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Votre matricule est incorrect !", "#6d0000", 7000);
    }else if(ecole.value.trim() == "" || ecole.value.trim() == "none"){
        ecole.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> L'ecole selectionnee est invalide !", "#6d0000", 7000);
    }else if(niveau.value.trim() == "" || niveau.value.trim() == "none"){
        niveau.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Le niveau selectionnee est invalide !", "#6d0000", 7000);
    }else if(email.value.trim() != "" && !(/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(email.value.trim()))){
        email.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> E-mail incorrect, vous pouvez laisser ce champ vide..", "#6d0000", 7000);
    }else if(phone.value.trim() == "" || !(/^6\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d\s*\d$/).test(phone.value.trim())){
        phone.style.boxShadow = "0px 0px 16px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Le num&eacute;ro de t&eacute;l&eacute;phone est incorrect !", "#6d0000", 7000);
    }else{
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "itraitement.php?fullname="+fullname.value.trim()+"&matricule="+matricule.value.trim()+"&ecole="+ecole.value.trim()+"&niveau="+niveau.value.trim()+"&email="+email.value.trim()+"&phone="+phone.value.trim().split(" ").join(""));
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == "ok"){
                    fullname.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    matricule.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    ecole.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0)";
                    niveau.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    email.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    phone.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";

                    window.location = "index.php?ref="+fullname.value.trim();
                }else if(xhr.responseText == "ex"){
                    matricule.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
                    showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> Un etudiant avec le meme matricule existe deja ! <a href='index.php'>Se connecter</a>", "rgba(255, 217, 0, 0.933)", 10000);
                }else{
                    isbn.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> Une erreur est survenue !", "#6d0000", 7000);
                }
            }
        }
        xhr.send(null);
    }
})