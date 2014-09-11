<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 05/09/14
 * Time: 15:51
 */
namespace ZE\Bandaid\Service;

class UserService
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserByCredentials($username,$password,$columns=null)
    {
        if(!$columns){
            $columns = array('id', 'email','username');
        }
        $sql = 'SELECT '.implode(",",$columns).' FROM users WHERE email = :username AND password = SHA2(CONCAT(:password,salt), 256)';
        $stmt  = $this->pdo->prepare($sql);
        $stmt->execute(array(':username' => $username,':password'=>$password));
        return $stmt->fetch();
    }

    public function createUser($username,$password, $email)
    {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $sql = "INSERT INTO users(username,email,salt,password) VALUES (:username,:email,:salt, SHA2(CONCAT(:password,:salt), 256) )";
        $stmt  = $this->pdo->prepare($sql);
        try {
            $stmt->execute(array(':username' => $username, ':email' => $email, ':salt' => $salt, ':password' => $password));
            return array('success' => true, 'id' => $this->pdo->lastInsertId());
        }catch (\PDOException $e){
            return array('success' => false, 'message' => 'Duplicate email or username');
        }
        catch (\Exception $e){
            return array('success' => false, 'message' => 'Unkown error');
        }
    }
}