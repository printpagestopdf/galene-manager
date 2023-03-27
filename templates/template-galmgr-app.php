<?php
/**
 * Template Name: Galène video conferencing manager
 * 
 */

add_filter( 'show_admin_bar', '__return_false' );

add_action('wp_enqueue_scripts', function(){
   wp_enqueue_script('galmgr_js');

   wp_enqueue_style( 'galmgr_fw_style' );
   wp_enqueue_style( 'galmgr_fw_list_style' );
   wp_enqueue_style( 'galmgr_fw_cc_style' );
   wp_enqueue_style( 'galmgr_style' );
});

//remove all non galene stylesheets
add_filter( "print_styles_array", function ($to_do) { 
	$filt_to_do=array_filter($to_do,function($var) {
		return (strpos($var,"galmgr_") === 0);
	});
	return $filt_to_do; 
}, 10, 1 );

		
ob_start();	
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head(); ?>
</head>
<body class="galene_app_page">
  <section class="section">
    <div class="container">
    

<?php
if( @$_REQUEST['galene_action'] != 'admin_screen_userselect' &&  @$_REQUEST['galene_action'] != 'admin_update_userselect' )
{
	global $more;
	$more=1;
	echo '<div class="content">';
	the_content();
	echo "</div>";
}

echo do_shortcode('[galene_main]');
?>
	</div>
	</section>

</body>
</html>
<?php
ob_end_flush();
?>