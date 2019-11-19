<?php
/**
 * Plugin Name: Share Drive
 * Plugin URI: 
 * Description: Rest Api To handle users and there information for Share Drive App.
 * Version: 1.0
 * Author: Simran
 * Author URI:  http://mutewebtechnologies.com/
 */

 /*** Setting Routs for the webservice */
add_action( 'rest_api_init', 'register_api_hooks' );
function register_api_hooks() {
  register_rest_route(
    'drive', '/login/',
    array(
      'methods'  => 'POST',
      'callback' => 'login',
    )
  );


  register_rest_route(
    'drive', '/logout/',
    array(
      'methods'  => 'POST',
      'callback' => 'loggout',
    )
  );

register_rest_route(
    'drive', '/changePassword/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'changePassword',
    )
  );

register_rest_route(
    'drive', '/verifyToken/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'verifyToken',
    	)
  );


register_rest_route(
    'drive', '/getUserInfo/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'getUserInfo',
    	)
  );


register_rest_route(
    'drive', '/getUserOrders/',
	    array(
    	  'methods'  => 'POST',
      	  'callback' => 'getUserOrders',
    	)
  );


register_rest_route(
    'drive', '/insertFiles/',
        array(
          'methods'  => 'POST',
          'callback' => 'insertFiles',
        )
  );

register_rest_route(
    'drive', '/updateUserDetails/',
        array(
          'methods'  => 'POST',
          'callback' => 'updateUserDetails',
    )
  );

}


 /**
     * 
     * @param type $requestedToken
     *  Logout User by token
*/
function loggout($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
      if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
			));

         	if(!empty($users)){

         		$user_id=$users[0]->ID;
         			if (delete_user_meta($user_id, '__auth_token_for_shared_drive__') ) {
         				$responsecreds['status']="success";
         				$responsecreds['data']=$responseUser;
         				return $responsecreds;
         			}
         	
         	}else{
	         		$responsecreds['status']="error";
	         		$responsecreds['message']="Invalid Token Please try again";
	         		return $responsecreds;	         		
	         	}  


	    }else{
	         		$responsecreds['status']="error";
	         		$responsecreds['message']="Invalid Token Please try again";
	         		return $responsecreds;	         		
	      	}         		
 }
         



 /**
     * 
     * @param type $request
     *  Checking Login Authorization
*/
function login($request){
          $creds = array();
          header('Access-Control-Allow-Origin: *');
         $creds['user_login'] = $request["username"];
          $creds['user_password'] =  $request["password"];
          $creds['remember'] = true;
          $user = wp_signon( $creds, false );
    ## Invalid User 
    if ( is_wp_error($user) ){
    			$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
   	}else{
        ## Set Token If user is valid
     		$token = getIndentificationString().$creds['user_login'].time();     		
     		$user_endoredata = json_decode(json_encode($user));
     		$user_id = $user_endoredata->data->ID;
     		$responseUser=get_user_meta($user->data->ID);
     		         		
     		update_user_meta( $user_id, '__auth_token_for_shared_drive__', $token);
     		update_user_meta( $user_id, '__auth_token_date_and_time_for_shared_drive__', time());
     		$responseUser=array();
     		$responseUser['user_nicename']=$user->data->user_nicename;
     		$responseUser['user_email']=$user->data->user_email;
     		$responseUser['display_name']=$user->data->display_name;
     		$responseUser['first_name']=$user->data->first_name;
     		$responseUser['last_name']=$user->data->last_name;
     		
     		$responsecreds['status']="success";
         	$responsecreds['data']=$responseUser;
         	$responsecreds['token']=$token;
         	return $responsecreds;

 	 
 	}
 	 
}


 /**
     * 
     * @param type $request
     *  Checking Login verifyToken 
*/
function verifyToken($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){         		

         			$user=get_user_meta($users[0]->data->ID);
         			$responseUser=array();
		     		$responseUser['user_nicename']=$user['nickname'][0];
		     		$responseUser['user_email']=$user['user_email'][0];
		     		$responseUser['display_name']=$user['display_name'][0];
		     		$responseUser['first_name']=$user['first_name'][0];
		     		$responseUser['last_name']=$user['last_name'][0];
		     		$responseUser['token']=$request["token"];

		     		$responsecreds['status']="success";
		     		$responsecreds['data']=$responseUser;
         		return $responsecreds;

         		
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }
         
  
 	 
}




 /**
     * 
     * @param type $request
     *  Checking Login getUserInfo 
*/
function getUserInfo($request){
          $responsecreds = array();
          header('Access-Control-Allow-Origin: *');
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){

         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){
         		$user=get_user_meta($users[0]->data->ID);
         		$responseUser=array();
		     		$responseUser['user_nicename']=$user['nickname'][0];
		     		$responseUser['user_email']=$user['user_email'][0];
		     		$responseUser['display_name']=$user['display_name'][0];
		     		$responseUser['first_name']=$user['first_name'][0];
		     		$responseUser['last_name']=$user['last_name'][0];
		     		$responseUser['token']=$request["token"];
                    
                    $responseUser['billing']['billing_first_name']=$user['billing_first_name'][0];
                    $responseUser['billing']['billing_last_name']=$user['billing_last_name'][0];
                    $responseUser['billing']['billing_email']=$user['billing_email'][0];
                    $responseUser['billing']['billing_phone']=$user['billing_phone'][0];
                    $responseUser['billing']['billing_country']=$user['billing_country'][0];
                    $responseUser['billing']['billing_state']=$user['billing_state'][0];
                    $responseUser['billing']['billing_city']=$user['billing_city'][0];
                    $responseUser['billing']['billing_address_1']=$user['billing_address_1'][0];
                    $responseUser['billing']['billing_address_2']=$user['billing_address_2'][0];
                    $responseUser['billing']['billing_company']=$user['billing_company'][0];


                    $responseUser['shipping']['shipping_first_name']=$user['shipping_first_name'][0];
                    $responseUser['shipping']['shipping_last_name']=$user['shipping_last_name'][0];
                    $responseUser['shipping']['shipping_company']=$user['shipping_company'][0];
                    $responseUser['shipping']['shipping_address_1']=$user['shipping_address_1'][0];
                    $responseUser['shipping']['shipping_address_2']=$user['shipping_address_2'][0];
                    $responseUser['shipping']['shipping_city']=$user['shipping_city'][0];
                    $responseUser['shipping']['shipping_postcode']=$user['shipping_postcode'][0];
                    $responseUser['shipping']['shipping_country']=$user['shipping_country'][0];
                    $responseUser['shipping']['shipping_state']=$user['shipping_state'][0];
             



		     		$responsecreds['status']="success";
         			$responsecreds['data']=$responseUser;

		     		
         		return $responsecreds;

         		
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         
  
 	 
}



 /**
     * 
     * @param type $request
     *  Change the password of loggedin user using restapi
*/
function changePassword($request){

	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){
		          $creds = array();
		          $new_password=$request["newpassword"];
		          $conform_password=$request["ccpassword"];

		          if($new_password===$conform_password){
			         		wp_set_password( $new_password, $users[0]->ID ); 	
			         		$responsecreds['status']="error";
	         				$responsecreds['message']="Password Changed Successfully";
	         				return $responsecreds;
		          }else{

		          		$responsecreds['status']="success";
         				$responsecreds['message']="new Password and conform password didn't match";
         				return $responsecreds;
		          }
		     	 



		      }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}


    }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
   
}



/**
     * 
     * @param type $request
     *  Update User Information by Token
*/
function updateUserDetails($request){

	## Verify Token
        $responsecreds = array();
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));

         	if(!empty($users)){

		             $user_id=$users[0]->ID;
                     $update_for=$request["update_type"];
                     $_first_name=$request["first_name"];
                     $_last_name=$request["last_name"];
                     $_company=$request["company_name"];
                     $_country=$request["country"];
                     $_state=$request["state"];
                     $_city=$request["city"];
                     $_postcode=$request["pincode"];
                     $_address_1=$request["address_1"];
                     $_address_2=$request["address_2"];
                     $_email=$request["email"];
                     $_phone=$request["phone"];
		          
                  if($update_for=="personal"){

                         /*update personal Information*/
                     update_user_meta( $user_id, 'first_name', $_first_name );
                     update_user_meta( $user_id, 'last_name', $_last_name );
                     update_user_meta( $user_id, 'company_name', $_company );




                  }elseif($update_for=="billing"){

                     /*update billing Information*/

                     update_user_meta( $user_id, 'billing_first_name', $_first_name );
                     update_user_meta( $user_id, 'billing_last_name', $_last_name );
                     update_user_meta( $user_id, 'billing_company', $_company );
                     update_user_meta( $user_id, 'billing_country', $_country );
                     update_user_meta( $user_id, 'billing_state', $_state );
                     update_user_meta( $user_id, 'billing_city', $_city );
                     update_user_meta( $user_id, 'billing_postcode', $_postcode );
                     update_user_meta( $user_id, 'billing_address_1', $_address_1 );
                     update_user_meta( $user_id, 'billing_address_2', $_address_2 );
                     update_user_meta( $user_id, 'billing_email', $_email );
                     update_user_meta( $user_id, 'billing_phone', $_phone );         


                  }elseif($update_for=="shipping"){
                    /*update shipping Information*/
                
                     update_user_meta( $user_id, 'shipping_first_name', $_first_name );
                     update_user_meta( $user_id, 'shipping_last_name', $_last_name );
                     update_user_meta( $user_id, 'shipping_company', $_company );
                     update_user_meta( $user_id, 'shipping_country', $_country );
                     update_user_meta( $user_id, 'shipping_state', $_state );
                     update_user_meta( $user_id, 'shipping_city', $_city );
                     update_user_meta( $user_id, 'shipping_postcode', $_postcode );
                     update_user_meta( $user_id, 'shipping_address_1', $_address_1 );
                     update_user_meta( $user_id, 'shipping_address_2', $_address_2 );
                    
                  }
     



		         

		          		$responsecreds['status']="success";
         				$responsecreds['message']="Information Updated Successfully!!";
         				return $responsecreds;
		         



		      }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}


    }else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
   
}


function add_cors_http_header(){
  header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');



/**
     * 
     * @param type $request
     *  Get All Order of user By token
*/
function getUserOrders($request){
          $responsecreds = array();
         
    	## Verify Token
         if(!empty($request["token"]) && isset($request["token"]) ){
         	$users = get_users(array(
					    'meta_key'     => '__auth_token_for_shared_drive__',
					    'meta_value'   => $request["token"],
					    'meta_compare' => '=',
		));
         	global $wpdb;

         	if(!empty($users)){
         		$responseUser=get_user_meta($users[0]->ID);
         		$posts = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_customer_user' AND  meta_value = " .$users[0]->ID, ARRAY_A);        
          $order_data=array();
          for($i=0;$i<count($posts);$i++){
            $order = wc_get_order( $posts[$i]['post_id'] );
            $d = array(
              'id' => $order->get_id(),
              'order_number' => $order->get_order_number(),
              'created_at' => $order->get_date_created()->date('Y-m-d H:i:s'),
              'updated_at' => $order->get_date_modified()->date('Y-m-d H:i:s'),
              'completed_at' => !empty($order->get_date_completed()) ? $order->get_date_completed()->date('Y-m-d H:i:s') : '',
              'status' => $order->get_status(),
              'currency' => $order->get_currency(),
              'total' => wc_format_decimal($order->get_total(), $dp),
              'subtotal' => wc_format_decimal($order->get_subtotal(), $dp),
              'total_line_items_quantity' => $order->get_item_count(),
              'total_tax' => wc_format_decimal($order->get_total_tax(), $dp),
              'total_shipping' => wc_format_decimal($order->get_total_shipping(), $dp),
              'cart_tax' => wc_format_decimal($order->get_cart_tax(), $dp),
              'shipping_tax' => wc_format_decimal($order->get_shipping_tax(), $dp),
              'total_discount' => wc_format_decimal($order->get_total_discount(), $dp),
              'shipping_methods' => $order->get_shipping_method(),
              'order_key' => $order->get_order_key(),
              'payment_details' => array(
                'method_id' => $order->get_payment_method(),
                'method_title' => $order->get_payment_method_title(),
                'paid_at' => !empty($order->get_date_paid()) ? $order->get_date_paid()->date('Y-m-d H:i:s') : '',
              ),
              'billing_address' => array(
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
                'company' => $order->get_billing_company(),
                'address_1' => $order->get_billing_address_1(),
                'address_2' => $order->get_billing_address_2(),
                'city' => $order->get_billing_city(),
                'state' => $order->get_billing_state(),
                'formated_state' => WC()->countries->states[$order->get_billing_country()][$order->get_billing_state()], //human readable formated state name
                'postcode' => $order->get_billing_postcode(),
                'country' => $order->get_billing_country(),
                'formated_country' => WC()->countries->countries[$order->get_billing_country()], //human readable formated country name
                'email' => $order->get_billing_email(),
                'phone' => $order->get_billing_phone()
              ),
              'shipping_address' => array(
                'first_name' => $order->get_shipping_first_name(),
                'last_name' => $order->get_shipping_last_name(),
                'company' => $order->get_shipping_company(),
                'address_1' => $order->get_shipping_address_1(),
                'address_2' => $order->get_shipping_address_2(),
                'city' => $order->get_shipping_city(),
                'state' => $order->get_shipping_state(),
                'formated_state' => WC()->countries->states[$order->get_shipping_country()][$order->get_shipping_state()], //human readable formated state name
                'postcode' => $order->get_shipping_postcode(),
                'country' => $order->get_shipping_country(),
                'formated_country' => WC()->countries->countries[$order->get_shipping_country()] //human readable formated country name
              ),
              'note' => $order->get_customer_note(),
              'customer_ip' => $order->get_customer_ip_address(),
              'customer_id' => $order->get_user_id(),
              'view_order_url' => $order->get_view_order_url(),
              'line_items' => array(),
              'shipping_lines' => array(),
              'tax_lines' => array(),
              'fee_lines' => array(),
              'coupon_lines' => array(),
            );
            //getting all line items
            foreach ($order->get_items() as $item_id => $item) {
              $product = $item->get_product();
              $product_id = null;
              $product_sku = null;
              // Check if the product exists.
              if (is_object($product)) {
                $product_id = $product->get_id();
                $product_sku = $product->get_sku();
              }
              $d['line_items'][] = array(
                  'id' => $item_id,
                  'subtotal' => wc_format_decimal($order->get_line_subtotal($item, false, false), $dp),
                  'subtotal_tax' => wc_format_decimal($item['line_subtotal_tax'], $dp),
                  'total' => wc_format_decimal($order->get_line_total($item, false, false), $dp),
                  'total_tax' => wc_format_decimal($item['line_tax'], $dp),
                  'price' => wc_format_decimal($order->get_item_total($item, false, false), $dp),
                  'quantity' => wc_stock_amount($item['qty']),
                  'tax_class' => (!empty($item['tax_class']) ) ? $item['tax_class'] : null,
                  'name' => $item['name'],
                  'product_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product->get_parent_id() : $product_id,
                  'variation_id' => (!empty($item->get_variation_id()) && ('product_variation' === $product->post_type )) ? $product_id : 0,
                  'product_url' => get_permalink($product_id),
                  'product_thumbnail_url' => wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'thumbnail', TRUE)[0],
                  'sku' => $product_sku,
                  'meta' => wc_display_item_meta($item, ['echo' => false])
              );
            }
            //getting shipping
            foreach ($order->get_shipping_methods() as $shipping_item_id => $shipping_item) {
              $d['shipping_lines'][] = array(
                'id' => $shipping_item_id,
                'method_id' => $shipping_item['method_id'],
                'method_title' => $shipping_item['name'],
                'total' => wc_format_decimal($shipping_item['cost'], $dp),
              );
            }
            array_push($order_data, $d);
          }
          $responsecreds['status']="success";
          $responsecreds['data']=$order_data;
          return $responsecreds;
         	}else{
         		$responsecreds['status']="error";
         		$responsecreds['message']="Invalid Token Please try again";
         		return $responsecreds;
         		
         	}
         	
	
         }
         
  
 	 
}





 /**
     * 
     * @param type $request
     *  Checking Login getUserInfo 
*/
function insertFiles($request){
      //     $responsecreds = array('myfiles',array(
      //       'Key',"dude/jackass.png",
      //       'lastmodified',"dude/jackass.png",
      //       'size',"dude/jackass.png",
      //       'ETag',"dude/jackass.png", 
      //       array(
      //       'Key',"dude/jackass.png",
      //       'lastmodified',"dude/jackass.png",
      //       'size',"dude/jackass.png",
      //       'ETag',"dude/jackass.png"
      //       )
      //    )
      // );
          
          header('Access-Control-Allow-Origin: *');
         $filesArray=array();


         $data['Key']="dude/jackass.png";
         $data['lastmodified']="Thu Nov 14 2019 11:04:00 GMT+0530 (India Standard Time)";
         $data['size']="1829127";
         $data['ETag']="12931ab6dbc48da5089af02c31caf652";
         $data['isDeleted']=false;
         $responsecreds['myfiles'][]=$data;
         
         $data['Key']="dudasfasfssssse/jackafdasfasfss.png";
         $data['lastmodified']="Thu Nov 14 2019 11:04:00 GMT+0530 (India Standard Time)";
         $data['size']="182912242347";
         $data['ETag']="12931ab6dbc48asfasfdda5089af02c31caf652";
         $data['isDeleted']=false;
         $responsecreds['myfiles'][]=$data;


         $data['Key']="dudasfasdfasfase/jackass.png";
         $data['lastmodified']="Thu Nov 14 2019 11:04:00 GMT+0530 (India Standard Time)";
         $data['size']="182234242429127";
         $data['ETag']="12931ab6dbc48da5089af02c31caf652";
         $data['isDeleted']=false;
         $responsecreds['myfiles'][]=$data;


          $adsf['Key']="dudasfasdfasfase/jackass.png";
         $adsf['lastmodified']="Thu Nov 14 2019 11:04:00 GMT+0530 (India Standard Time)";
         $adsf['size']="182234242429127";
         $adsf['ETag']="12931ab6dbc48da5089af02c31caf652";
         $adsf['isDeleted']=false;
         $responsecreds['abcdef'][]=$adsf;



         
          echo json_encode($responsecreds);die;

                   
        ## Verify Token
         if(!empty($request["email"]) && isset($request["email"]) ){
            $users = get_user_by( 'email', 'moiz@creativemirza.com' );
            


            if(!empty($users)){
                $havemyfiles = get_user_meta($users->ID, 'myfiles', false);
                if(!empty($havemyfiles)){
                   $filesArray= $havemyfiles;
                   //update  previous get and add new  :-              Condition
                   
                   //$user_id=$users[0]->ID;

                   //uploadedsfiles  :- key with the name of this

                   // $updated = update_user_meta( $user_id, 'uploadedsfiles', $responsecreds );

                }else{
                	//add new key and value
                	// $updated = update_user_meta( $user_id, 'uploadedsfiles', $responsecreds );
                }

                die;
                


                    $responsecreds['status']="success";
                    $responsecreds['data']=$havemyfiles;

                    
                return $responsecreds;

                
            }else{
                $responsecreds['status']="error";
                $responsecreds['message']="Invalid Token Please try again";
                return $responsecreds;
                
            }
            
    
         }else{
                $responsecreds['status']="error";
                $responsecreds['message']="Invalid Token Please try again";
                return $responsecreds;
                
            }
         
  
     
}


function getIndentificationString($length = 50) {
    $base64Chars = 'Aqrstuvwadfxyz0%^1234asf56789abcde&^fghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
    $result = '';
    for ($i = 0; $i < $length; ++$i) {
        $result .= $base64Chars[mt_rand(0, strlen($base64Chars) - 1)];
    }

    return $result;
}

