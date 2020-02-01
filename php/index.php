<?php
/**
 * Created by PhpStorm.
 * User: ASHBAR
 * Date: 12.11.2019
 * Time: 13:18
 */

include "create_database.php";
include "header.php";

error_reporting(E_ERROR | E_PARSE);

echo '<br><h2 style="font-size: 135%">&nbsp;FOTOĞRAFLAR YÜKLEYİN VE GÖRÜNTÜLEYİN</h2>';

session_start();
if(isset($_SESSION["username"]) && isset($_SESSION["password"]) && $_SESSION["id"]){
    class files extends VeriTabani {
        protected $conn;
        protected $hatalar;
        protected $yol;
        protected $grup_dizi;
        protected $resim_aciklama;
        function _construct(){
            parent::_construct();
            $this->conn = parent::getConn();
            $this->hatalar= array();
            $this->resim_aciklama=array();
        }
        function getConn()
        {
            return $this->conn;
        }

        function getErrs()
        {
            return $this->hatalar;
        }

        function yukle(){
            $dosya_adi = $_FILES['resim']['name'];
            $dosya_boyutu =$_FILES['resim']['size'];
            $file_tmp = $_FILES['resim']['tmp_name'];
            $dosya_uzantisi = strtolower(@end(explode('.',$_FILES['resim']['name'])));
            $path="../images/";
            $aciklama = $_POST["aciklama"];
            $kategori=$_POST["kategori"];
            $kullanici=$_SESSION['username'];

            $this->resim_aciklama[$kategori] = $aciklama;

            $extensions= array("jpeg","jpg","png");

            if(in_array($dosya_uzantisi,$extensions)=== false){
                $this->hatalar[]="Uzantıya izin verilmiyor. Lütfen JPEG veya PNG dosyalarını seçiniz.";
            }

            if($dosya_boyutu > 50097152){
                $this->hatalar[]='Dosya boyutu maximum 50 MB olmalı.';
            }

            if(isset($_SESSION['username'])){
                $sql = 'SELECT user_id FROM users WHERE username="'.$kullanici.'"';
                
                $sorgu = $this->conn->prepare($sql);
                $sorgu->execute();
                $kullanici_id = $sorgu->fetch(PDO::FETCH_COLUMN);
            }

            $filename="".date("m-d-Y h-i-s-s")."".rand(0,10000)."".$dosya_adi;

            if(empty($this->hatalar)==true){
                $klasor = $path.$_SESSION['id'];
                if(!file_exists($klasor)) {
                    mkdir($klasor);
                }
                    move_uploaded_file($file_tmp,$klasor."/"."!".$kategori."!".$filename);

                $tarih = date("m-d-Y h:i:s",filectime($klasor."/"."!".$kategori."!".$filename));
                $sql = "INSERT INTO files(path, aciklama, tarih, kategori, kullanici) VALUES(:path, :aciklama, :tarih, :kategori, :kullanici)";
                $query = $this->conn->prepare($sql);

                $this->yol=$klasor."/"."!".$kategori."!".$filename;
                if(!empty($_POST["aciklama"]) && !empty($_POST["kategori"]))
                {
                    $query->bindParam(':path',$this->yol,PDO::PARAM_STR);
                    $query->bindParam(':aciklama',$aciklama,PDO::PARAM_STR);
                    $query->bindParam(':tarih',$tarih,PDO::PARAM_STR);
                    $query->bindParam(':kategori',$kategori,PDO::PARAM_INT);
                    $query->bindParam(':kullanici',$kullanici_id, PDO::PARAM_INT);

                    $query->execute();
                }
                else{
                    $this->hatalar[]='Lütfen alanlardan hiçbirini boş bırakmayınız.';
                }
            }
        }
        public function sil(){
            $file_id = $_POST["file_id"];
            $sql = 'DELETE FROM files WHERE file_id="'.$file_id.'"';

            $this->getConn()->query($sql);

        }
        public function guncelle()
        {
            $file_id = $_POST["file_id"];
            $aciklama = $_POST["aciklama"];
            $sql = 'UPDATE files SET aciklama="'.$aciklama.'" WHERE file_id="'.$file_id.'"';
            $this->getConn()->query($sql);
        }
        function combobox(){
            try{
                $sql = "SELECT * FROM categories";
                $sorgu= $this->conn -> prepare($sql);
                $sorgu -> execute();
                $kayitlar = $sorgu -> fetchAll(PDO::FETCH_ASSOC);
                if($sorgu -> rowCount() > 0) {
                    foreach ($kayitlar as $kayit) {
                        echo'<option value="'.$kayit['kategori_id'].'">'.$kayit['kategori_ad'].'</option>';
                    }
                }
            }catch (Exception $e){
                $this->hatalar[]='Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
            }
        }
        function goruntule_tablo(){
            try{
                $sql = 'SELECT * FROM files LEFT JOIN categories ON 
                    categories.kategori_id=files.kategori 
                    LEFT JOIN users ON users.user_id=files.kullanici';
                $query = $this->getConn()->prepare($sql);
                $query -> execute();
                $kayitlar = $query -> fetchAll(PDO::FETCH_OBJ);
                if($query -> rowCount() > 0) {
                    foreach ($kayitlar as $kayit) {
                        if($kayit->kullanici == $_SESSION["id"]){
                            echo'
                            <tr onclick="getRoww()">
                                <td>'. $kayit -> file_id.'</td>
                                <td>'. $kayit -> path.'</td>
                                <td>'. $kayit -> aciklama.'</td>
                                <td>'. $kayit -> tarih.'</td>
                                <td>'. $kayit -> kategori_ad.'</td>
                                <td>'. $kayit -> username.'</td>
                            </tr>
                        ';
                        }
                    }
                }
                else{
                    echo'
                    <tr>
                        <td colspan="6">Henüz Dosya Bulunmamaktadır.</td>
                    </tr>
                ';
                }
            }catch (Exception $e){
                $this->hatalar[]='Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
            }
        }
        function butonlar(){
            echo'<form class="frm1" action="" method="post">';
            $kat_array=array();
            try{
                $sql = 'SELECT * FROM files LEFT JOIN categories ON 
                    categories.kategori_id=files.kategori 
                    LEFT JOIN users ON users.user_id=files.kullanici';
                $query = $this->getConn()->prepare($sql);
                $query -> execute();
                $kayitlar = $query -> fetchAll(PDO::FETCH_ASSOC);
                if($query -> rowCount() > 0) {
                    foreach ($kayitlar as $kayit) {
                        if($kayit["kullanici"] == $_SESSION["id"]){
                            $this->grup_dizi[]=$kayit["path"];
                            if(count($kat_array)==0){
                                $kat_array[]=$kayit["kategori_id"];
                                if(isset($kayit["kategori_id"])){
                                    echo'<button type="submit" class="buton2" name="'.$kayit["kategori_id"].'">'.$kayit["kategori_ad"].'
                                        </button>';
                                }
                            }
                            else{
                                if(! in_array($kayit["kategori_id"],$kat_array)){
                                    $kat_array[]=$kayit["kategori_id"];
                                    if(isset($kayit["kategori_id"])){
                                        echo'<button style="margin-left: 2%;" type="submit" class="buton2" name="'.$kayit["kategori_id"].'">'.$kayit["kategori_ad"].'
                                            </button>';
                                    }
                                }
                            }
                        }
                    }
                }
                else{
                    echo'Henüz Dosya Bulunmamaktadır.';
                }
            }catch (Exception $e){
                $this->hatalar[]='Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.';
            }
            echo'</form>';
        }

        function uzanti($file_name) {
            //oluşturulan path'ten kategori id kısmını ayır
            $kategori_id = explode("!", $file_name);
            return $kategori_id[1];
        }

        function goruntule_album(){
            echo '<div class="popupdiv" id="popupdiv">
                    <div id="myPopup">
                           <h2 id="aciklama"></h2>
                           <h2 id="tarih"></h2>
                           <h2 id="kullanici"></h2>
                    </div>
                </div>';


            if(!empty($this->grup_dizi)){
                //resimleri görüntüle
                $dizin = "../images/".$_SESSION["id"]."/";
                $satirLimit = 3;
                $satir = 0;
                $dir = opendir($dizin);
                foreach ($this->grup_dizi as $grupla){
                    $kategori_id = explode("!", $grupla);
                    if(isset($_POST[$kategori_id[1]])){
                        while (($dosya = readdir($dir)) !== false){
                            if ($dosya != "." && $dosya != "..") {
                                if(! is_dir($dizin.$dosya) && $this->uzanti($dosya) == $kategori_id[1]){
                                    $satir++;
                                    echo '<a href="'.$dizin.$dosya.'">';
                                    $src=$dizin.$dosya;

                                    $sql = 'SELECT * FROM files where path="'.$src.'"';
                                    $query = $this->getConn()->prepare($sql);
                                    $query -> execute();
                                    $kayitlar = $query -> fetchAll(PDO::FETCH_ASSOC);
                                    if($query -> rowCount() > 0) {
                                        foreach ($kayitlar as $kayit) {
                                            echo '<img onmouseover="getImage(\'' . $src . '\',\'' . $kayit["aciklama"] . '\', \''.$kayit["tarih"].'\' , \''.$_SESSION["username"].'\')" onmouseleave="delImage()" src="' . $dizin . $dosya . '" width="10%" height="20%" border="0" />';
                                            echo "</a>";
                                        }
                                    }
                                    if ($satir==$satirLimit){
                                        echo "<br />\n";
                                        $satir=0;
                                    }else{
                                        echo "\n";
                                    }
                                }
                            }
                        }
                        closedir($dir);
                    }
                }
            }
        }
    }

    $obj = new files();
    $obj->_construct();

    if(isset($_FILES["resim"]) && isset($_POST["yukle"])){
        $obj->yukle();
    }
    if(isset($_POST["sil"])){
        $obj->sil();
    }
    if(isset($_POST["guncelle"])){
        $obj->guncelle();
    }

    ?>

    <form class="frm1" action="" method="post" enctype="multipart/form-data">
        <label>Yüklemek için resim seçiniz:</label>
        <input type="file" name="resim">
        <br>
        <br>
        <input type="text" name="aciklama" placeholder="Açıklama giriniz">
        <p class="hata"><?php if(isset($_POST["yukle"])) if(empty($_POST["aciklama"])) echo'Lütfen resme ait açıklamayı giriniz!'; ?></p>

        <label> Kategoriler : </label>
        <select name="kategori">
            <?php
            $obj->combobox();
            ?>
        </select>
        <br>
        <br>
        <input type="hidden" id="hidden" name="file_id">
        <button type="submit" name="yukle" style="float: left">Yükle</button>
        <button type="submit" name="sil" style="float: left; margin-left: 2%">Sil</button>
        <button type="submit" name="guncelle" style="margin-left: 2%">Güncelle</button>
        <br><br>
        <button type="submit" name="tbl-sbm">Tablo Halinde Göster</button>
    </form>

    <br>
<?php
    if(empty($obj->getErrs())==false){
        foreach ($obj->getErrs() as $hata){
            echo'<p class="hata">'.$hata.'</p>';
        }
    }
}
else{
    echo '<div><br>
        <h2 style="float: left; margin-left: -1%;margin-top: 3px">&nbsp;&nbsp;&nbsp;Burayı görüntülemek için önce</h2>
        <a style="font-size: 24px; margin-left: -19%;" href="login.php">giriş yapmalısınız!</a></div>';
}

if(isset($_POST["tbl-sbm"])){
    echo'
    <br>
    <br>

    <table id="tablo" style="width: 80%; margin-left: 10%;">
        <thead>
        <tr>
            <th>FILE ID </th>
            <th>PATH</th>
            <th>AÇIKLAMA</th>
            <th>TARİH</th>
            <th>KATEGORİ</th>
            <th>KULLANICI</th>
        </tr>
        </thead>
        <tbody>
        ';
    if(isset($_SESSION["id"]))
        $obj->goruntule_tablo();

    echo '</tbody> </table>';
}

if(isset($_SESSION["id"])){
    echo '<div class="butt2">';
    $obj->butonlar();
    echo '</div>';
}


echo'<p></p><br><br>';

if(isset($_SESSION["id"])){
    echo '<div style=" width:100%; height:100%;margin-left: 5%">';
    $obj->goruntule_album();
    echo '</div>';
}



echo'<br><br><br><br><br><br>';
include "footer.php";