<?php
declare(strict_types=0);

class InfoUser{
  public $info_id;
  public $user_id;
  public $read;

  public static function create($info_id, $user_id){
    require_once('sql.inc.php');
    $qry = "INSERT INTO msl_info_users (info_id, user_id) VALUES ('".intval($info_id)."','".intval($user_id)."')";
    sql_insert($qry);
    $values = sql_select_one("SELECT * FROM msl_info_users WHERE info_id=".intval($info_id)." AND user_id=".intval($user_id));
    $info_user = new InfoUser();
    $info_user->_init_values($values); 
    return $info_user;
  }

  public function _init_values( $values ){
    foreach($values as $key => $value){
      if(property_exists($this, $key)){
        $this->{$key} = $value;
      }
    }
  }

  public function update( array $updates = array() ){
    require_once('sql.inc.php');
    $qry = "UPDATE msl_info_users SET ";
    $qry .= sql_build_update_query($updates).' ';
    $qry .= "WHERE info_id='".intval($this->info_id)."' AND user_id='".intval($this->user_id)."'";
    sql_update($qry);
  }
}