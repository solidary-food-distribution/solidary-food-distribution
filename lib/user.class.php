<?php


class User{
  public $id;
  public $name;
  public $email;
  public $member_id; //can be switched
  private $password;
  private $access=array();
  private $members=array();

  public function __construct($id){
    require_once('sql.class.php');
    $qry="SELECT * FROM msl_users WHERE id='".intval($id)."'";
    $res=SQL::selectOne($qry);
    $this->init_with_array($res);
    if($this->id){
      $this->init_access();
    }
  }

  public function init_with_array($array){
    foreach($array as $k=>$v){
      if(property_exists('User',$k)){
        $this->{$k}=$v;
      }
    }
  }

  private function init_access(){
    $this->access=array();
    $this->members=array();
    $qry="SELECT access,member_id FROM msl_access WHERE start<=CURDATE() AND end>=CURDATE() AND user_id='".intval($this->id)."'";
    $res=SQL::select($qry);
    foreach($res as $v){
      if($v['member_id']){
        $this->access[$v['access']][$v['member_id']]=1;
        $this->members[$v['member_id']]=1;
      }else{
        $this->access[$v['access']]=1;
      }
    }
    if(isset($this->access['products']) 
      || isset($this->access['members'])
      || isset($this->access['users'])
      || isset($this->access['orders'])
      || isset($this->access['debits'])){
      $this->access['admin']=1;
    }
  }

  public static function get_by_email_password($email,$password){
    require_once('sql.class.php');
    $qry="SELECT * FROM msl_users WHERE email='".SQL::escapeString($email)."'";
    $res=SQL::selectOne($qry);
    if(empty($res)){
      return;
    }
    if(!password_verify($password, $res['passwd'])){
      return;
    }
    return new User($res['id']);
  }

  public function set_session(){
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
