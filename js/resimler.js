var popup = document.getElementById("myPopup");
var img = document.createElement("img");
var descrp = document.getElementById("aciklama");
var date  =document.getElementById("tarih");
var session = document.getElementById("kullanici");

function getImage(resim, aciklama, tarih, kullanici){
    img.src = resim;
    img.className = "dyn_img";

    descrp.innerText="AÇIKLAMA: "+aciklama;
    descrp.className = "aciklama";

    date.innerText="TARİH: "+tarih;
    date.className = "tarih";

    session.innerText="KULLANICI: "+kullanici;
    session.className = "kullanici";

    popup.append(img);
    popup.append(descrp);
    popup.append(date);
    popup.append(session);


    popup.style.visibility='visible';
}

function delImage(){
    popup.style.visibility='hidden';
}