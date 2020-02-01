<?php
/**
 * Created by PhpStorm.
 * User: ASHBAR
 * Date: 3.12.2019
 * Time: 11:51
 */

class VeriTabani{
    protected $conn;
    protected $servername = "localhost";
    protected $username = "root";
    protected $password = "";
    protected $dbname = "uygulama3";

    function _construct(){
        try {
            $db = new PDO("mysql:host=$this->servername;charset=utf8", $this->username, $this->password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $database = "CREATE DATABASE IF NOT EXISTS ".$this->dbname.";ALTER DATABASE ".$this->dbname." CHARACTER SET utf8 COLLATE utf8_turkish_ci;";
            $db->exec($database);

            $this->conn=new PDO("mysql:host=$this->servername;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->create_table_categories();
            $this->create_table_users();
            $this->create_table_files();

            return "<br>basarili<br>";
        }catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    function create_table_categories(){
        try{
            $kategori = "
            use uygulama3;
            CREATE TABLE IF NOT EXISTS categories(
                kategori_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                kategori_ad varchar(100) NOT NULL
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;";

            $this->conn->exec($kategori);
            return "categories olusturuldu";
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }

    function create_table_users(){
        try{
            $kullanici=" 
            use uygulama3;
            CREATE TABLE IF NOT EXISTS users (
                user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(200) NOT NULL, 
                password VARCHAR(200) NOT NULL
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci";

            $this->conn->exec($kullanici);

            return "users olusturuldu";
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    }

    function create_table_files(){
        try{
            $dosya=" 
            use uygulama3;
            CREATE TABLE IF NOT EXISTS files (
                file_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                path VARCHAR(200) NOT NULL, 
                aciklama VARCHAR(100) NOT NULL,
                tarih VARCHAR(30) NOT NULL,
                kategori INT(6),
                kullanici INT(6)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;;
            ALTER TABLE files FOREIGN KEY (kategori) REFERENCES categories(kategori_id) ON DELETE CASCADE ON UPDATE CASCADE;
            ALTER TABLE files FOREIGN KEY (kullanici) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE";

            $this->conn->exec($dosya);

            return "files olusturuldu";
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
    }

    function tr_strtoupper($text)
    {
        $search=array("ç","i","ı","ğ","ö","ş","ü");
        $replace=array("Ç","İ","I","Ğ","Ö","Ş","Ü");
        $text=str_replace($search,$replace,$text);
        $text=strtoupper($text);
        return $text;
    }

    public function getConn(){
        return $this->conn;
    }
}