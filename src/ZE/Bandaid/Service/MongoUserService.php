<?php
/**
 * Created by PhpStorm.
 * User: zach
 * Date: 05/09/14
 * Time: 15:51
 */
namespace ZE\Bandaid\Service;

class MongoUserService implements UserServiceInterface
{
    
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserByCredentials($username,$password,$columns=null)
    {

        $query = array('username' => $username);
        $user = $this->db->users->findOne($query);
        if(!$user || !hash("sha256", $user['password'] . $user['salt']) == $password ){
            return null;
        } else {
            return $user;
        }



    }

    public function createUser($username,$password, $email)
    {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        $password = hash("sha256", $password . $salt);
        $user = array(
            'email' => $username,
            'username' => $username,
            'salt' => $salt,
            'password' => $password
        );
        try {
            $this->db->users->insert($user);
        } catch (\Exception $e){
            return array('success' => false, 'message' => 'Unkown error');
        }
    }
}