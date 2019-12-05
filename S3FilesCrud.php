<?php

function getUserS3Files($request){
  $responsecreds = array();
  $auth = apache_request_headers();
  ## Verify Token
  if(isset($auth['Authorization']) && $auth['Authorization']!=""){
    
    $req=$request['folderName'];
    if(!empty($req)){
      if ($request['userId']) {
        $userId = $request['userId'];
      } else {
        $users = get_users(array(
          'meta_key'     => '__auth_token_for_shared_drive__',
          'meta_value'   => $auth['Authorization'],
          'meta_compare' => '=',
        ));
        if(!empty($users)){
          $userId=$users[0]->ID;
        }
      }
      $files = get_user_meta($userId, $req, true);
      $activefiles=array();
      if(!empty($files)){
        for($i=0;$i<count($files[$req]);$i++){
          $item = $files[$req][$i];
          if ($item['isDeleted']) {
            //
          } else {
            array_push($activefiles, $item);
          }
        }
      }
      return new WP_REST_Response($activefiles, 200);
    } else {
      $resp = array();
      $resp['status']="error";
      $resp['message']="Folder name cannot be empty.";
      return new WP_REST_Response($resp, 404);
    }
  }else{
    return return401Err();
  }
}

function deleteUsersS3Files($request){
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
        $folder_id = $request['folderName'];
        $fileId = $request['id'];
        $user_id=$users[0]->ID;
        $newfiles=array();
        $get_myfiles =  get_user_meta( $user_id, $folder_id, true);
        $updatedata = array();
          foreach($get_myfiles[$folder_id] as $myfiles){
              $updatedata =  $myfiles;
              if(in_array($fileId, $myfiles)){
                  $myfiles['isDeleted'] = true;
                  $newfiles[$folder_id][]=$myfiles;
              }else{
                  $newfiles[$folder_id][]=$myfiles;
              }
          }
          $updated = update_user_meta( $user_id, $folder_id, $newfiles);
          $responsecreds['status']="success";
          $responsecreds['message']="File Deleted Successfully!";
          return new WP_REST_Response($responsecreds, 200);
    }
  }
}
?>