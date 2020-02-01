<?php
/**
 * Created by PhpStorm.
 * User: ASHBAR
 * Date: 6.12.2019
 * Time: 16:11
 */

include 'create_database.php';

class users extends VeriTabani{
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
         try{
            $username = $_POST["username"];
            //başka kullanıcı var mı?
            $varmi = 'SELECT * FROM users WHERE username="'.$username.'"';
            $qry = $this->conn->prepare($varmi);
            $qry->execute();
            if($qry -> rowCount() == 0){
                $sql = "INSERT INTO users(username, password) VALUES(:username, :password)";

                $query = $this->conn -> prepare($sql);

                $password = base64_encode($_POST["password"]);

                if(!(empty($_POST["username"]) || empty($_POST["password"])))
                {
                    $query->bindParam(':username',$username,PDO::PARAM_STR);
                    $query->bindParam(':password',$password,PDO::PARAM_STR);
                    $query -> execute();

                    $sql2 = 'SELECT user_id FROM users WHERE username="'.$username.'"';
                    $sorgu = $this->conn->prepare($sql2);
                    $sorgu->execute();
                    $kullanici_id = $sorgu->fetch(PDO::FETCH_COLUMN);

                    $_SESSION['id'] =$kullanici_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    
                    header("Location: categories.php");
                }
                else{
                    echo'<p class="hata">Kullanıcı adınızı ve parolanızı giriniz!</p>';
                }
            }
            else{
                echo'<p class="hata">Aynı kullanıcı adına ait bir hesap daha bulunmaktadır!</p>';
            }
        }
        catch (PDOException $e){
            echo'HATA: '.$e;
        }
    }

    function giris(){
        try{
            $username = $_POST["username"];
            $parola = base64_encode($_POST["password"]);

            $sql = 'SELECT * FROM users WHERE username="'.$username.'" AND password="'.$parola.'"';
            $sorgu = $this->conn->prepare($sql);
            $sorgu->execute();
            $kayit=$sorgu->fetchAll(PDO::FETCH_ASSOC);
            if($sorgu -> rowCount() > 0){
                foreach ($kayit as $k) {
                    $_SESSION['id'] = $k['user_id'];
                    $_SESSION['username'] = $k['username'];
                    $_SESSION['password'] = $k['password'];
                }
            }
            else{
                echo'<p class="hata">Lütfen kullanıcı adınızı ya da parolanızı kontrol ediniz!</p>';
            }
            if(isset($_SESSION["id"])){
                header("Location: index.php" );
            }
        }catch (Exception $e){
            echo'Böyle bir kullanıcı bulunmamaktadır: '.$e;
        }
    }
}
$obj = new users();
$obj->_construct();

include "header.php";

session_start();

if(isset($_SESSION["username"]) && isset($_SESSION["password"]) && $_SESSION["id"]){
    echo '<br><h3>&nbsp;&nbsp;Hoş Geldiniz:  '.$_SESSION["username"].'</h3>';
    echo '<h3>&nbsp;&nbsp;Ama zaten giriş yapmışsınız.</h3>';
}
else{
    echo'
        <div class="form">
        <form action="" class="frm1" method="post">
            <input type="text" name="username" placeholder="Kullanıcı Adı" value="" >
            <p class="hata"></p>
            <input type="password" name="password" placeholder="Parola" value="" >
            <p class="hata"></p>
            <button type="submit" name="giris" style="float: left">Giriş</button>
            <p style="float: left; margin-left: 2%">Hesabınız yok mu? Oluşturun</p>
            <button type="submit" name="kayit" style="margin-left: -35%">Ekle</button>
        </form>
        
        <br>
        </div>
    ';
}
if(isset($_POST["giris"])){
    $obj->giris();
}

if(isset($_POST["kayit"])){
    $obj->ekle();
}

echo'<br><br><br><br><br><br>';
include "footer.php";
