<?php
session_start();


class user{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;


    public function register($login, $password, $email, $firstname, $lastname){

        $db= mysqli_connect("localhost","root","","classes");
            $pwd_hash = password_hash($password, 
            PASSWORD_DEFAULT, ['cost' => 12]);
            $password_confirm= $password;
            
            $req_login="SELECT * FROM `utilisateurs` WHERE login ='$login'";
            $query_login=mysqli_query($db, $req_login);
            $compare_login= mysqli_fetch_all($query_login);
    
            if(count($compare_login) != 0){
                echo "Désolé ce login est déjà utilisé";

            }
            else{
               
                if($password === $password_confirm){
                   
                    $req_register= "INSERT INTO `utilisateurs`( `login`, `password`, `email`, `firstname`, `lastname`) VALUES ( '$login', '$pwd_hash', '$email', '$firstname', '$lastname') ";
                    mysqli_query($db, $req_register);
                    
                    return $user_array= array($login, $pwd_hash, $email, $firstname, $lastname);   
                    
                }
                else{
                    echo "Votre mot de passe est différent";
                }
               
            }


             
    }

    public function connect($login, $password){


        if(!empty($login) && !empty($password)){

            $db= mysqli_connect("localhost","root","","classes");

            $req_connect= "SELECT * FROM utilisateurs WHERE login = '$login'";
            $query_connect = mysqli_query($db,$req_connect);
            $data_users = mysqli_fetch_all($query_connect, MYSQLI_ASSOC);
            
            if(count($data_users) == 0)
            {
                echo "Login ou mot de passe incorrect";
                
            }
            elseif(password_verify($password, $data_users[0]['password']))
            {
                $this->id=$data_users[0]['id'];
                $this->login= $data_users[0]['login'];
                $this->email= $data_users[0]['email'];
                $this->firstname= $data_users[0]['firstname'];
                $this->lastname= $data_users[0]['lastname'];


                $req_id= "SELECT id FROM `utilisateurs` WHERE `login` = '$login'";
                $query_id = mysqli_query($db,$req_id);
                $id_users = mysqli_fetch_assoc($query_id);
                $_SESSION["login"]= $login;
                $_SESSION["id"]=$id_users['id'];
                $_SESSION['connected']=1;
                echo "Vous êtes connecté";
            }
            else
            {
                echo "Login ou mot de passe incorrect";
                
            }
        }
        else{
            echo "Veuillez remplir tous les champs";
        }

    }
    public function disconnect(){
        
        unset($_SESSION);
        session_destroy();
        return "Vous êtes déconnecté";
        
        
       
    }

    public function delete(){
        $db= mysqli_connect("localhost","root","","classes");
        $req_delete=" DELETE FROM `utilisateurs` WHERE id = '$this->id' ";
        mysqli_query($db, $req_delete);
        unset($_SESSION);
        session_destroy();
        echo "Utilisateur supprimé";

    }
    public function update($login, $password, $email, $firstname, $lastname){

        $db= mysqli_connect("localhost","root","","classes");


        $password_confirm= $password;
        $pwd_hash=password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        if(!empty($login) && !empty($password) && !empty($password_confirm) ){
            $req_login="SELECT * FROM `utilisateurs` WHERE login='$login'";
            $query_login= mysqli_query($db, $req_login);
            $compare_login=mysqli_fetch_all($query_login);
            
             if(count($compare_login) != 0){
                echo "Désolé ce login est déjà utilsé";
            }
            elseif(!empty($password) && !empty($password_confirm)){
                if($password === $password_confirm ){
                    $req_update2="UPDATE `utilisateurs` SET `login`= '$login',`password`= '$pwd_hash', email='$email', firstname='$firstname', lastname ='$lastname' WHERE id = '$this->id'" ;
                    mysqli_query($db, $req_update2);
                    var_dump($req_update2);
                    $_SESSION["login"]=$login;
                }
                else{
                    
                    echo "Mot de passe différent";
                }
            }


            
        }
        
        

    }
    public function isConnected(){
        
        if(isset($_SESSION['connected'])){
            return true;
        }
        else{
            return false;
            
        }

    }

    public function getAllInfos(){
        $db= mysqli_connect("localhost","root","","classes");
       
        $req_all_info= "SELECT * FROM utilisateurs WHERE login= '$this->login'";
        $query_info= mysqli_query($db, $req_all_info);
        $all_infos= mysqli_fetch_all($query_info, MYSQLI_ASSOC);
        return $all_infos;

    }

    public function getLogin(){
        return $this->login;
    }

    public function getEmail(){

        return $this->email;;
    }

    public function getFirstname(){
        return $this->firstname;
    }

    public function getLastname(){
        return $this->lastname;
    }

    public function refresh(){

        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req=" SELECT * FROM utilisateurs WHERE id = '$id_user' ";
        $query= mysqli_query($db, $req);
        $result=mysqli_fetch_all($query, MYSQLI_ASSOC);

        $this->login =$result[0]['login'];
        $this->email =$result[0]['email'] ;
        $this->firstname =$result[0]['firstname'] ;
        $this->lastname =$result[0]['lastname'] ;

        return $array =[ $this->login, $this->email, $this->firstname, $this->lastname];


    }
     

}



$user = new user;
//$user->register( "new_usersqli", "mdp", "contact@mail.com", "firstname", "lastname");
$user->connect("user2sqli", "mdp");
//$user->disconnect();
//$user->delete();
//$user->update("user2sqli", "mdp", "contact@mail.com", "firstname", "lastname");
//var_dump($user->getAllInfos());
//echo $user->getFirstname();
var_dump($user->refresh());



?>


