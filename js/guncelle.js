function getRow(){
    var table = document.getElementById('tablo');
    var cells = table.getElementsByTagName('td');

    for (var i = 0; i < cells.length; i++) {
        var kategori_ad=document.getElementById("kategori_ad");
        var kategori_id=document.getElementById("kategori_id");

        var cell = cells[i];
        cell.onclick = function () {
            var rowId = this.parentNode.rowIndex;

            var secilmeyenSatirlar = table.getElementsByTagName('tr');
            for (var i = 0; i < secilmeyenSatirlar.length; i++) {
                secilmeyenSatirlar[i].style.backgroundColor = "";
            }

            var secilenSatir = table.getElementsByTagName('tr')[rowId];
            secilenSatir.style.backgroundColor = "yellow";

            kategori_id.value = secilenSatir.cells[0].innerHTML;
            kategori_ad.value = secilenSatir.cells[1].innerHTML;
        }
    }
}

function getRoww(){
    var table = document.getElementById('tablo');
    var cells = table.getElementsByTagName('td');

    for (var i = 0; i < cells.length; i++) {
        var file_id=document.getElementById("hidden");

        var cell = cells[i];
        cell.onclick = function () {
            var rowId = this.parentNode.rowIndex;

            var secilmeyenSatirlar = table.getElementsByTagName('tr');
            for (var i = 0; i < secilmeyenSatirlar.length; i++) {
                secilmeyenSatirlar[i].style.backgroundColor = "";
            }

            var secilenSatir = table.getElementsByTagName('tr')[rowId];
            secilenSatir.style.backgroundColor = "yellow";

            file_id.value = secilenSatir.cells[0].innerHTML;
        }
    }
}
