<?php

	add_filter( 'sanitize_file_name', array(hw_cyrlat_filename(),'convert') );

	function hw_cyrlat_filename_exists(){
		add_action( 'shutdown', array( hw_cyrlat_filename(), 'scan_and_convert_fiels' ) );
	}
	register_activation_hook(__FILE__, 'hw_cyrlat_filename_exists');