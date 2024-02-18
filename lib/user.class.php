<?php
declare(strict_types=1);

class User{
  public $id;
  public $name;
  public $email;
  public $password;
  public $member_id; //can be switched
  public $access=array();
  public $members=array();

  public function password_verify($password){
    if($this->password == $password && trim($password)!==''){
      return true;
    }
    return password_verify($password, $this->password);
  }

  public function set_session(){
    if($_SESSION['scale']){
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
      'member_id'=>$this->member_id,
      'access'=>$this->access,
      'members'=>$this->members,
    );
  }
}
