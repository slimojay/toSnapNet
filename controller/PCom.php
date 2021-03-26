<?php 
session_start();
require('BaseClass.php');
class PCom extends BaseClass{
public $coN; public $con;
public function connectToDb(){
    $this->coN = $this->connect('test_db', '', 'test_db');
    return $this->coN;
}
public function assignCon(){
    $this->con = connectToDb();
}


public function loginUser($email, $password){
    try{

        $sql = $this->connectToDb()->prepare("SELECT * FROM users WHERE email = ?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $res = $sql->get_result();
        if($res->num_rows == 0){
            $this->displayErrors("account does not exist");
            exit;
        }else{
            while($r = $res->fetch_assoc()){
            //$r = $res->fetch_assoc();
            if(password_verify($password, trim($r['password']))){
              //  $this->updateLastLogin($r['id']);
                $this->handleLogin($r['id'], $r['email'], $r['name']);
                   
            }else{
                $this->displayErrors("wrong login credentials " . $password);
                exit;
            }
        }
        }
      }catch(Exception $e){
          echo $e ;
      }
}

private function displayErrors($err){
    $error = "<center style='margin-top:10%;'><p class='text-danger'>$err</p></center>";
    echo $error;
}

private function handleLogin($id, $email, $name){
    $_SESSION['id'] = $id;
    $_SESSION['email'] = $email;
    $_SESSION['user'] = $name;
    echo "<script>window.location='../views/index.php'</script>";  
}

public function fetchWards(){
    $str = '';
       $sel = $this->connectToDb()->query("SELECT * FROM wards");
       
       if($sel->num_rows > 0){
           while($r = $sel->fetch_assoc()){
               $str .= "<option value='" . $r['id'] . "'>". ucwords($r['name']) . "</option>";
           }
           return $str;
       }else{
           return "<option>no wards available</option>";
       }
}

public function createUser($name, $email, $password, $url, $urlf){
    $name = $this->connectToDb()->real_escape_string($name);// or die($this->coN->error);
    $email = $this->connectToDb()->real_escape_string($email);
    $password = $this->connectToDb()->real_escape_string($password);
    $password = password_hash($password, PASSWORD_DEFAULT);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->displayErrors($email . "is not a valid email address");
      }
    $keys = array('name', 'email', 'password');
    $vals = array($name, $email, $password);
    $this->insert('users', $keys, $vals);
    if ($this->output['outcome'] == 'data inserted'){
        return "<script>alert('account successfully created.. kindly login please'); window.location='$url'</script>";
    }else{
        return "<script>alert('operation failed'); window.location='$urlf'</script>";
       //return $this->connectToDb()->error;
    }
}

public function createCenter($name, $table, $url){
    if(strlen($name) < 3){
        $this->displayErrors('entry looks invalid'); 
        exit;
    }
    /*if(strlen($name) <= 4){
        $abbr = $name;
    }else{
        $arr = str_split($name);
        $abbr = strtoupper($arr[0].$arr[1].$arr[3]);
        
    }*/
    $sql = "INSERT INTO $table (name) VALUES (?)";
    $d = $this->conn->prepare($sql);
    $d->bind_param("i", $name);
    
    if($d->execute()){
        echo "<script>alert('operation successful'); window.location='$url'</script>";
    }else{
        $this->displayErrors("operation failed \n could not create $name center");
    }
}

public function createCitizen($name, $gender, $add, $ph, $ward){
    $keys = array('name', 'gender', 'address', 'phone', 'ward_id');
    $vals = array($name, $gender, $add, $ph, $ward);
    $this->insert('citizens', $keys, $vals);
    if ($this->output['outcome'] == 'data inserted'){
        echo "<script>alert('operation successful'); window.location='$url'</script>";
    }else{
        $this->displayErrors("operation failed \n could not create $name citizen");
    }
}

public function report(){
    $str = '';
/* $sql = "SELECT c.*, s.*, l.*, w.*, c.name AS cn,
    w.name AS wn, s.name AS sn, l.name AS ln, FROM wards AS w RIGHT JOIN citizens AS c ON w.id = c.ward_id 
    INNER JOIN lgas AS l ON w.lga_id = l.id INNER JOIN states AS s ON l.state_id = s.id";*/

    $sql = "SELECT c.*, l.*, w.*, s.*, c.name AS cn, w.name AS wn, l.name AS ln, s.name AS sn FROM citizens AS c INNER JOIN wards AS w ON c.ward_id = w.id INNER JOIN lgas AS l ON w.lga_id = l.id INNER JOIN states AS s ON l.state_id = s.id";
   try{
   $sel = $this->connectToDb()->query($sql) OR $this->displayErrors($this->connectToDb()->error); 
   if ($sel->num_rows > 0){
       while($r = $sel->fetch_assoc()){
           $str .= "<tr><td>" . $r['cn'] . "</td><td>" . $r['wn'] . "</td><td>" . $r['ln'] . "</td><td>" . $r['sn'] . "</td></tr>";
           
       }
       return $str;
   }else{
       return $this->displayErrors("no record found");
   }
}
catch (Exception $e){
 die($e);
}
}


}
