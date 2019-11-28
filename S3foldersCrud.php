<?php
function getUsersFolders($request){
  $responsecreds = array();
  $auth = apache_request_headers();
  ## Verify Token
  if(isset($auth['Authorization']) && $auth['Authorization']!=""){
    $users = get_users(array(
      'meta_key'     => '__auth_token_for_shared_drive__',
      'meta_value'   => $auth['Authorization'],
      'meta_compare' => '=',
    ));
    if(!empty($users)){
      $user_id=$users[0]->ID;
      $files = get_user_meta($user_id, 'allcreatedFolders', true);
      if (!empty($files)) {
        return new WP_REST_Response(explode(',', $files), 200);
      } else {
        return returnFolderNotFoundErr();
      }
    }
  }
}


?>