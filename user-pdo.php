<?php

session_start();

try{
    $db = new PDO('mysql:host=localhost; dbname=classes; charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo 'Échec lors de la connexion : ' . $e->getMessage();
}


class userpdo{

    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    public function register($login, $password, $email, $firstname, $lastname){

        global $db;

        $pwd_hash = password_hash($password, 
        PASSWORD_DEFAULT, ['cost' => 12]);

        $password_confirm =  $password;
        
        $req_login= $db-> prepare( "SELECT * FROM `utilisateurs` WHERE `login` = ? " );
        $req_login->execute(array($login));
        $compare_login= $req_login->fetchall();
        var_dump($compare_login);

        if(count($compare_login) != 0){
            echo "Désolé ce login est déjà utilisé";

        }
        else{
            if($password == $password_confirm){
                $req_register= $db->prepare("INSERT INTO `utilisateurs`(`login`, `password`, `email`, `firstname`, `lastname`) VALUES (?, ?, ?, ?, ?)");
                $req_register->execute(array($login, $pwd_hash, $email, $firstname, $lastname));
                echo "utilisateur enregistré";
                return $user_array= array($login, $pwd_hash, $email, $firstname, $lastname);

            }
            else{
                echo "Votre mot de passe est différent";
            }
        }

    }

    public function connect($login, $password){


        if(!empty($login) && !empty($password)){

            global $db;

            $req_connect= $db-> prepare( "SELECT * FROM `utilisateurs` WHERE `login` = ? " );
            $req_connect->execute(array($login));
            $data_users= $req_connect->fetch();
           
            
            if(count($data_users) == 0)
            {
                echo "Login ou mot de passe incorrect";
            }
            elseif(password_verify($password, $data_users['password']))
            {
                $this->id=$data_users['id'];
                $this->login= $data_users['login'];
                $this->email= $data_users['email'];
                $this->firstname= $data_users['firstname'];
                $this->lastname= $data_users['lastname'];

                $req_id= $db-> prepare("SELECT id FROM `utilisateurs` WHERE `login` = ? ");
                $req_id->execute(array($login));
                $id_users= $req_id->fetch();
                
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
        global $db;
        $req_delete= $db->prepare(" DELETE FROM `utilisateurs` WHERE login = '$this->login' ");
        $req_delete->execute();
        unset($_SESSION);
        session_destroy();
        echo "Utilisateur supprimé";
        

    }
    public function update($login, $password, $email, $firstname, $lastname){

        global $db;

        $password_confirm= $password;
        $pwd_hash=password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        
        if(!empty($login) && !empty($password) && !empty($password_confirm) ){

            $req_login= $db->prepare("SELECT * FROM `utilisateurs` WHERE `login` = ? ");
            $req_login-> execute([$login]);
            $compare_login= $req_login->fetchall();
            var_dump($compare_login);

            

            if(count($compare_login) != 0){
                echo "Désolé ce login est déjà utilsé";
            }
            elseif(!empty($password) && !empty($password_confirm)){
                if($password === $password_confirm ){

                    $req_update= $db-> prepare("UPDATE `utilisateurs` SET `login`=  ? ,`password`= ? , email= ? , firstname= ? , lastname = ? WHERE id = $this->id") ;

                    $req_update->execute(array($login, $pwd_hash, $email, $firstname, $lastname ));
                   
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

        global $db;
        
        $req_all_info= $db-> prepare("SELECT * FROM utilisateurs WHERE login= '$this->login'");
        $req_all_info->execute();
        $all_infos= $req_all_info->fetchAll();
        

        return $all_infos;

    }

    public function getLogin(){
        
         return $this->login;
    }

    public function getEmail(){

     

        return $this->email;
    }

    public function getFirstname(){
    

        return $this->firstname;
    }

    public function getLastname(){
    

        return $this->lastname;
    }

    public function refresh(){
        

        $req= $db-> prepare(" SELECT * FROM utilisateurs WHERE id = '$this->id' ");
        $req->execute();
        $result= $req->fetchAll();

         $this->login =$result[0]['login'];
         $this->email =$result[0]['email'] ;
         $this->firstname =$result[0]['firstname'] ;
         $this->lastname =$result[0]['lastname'] ;

         return $array =[ $this->login, $this->email, $this->firstname, $this->lastname];





    }
}



$user= new userpdo;

 //$user->register("pdo_user3", "aze","monmail@mail","firstname", "lastname");
 $user->connect("new_userpdo7", "aze");
 //$user->update("new_userpdo7", "aze","monmail@mail","firstname", "lastname")
 var_dump($user->getAllInfos());
 //$user->delete();
 //var_dump($user->refresh());
//$state= $user->disconnect();
//var_dump($state);



?>

