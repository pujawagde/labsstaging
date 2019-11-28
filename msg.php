<?php
function returnFolderNotFoundErr() {
  $responsecreds = array();
  $responsecreds['status']="error";
  $responsecreds['message']="Folder not found!";
  return new WP_REST_Response($responsecreds, 404);
}

function return401Err() {
  $responsecreds = array();
  $responsecreds['status']="error";
  $responsecreds['message']="Invalid Token Please try again!";
  return new WP_REST_Response($responsecreds, 401);
}
?>