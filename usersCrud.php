<?php

function getAllUsers(){
  $resp = array();
  $auth = apache_request_headers();
  ## Verify Token
  if(isset($auth['Authorization']) && $auth['Authorization']!=""){
    $users = get_users(array(
      'meta_key'     => '__auth_token_for_shared_drive__',
      'meta_value'   => $auth['Authorization'],
      'meta_compare' => '=',
    ));
    if(!empty($users)){
      if($users[0]->roles[0]=="administrator"){
        $allUsers = get_users();
        $userdata = array();
        foreach ( $allUsers as $user ) {
          if($user->roles[0]!="administrator"){
            array_push($userdata, array(
              'id' => $user->id,
              'isOffline' => true,
              'createdDate' => $user->user_registered,
              'email' => $user->user_email,
              'name' => $user->user_nicename,
              'userStatus' => $user->user_status,
              'displayName' => $user->display_name,
            ));
          }
        } 
        return new WP_REST_Response($userdata, 200);  
      }
      else{
        $resp['code']="UN_AUTHORISED_ACCESS";
        $resp['status']="Error";
        $resp['message']="Not Admin";
        return new WP_REST_Response($resp, 200);
      }
    }
    else{
      $resp['status']="error";
      $resp['message']="Invalid Token Please try again";
      return new WP_REST_Response($resp, 401);
    }
  }
}
?>