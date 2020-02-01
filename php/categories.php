<?php
/**
 * Created by PhpStorm.
 * User: ASHBAR
 * Date: 3.12.2019
 * Time: 12:03
 */
include "create_database.php";

include "header.php";

class categories extends VeriTabani{
    protected $conn;
    function _construct(){
        parent::_construct();
        $this->conn = parent::getConn();
    }
    function getConn()
    {
        return $this->conn;
    }

    public function ekle(){
        $sql = "INSERT INTO categories(kategori_id, kategori_ad) VALUES(NULL, :kategori_ad)";

        $kategori_ad = parent::tr_strtoupper($_POST["kategori_ad"]);

        $query = $this->getConn()->prepare($sql);

        if(!empty($_POST["kategori_ad"]))
        {
            $sayac=0;
            $sql2 = "SELECT * FROM categories";
            $query2 = $this->getConn()->prepare($sql2);
            $query2 -> execute();
            $kayitlar = $query2 -> fetchAll(PDO::FETCH_OBJ);
            if($query2 -> rowCount() > 0) {
                foreach ($kayitlar as $kayit) {
                    if($kayit->kategori_ad == $kategori_ad){
                        $sayac++;
                    }
                }
            }
            if($sayac>0){
                echo'<p class="hata">Kategori Adı Listede Bulunmaktadır. Lütfen Başka Bir Kategori Giriniz.</p>';
            }
            else{
                $query->bindParam(':kategori_ad',$kategori_ad,PDO::PARAM_STR);
                $query->execute();
            }
        }
    }

    public function sil(){
        $kategori_id = $_POST["kategori_id"];
        $sql = "DELETE FROM categories WHERE kategori_id=".$kategori_id;
        $this->getConn()->query($sql);
    }
    public function guncelle(){
        $kategori_id = $_POST["kategori_id"];
        $kategori_ad = parent::tr_strtoupper($_POST["kategori_ad"]);
        $sql = "UPDATE categories SET kategori_ad='".$kategori_ad."'WHERE kategori_id=".$kategori_id;
        $this->getConn()->query($sql);
    }
    public function listele(){
        $sql = "SELECT * FROM categories";
        $query = $this->getConn()->prepare($sql);
        $query -> execute();
        $kayitlar = $query -> fetchAll(PDO::FETCH_OBJ);
        if($query -> rowCount() > 0) {
            foreach ($kayitlar as $kayit) {
                echo'
                <tr onclick="getRow()">
                    <td>'. $kayit -> kategori_id.'</td>
                    <td>'. $kayit -> kategori_ad.'</td>
                </tr>
            ';
            }
        }
        else{
            echo'
            <tr>
                <td colspan="2">Henüz Kategori Bulunmamaktadır.</td>
            </tr>
        ';
        }
    }
}

$obj = new categories();
$obj->_construct();

if(isset($_POST["ekle"])){
    $obj->ekle();
}

if(isset($_POST["sil"])){
    $obj->sil();
}

if(isset($_POST["guncelle"])){
    $obj->guncelle();
}
?>

    <div class="form">
        <form class="frm1" action="" method="post">
            <input type="text" id="kategori_ad" name="kategori_ad" placeholder="Kategori adını giriniz">
            <p class="hata"><?php if(isset($_POST["ekle"])) if(empty($_POST["kategori_ad"])) echo'Lütfen eklemek istediğiniz kategoriyi giriniz!'; ?></p>

            <button type="submit" name="ekle" style="float: left">Ekle</button>
            <button type="submit" name="sil" style="float: left; margin-left: 2%">Sil</button>
            <button type="submit" name="guncelle" style="margin-left: 2%">Güncelle</button>

            <input type="hidden" id="kategori_id" name="kategori_id">
        </form>

        <br>
        <br>

        <table id="tablo" style="width: 75%; margin-left: 13%;">
            <thead>
            <tr>
                <th>KATEGORİ ID </th>
                <th>KATEGORİ AD </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $obj->listele();
            ?>
            </tbody>
        </table>
    </div>
    <br><br><br><br><br><br>
<?php
include "footer.php";


//BUNU DİKKATE ALMAYIN LÜTFEEN :)
class categoriess extends VeriTabani{}