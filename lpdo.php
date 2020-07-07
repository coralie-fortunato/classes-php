<?php
session_start();

class lpdo{
 
    private $bdd;
    private $lastquery;
    private $lastresult;


    
    public function __construct($host=null, $username=null, $password=null , $db=null){
        
        $this->bdd=new mysqli($host, $username, $password, $db);
        

    }

    public function connect($host, $username, $password, $db){

        
        
        if($this->bdd){

            $this->bdd->close();
            $this->bdd=new mysqli($host, $username, $password, $db);
              
        }
        else{
            $this->bdd=new mysqli($host, $username, $password, $db);
           
        }
        
    }

    public function __destruct(){
        $this->bdd=null;
    }


    public function close(){
        $this->bdd->close();
    }

    public function execute($query){

        
        $data=$this->bdd->query($query);
        $result= $data-> fetch_all();
        $this->lastquery=$query;
        $this->lastresult=$result;
        return $result;

    }

    public function getLastQuery(){
        return $this->lastquery;
    }

    public function getLastResult(){
        return $this->lastresult;
    }

    public function getTables(){
        $tables = $this->bdd->query(" SHOW TABLES ;");
        $result= $tables->fetch_all();
        return $result;
    }
    
    public function getFields($table){
        $fields= $this->bdd->query(" SHOW COLUMNS FROM $table ; ");
        $result= $fields->fetch_all();
        return $result ;

    }

}
$user= new lpdo("localhost","root","","classes");
$user->connect("localhost","root","","classes");


$query="SELECT * FROM utilisateurs";
$result= $user->execute($query);
$lastquery=$user->getLastQuery();
var_dump($lastquery);
$lastresult=$user->getLastResult();
var_dump($lastresult);
$tab= $user->getTables();
var_dump($tab);

$table= 'utilisateurs';

$field= $user->getFields($table);
var_dump($field);
?>