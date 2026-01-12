const sendFormButton = document.querySelector(".form__footer--button"),
      email = document.querySelector("input#email"),
      ucode = document.querySelector("input#ucode"),
      errmsg = document.querySelector(".connexionErrorMsg");
    
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

let emailIsOk = false;
email.focus();
email.addEventListener("input", (e)=>{
    let emailVal = e.target.value.trim();
    if(emailVal != ""){
        if(/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(emailVal)){
            e.target.style.boxShadow = "0px -0.3px 0.3px 2px rgba(1, 176, 1, 0.941)";
            e.target.style.backgroundColor = "white";
            emailIsOk = true;
         }else{
            e.target.style.boxShadow = "0px 0px 0.3px 3px #b80000bc";
            e.target.style.backgroundColor = "white";
            emailIsOk = false;
        }
    }else{
        e.target.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
        e.target.style.backgroundColor = "rgba(234,234,234)";
        emailIsOk = false;
    }
})
let ucodeIsOk = false;
ucode.addEventListener("input", (e)=>{
    let codeVal = e.target.value.trim();
    if(codeVal != ""){
        if(codeVal.length == 13){
            e.target.style.boxShadow = "0px -0.3px 0.3px 2px rgba(1, 176, 1, 0.941)";
            e.target.style.backgroundColor = "white";
            ucodeIsOk = true;
        }else{
            e.target.style.boxShadow = "0px 0px 0.3px 3px #b80000bc";
            e.target.style.backgroundColor = "white";
            ucodeIsOk = false;
        }
    }else{
        e.target.style.boxShadow = "0px 0px 8px 5px rgba(127, 127, 127, 0.051)";
        e.target.style.backgroundColor = "rgba(234,234,234)";
        ucodeIsOk = false;
    }
})
email.value = "";
ucode.value = "";

sendFormButton.addEventListener("click", (e)=>{

    if(ucodeIsOk && emailIsOk){
        const form = new FormData();
        form.append("email", email.value.trim());
        form.append("ucode", ucode.value.trim());

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "ctraitement.php");
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                if(xhr.responseText == 'found'){
                    window.location = "dashboard.php";
                }else{
                    showErrmsg("Email ou code incorrect !");
                }
            }
        }
        xhr.send(form);
    }else{
        if(ucodeIsOk == false && emailIsOk == false){
            showErrmsg("Les informations entrees ne sont pas valide !");
        }else{
            if(ucodeIsOk == false){
                showErrmsg("Le code entrer est invalide !");
            }else{
                showErrmsg("L'email entrer est invalide !");
            }
        }
    }
})