<?php

/**
 * Created by PhpStorm.
 * User: Omistaja
 * Date: 26-Jul-16
 * Time: 20:19
 */
require_once('db.php');

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $db_connect = $database->connection();
        $this->conn = $db_connect;
    }

    public function query($sql)
    {
        $statement = $this->conn->prepare($sql);
        return $statement;
    }

    public function lastId() {
        $stmt = $this->conn->lastInsertId();
        return $stmt;
    }

    public function signup($username, $password, $email, $code)
    {
        try {
            $pwd = md5($password);
            $statement = $this->conn->prepare("INSERT INTO users(username, password, e-mail, token)
                                   VALUES(:username, :pwd, :email, :code )");
            $statement->bindParam(":username", $username);
            $statement->bindParam(":pwd", $pwd);
            $statement->bindParam(":email", $email);
            $statement->bindParam(":code", $code);
            $statement->execute();
            return $statement;
        }
        catch(PDOException $err) {
            echo $err->getMessage();
        }
    }
    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username=:username");
            $stmt->execute(array(":username"=>$username));
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() == 1) {
                if ($userData["userStatus"] == "active") {
                    if ($userData['password'] == md5($password)) {
                        $_SESSION['userSession'] == $userData['userId'];
                        return true;
                    }
                    else {
                        header("Location: index.php?error");
                        exit;
                    }
                }
                else {
                    header("Location: index.php?inactive");
                    exit;
                }
            }
            else {
                header("Location: index.php?error");
                exit;
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function is_logged_in() {
        if (isset($_SESSION['userSession'])) {
            return true;
        }
    }

    public function logout() {
        session_destroy();
        $_SESSION['userSession'] = false;
    }
    public function redirect($url) {
        header("Location: " . $url);
    }

    function send_mail() {
        require_once (phpmailer.php);
        $mail = new
    }
}

?>