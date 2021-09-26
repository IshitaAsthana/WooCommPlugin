<?php 

defined( 'ABSPATH' ) or exit;

?>
<script type="text/javascript">
	jQuery( function( $ ) {
		$("#footer-thankyou").html("If you like <strong>WooCommPlugin</strong> please leave us a <a href='#'>★★★★★</a> rating. A huge thank you in advance!");
	});
</script>
<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e( 'Invoice Settings', 'woocommplugin' ); ?></h2>
	<h2 class="nav-tab-wrapper">
	<?php
	foreach ($settings_tabs as $tab_slug => $tab_title ) {
		$tab_link = esc_url("?page=woocommplugin_store_policies_submenu&tab={$tab_slug}");
		printf('<a href="%1$s" class="nav-tab nav-tab-%2$s %3$s">%4$s</a>', $tab_link, $tab_slug, (($active_tab == $tab_slug) ? 'nav-tab-active' : ''), $tab_title);
	}
	?>
	</h2>

	<form method="post" action="options.php" id="woocommplugin_store_policies" class="<?php echo "{$active_tab} {$active_section}"; ?>">
		<?php
			do_action( 'woocommplugin_store_policies_page', $active_tab, $active_section );
			if ( has_action( 'woocommplugin_store_policies_page_'.$active_tab )) {
				
				do_action( 'woocommplugin_store_policies_page_'.$active_tab, $active_section );
				
			} else {
				
				do_action( 'woocommplugin_store_policies_page_'.$active_tab, $active_section );
				
				submit_button();
			}
		?>
	</form>	
</div>
