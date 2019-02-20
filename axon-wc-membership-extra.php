<?php
/**
 * Plugin Name: Axon Wc Membership Extra
 * Plugin URI: https://axonil.com/plugins/axon-wc-membership-extra
 * Description: An woocommerce membership extra functionalities like custom bulk edit plugin
 * Version: 1.0.0
 * Author: M A MOYEEN
 * Author URI: https://axonil.com/authors/mamoyeen
 * 
 * 
*/
if ( ! defined( 'WPINC' ) ) {
	die; //abort direct call
}

/**
 * Extra features for Woocommerce membership plugin
 *
 * Provides extra features such as bulk update of expiary time.  
 * 
 *
 * @since 1.0.0
 */    
class Axon_Wc_Membership_Extra{

  /**
   * Axon Woocommerce Membership Extra feature constructor
   *
   * @since 1.0.0
  */
  public function __construct(){

     if(isset($_REQUEST['post_type'])){
        if($_REQUEST['post_type']=='wc_user_membership'){ // check the context to use
        	  // add bulk edit form element
              add_action( 'bulk_edit_custom_box',array( $this, 'bulk_edit' )); 
              // save bulk edit data
              add_action('save_post',array($this,'save_expiray'));
         }
     }
     
   }
   /**
    * saves the bulk edit expiration data
    * 
    * @since 1.0.0
    * @param int $post_id
    * @return null
   */
   public function save_expiray($post_id){
      if(isset($_GET['_axon_wc_member_expiary'])){
         try{
         	$user_membership = wc_memberships_get_user_membership( get_post($post_id));
            if(!empty($user_membership)){
            	$timezone  = wc_timezone_string();
            	$mysql_date_format = 'Y-m-d H:i:s';
            	$end_date=wc_memberships_parse_date( $_GET['_axon_wc_member_expiary'], $mysql_date_format );
            	$end_date=date( $mysql_date_format, wc_memberships_adjust_date_by_timezone( strtotime( $end_date ), 'timestamp', $timezone ) );
            	$user_membership->set_end_date( $end_date );
            	if(strtotime( $end_date ) > current_time( 'timestamp' ) && 'expired' === $user_membership->get_status()){
                   $user_membership->update_status( 'active' );
            	}
            }
  	 	
  	      }catch(Exception $e){
                    print_r($e);
  	      }	
  	 	
  	 }
  
   }
   
  /**
   * displays the bulk edit form element to give input
   * 
   * @since 1.0.0
   * @param string $column
   * @return null
  */
  public function bulk_edit( $column){
 		if ( 'expires' !== $column ) {
			return;
		}
  ?>
        <fieldset class="inline-edit-col-center" id="axon-wc-membership-fields-bulk">
			<div class="inline-edit-col">
				<div class="inline-edit-group">
					<label class="inline-edit-status alignleft">
						<span class="title">Expires</span>
						<input type="date" id="axon-wc-membership-expiary" name="_axon_wc_member_expiary" placeholder="YYYY-MM-DD">
					</label>
				</div>
			</div>
		</fieldset>
<?php

  }

}
// create instance
new Axon_Wc_Membership_Extra;

?>
