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
            $password_confirm= htmlspecialchars($_POST["confirm_password"]);
            $login=htmlspecialchars($_POST["login"]);
            $password= htmlspecialchars($_POST["password"]);
            $password_confirm= htmlspecialchars($_POST["confirm_password"]);
            $email=htmlspecialchars($_POST['email']);
            $firstname=htmlspecialchars($_POST['firstname']);
            $lastname=htmlspecialchars($_POST['lastname']);

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
        $login=htmlentities($_POST["login"]);
        $password= htmlentities($_POST["password"]);

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
        header("Location:user.php");
       
    }

    public function delete(){
        $db= mysqli_connect("localhost","root","","classes");
        $req_delete=" DELETE FROM `utilisateurs` WHERE id = $_SESSION[id]";
        mysqli_query($db, $req_delete);
        unset($_SESSION);
        session_destroy();

    }
    public function update($login, $password, $email, $firstname, $lastname){

        $db= mysqli_connect("localhost","root","","classes");

        $new_login= htmlspecialchars($_POST['new_login']);
        $new_email= htmlspecialchars($_POST['new_email']);
        $new_firstname= htmlspecialchars($_POST['new_firstname']);
        $new_lastname= htmlspecialchars($_POST['new_lastname']);
        $new_password= htmlspecialchars($_POST['new_password']);
        $password_confirm= htmlspecialchars($_POST
        ['confirm_password']);
        $pwd_hash=password_hash($new_password, PASSWORD_DEFAULT, ['cost' => 12]);

        if(!empty($new_login) && !empty($password) && !empty($password_confirm) ){
            $req_login="SELECT * FROM `utilisateurs` WHERE login='$new_login'";
            $query_login= mysqli_query($db, $req_login);
            $compare_login=mysqli_fetch_all($query_login);
            $user_login=$_SESSION['login'];
             if(count($compare_login) != 0){
                echo "Désolé ce login est déjà utilsé";
            }
            elseif(!empty($password) && !empty($password_confirm)){
                if($new_password === $password_confirm ){
                    $req_update2="UPDATE `utilisateurs` SET `login`= '$new_login',`password`= '$pwd_hash', email='$new_email', firstname='$new_firstname', lastname ='$new_lastname' WHERE login= '$user_login'" ;
                    mysqli_query($db, $req_update2);
                    $_SESSION["login"]=$new_login;
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
        $id_user= $_SESSION['id'];
        $req_all_info= "SELECT * FROM utilisateurs WHERE id= '$id_user'";
        $query_info= mysqli_query($db, $req_all_info);
        $all_infos= mysqli_fetch_all($query_info, MYSQLI_ASSOC);
        return $all_infos;

    }

    public function getLogin(){
        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req_login= "SELECT login FROM utilisateurs WHERE id= '$id_user' ";
        $query_login= mysqli_query($db, $req_login);
        $user_login= mysqli_fetch_all($query_login);
        return $user_login[0][0];
    }

    public function getEmail(){
        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req_email= "SELECT email FROM utilisateurs WHERE id= '$id_user' ";
        $query_email= mysqli_query($db, $req_email);
        $user_email= mysqli_fetch_all($query_email);
        return $user_email[0][0];
    }

    public function getFirstname(){
        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req_firstname= "SELECT firstname FROM utilisateurs WHERE id= '$id_user' ";
        $query_firstname= mysqli_query($db, $req_firstname);
        $user_firstname= mysqli_fetch_all($query_firstname);
        return $user_firstname[0][0];
    }

    public function getLastname(){
        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req_lastname= "SELECT lastname FROM utilisateurs WHERE id= '$id_user' ";
        $query_lastname= mysqli_query($db, $req_lastname);
        $user_lastname= mysqli_fetch_all($query_lastname);
        return $user_lastname[0][0];
    }

    public function refresh(){
        $db= mysqli_connect("localhost","root","","classes");
        $id_user= $_SESSION['id'];
        $req=" SELECT * FROM utilisateurs WHERE id = '$id_user' ";
        $query= mysqli_query($db, $req);
        $result=mysqli_fetch_all($query, MYSQLI_ASSOC);
        echo $this->login =$result[0]['login'];
        echo $this->email =$result[0]['email'];
        echo $this->firstname =$result[0]['firstname'];
        echo $this->lastname =$result[0]['lastname'];





    }
     

}


if(isset($_POST['valider'])){

    $login=htmlspecialchars($_POST["login"]);
    $password= htmlspecialchars($_POST["password"]);
    $password_confirm= htmlspecialchars($_POST["confirm_password"]);
    $email=htmlspecialchars($_POST['email']);
    $firstname=htmlspecialchars($_POST['firstname']);
    $lastname=htmlspecialchars($_POST['lastname']);

    $register_user= new user();
    $result= $register_user->register($login, $password, $email, $firstname, $lastname);
    var_dump($result);
    
}

if(isset($_POST['connect'])){
    $login=htmlentities($_POST["login"]);
    $password= htmlentities($_POST["password"]);
    $connect_user= new user;
    $connect_user->connect($login, $password);
}
    
if(isset($_GET['disconnect'])){
    $disconnect_user= new user;
    $disconnect_user-> disconnect();
}
if(isset($_GET['delete'])){
    $delete_user= new user;
    $delete_user->delete();
}
if(isset($_POST['update'])){

    $new_login= htmlspecialchars($_POST["new_login"]);
    $new_email= htmlspecialchars($_POST['new_email']);
    $new_firstname= htmlspecialchars($_POST['new_firstname']);
    $new_lastname= htmlspecialchars($_POST['new_lastname']);
    $new_password= htmlspecialchars($_POST['new_password']);
    $password_confirm= htmlspecialchars($_POST['confirm_password']);

    $user_update= new user;
    $user_update-> update($new_login, $password_confirm, $new_email, $new_firstname, $new_lastname);


}
// L'utilisateur est-il connecté
$is_user_connected= new user;
$user_status=$is_user_connected->isConnected();
//echo $user_status;

// Info de l'utilisateur connecté

$user_info= new user;
$all_infos = $user_info->getAllInfos();
//var_dump($all_infos);

// Récupérer Login

$login= new user;
$data=$login->getLogin();
echo $data . " ";

// Récupérer email

$email= new user;
$data=$email->getEmail();
echo $data. " ";

// Récupérer firstname

$firstname= new user;
$data=$firstname->getFirstname();
echo $data. " ";

// Récupérer lastname

$lastname= new user;
$data=$lastname->getLastname();
echo $data. " ";



?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
     <form action="" method="POST">
     <label for="login"> Login</label>
     <input type="text" name="login" id="login">
     <label for="email">Email</label> 
     <input  type="text"  name="email" id=email>
     <label for="firstname" >Firstname</label>
     <input type="text" name="firstname" id="firstname">
     <label for="lastname" >Lastname</label>
     <input type="text" name="lastname" id="lastname">
     <label for="password">Mot de passe</label>
     <input type="password" name="password" id="password">
     <label for="password"> Confirmation de Mot de passe</label>
     <input type="password" name="confirm_password" id="confirm_password">
     <button type="submit" name="valider">VALIDER</button>
     
     
     </form>

     <h1>Se connecter</h1>
     <form action="" method="post">
        <input type="text" name="login" id="login" placeholder="Login">
        <input type="password" name="password" id="password">
        <button type="submit" name="connect">Se connecter</button>
     </form>
     <h1>Se deconnecter</h1>
     <a href="user.php?disconnect">Se déconnecter</a>

     <h1>Supprimer utilisateur</h1>

     <a href="user.php?delete">Supprimer utilisateur</a>

     <h1>Modifier informations utilisateur</h1>

     <form action="" method="POST">
     <label for="login"> Login</label>
     <input type="text" name="new_login" id="new_login">
     <label for="email">Email</label> 
     <input  type="text"  name="new_email" id=email>
     <label for="firstname" >Firstname</label>
     <input type="text" name="new_firstname" id="firstname">
     <label for="lastname" >Lastname</label>
     <input type="text" name="new_lastname" id="lastname">
     <label for="password">Mot de passe</label>
     <input type="password" name="new_password" id="password">
     <label for="password"> Confirmation de Mot de passe</label>
     <input type="password" name="confirm_password" id="confirm_password">
     <button type="submit" name="update">VALIDER</button>
     
     
     </form>

    </main>
</body>
</html>