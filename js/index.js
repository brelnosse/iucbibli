const fullname = document.getElementById("fullname");
const matricule = document.getElementById("matricule");

const connexionBtn = document.querySelector(".form__footer--button");
const errmsg = document.querySelector(".connexionErrorMsg");

const showErrmsg = (msg, color, time) =>{
    errmsg.innerHTML = msg;
    errmsg.style.backgroundColor = color;
    errmsg.classList.remove('hidden')
    setTimeout(()=>{
        hideErrmsg();
    }, time);
}
const hideErrmsg = () =>{
    errmsg.classList.add('hidden')
}


connexionBtn.addEventListener("click", (e)=>{
    fullname.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
    matricule.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";

    if(fullname.value.trim() == "" || fullname.value.trim().length < 4){
        fullname.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Le nom entre incorrect !", "#6d0000", 7000);        
    }else if(matricule.value.trim() == "" || !(/^[a-zA-Z0-9]{13}$/.test(matricule.value.trim()))){
        matricule.style.boxShadow = "0px 0px 8px 2px rgba(180,0,0)";
        showErrmsg("<i class='fa fa-exclamation-triangle'></i> Votre matricule est incorrect !", "#6d0000", 7000);
    }else{
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "ctraitement.php?fullname="+fullname.value.trim()+"&matricule="+matricule.value.trim());
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == "ok"){
                    fullname.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    matricule.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";

                    window.location = "home.php?cote=all";
                }else if(xhr.responseText == "nex"){
                    showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> Mot de passe ou identifiant incorrect ! <a href='inscription.php' style='text-decoration: none; font-weight: bold; color: #b80000'>S'inscrire ?</a>", "rgba(255, 217, 0, 0.933)", 10000);
                }else{
                    isbn.style.boxShadow = "0px 0px 16px 2px rgba(100,100,100,0.1)";
                    showErrmsg("<i class='fa fa-exclamation-triangle' style='margin-right: 10px'></i> Une erreur est survenue !", "#6d0000", 7000);
                }
            }
        }
        xhr.send(null);
    }
})