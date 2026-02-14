<?php
declare(strict_types=1);

class User{
  public $id;
  public $name;
  public $email;
  public $password;
  public $member_id; //can be switched in future
  public $pickup_pin;
  public $access=array();
  public $members=array();
  public $last_login;

  public function password_verify($password){
    if($this->password == $password && trim($password)!==''){
      return true;
    }
    return password_verify($password, $this->password);
  }

  public function set_session(){
    if(isset($_SESSION['scale']) && $_SESSION['scale']){
      $scale_access = array(
        'deliveries' => 1,
        'pickups' => 1,
        'inventory' => 1
      );
      foreach($this->access as $access => $data){
        if(!isset($scale_access[$access])){
          unset($this->access[$access]);
        }
      }
    }
    $_SESSION['user']=array(
      'user_id'=>$this->id,
      'name'=>$this->name,
      'email'=>$this->email,
      'pickup_pin'=>$this->pickup_pin,
      'member_id'=>$this->member_id,
      'access'=>$this->access,
      'members'=>$this->members,
    );
  }

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = 
      "UPDATE msl_users SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE id='".intval($this->id)."'";
    sql_update($qry);
  }
}
