<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<script>
	// ============================================================================
	// Variables for the view
	// ============================================================================

	// ============================================================================
	// Functions for the view
	// ============================================================================

	// ============================================================================
	// Events for the view
	// ============================================================================
	$(document).ready(function() {
		// No events for the view yet
	});

	// ============================================================================
	// Initialization for the view
	// ============================================================================
	$(document).ready(function() {
		// No initialization for the view yet
	});
</script>

<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Developers'), 'icon'=>'gears'));

echo '<h2 class="mt-4 mb-4"><b>PHP version: '.phpversion().'</b></h2>';

// PHP Ini
$uploadOptions = array(
	'upload_max_filesize'=>ini_get('upload_max_filesize'),
	'post_max_size'=>ini_get('post_max_size'),
	'upload_tmp_dir'=>ini_get('upload_tmp_dir')
);
printTable('File Uploads', $uploadOptions);

// Loaded extensions
printTable('Server information ( $_SERVER )', $_SERVER);

// PHP Ini
printTable('PHP Configuration options ( ini_get_all() )', ini_get_all());

// Loaded extensions
printTable('Loaded extensions',get_loaded_extensions());

// Locales installed
exec('locale -a', $locales);
printTable('Locales installed', $locales);

echo '<hr>';
echo '<h2>BLUDIT</h2>';
echo '<hr>';

// Constanst defined by Bludit
$constants = get_defined_constants(true);
printTable('Bludit Constants', $constants['user']);

// Site object
printTable('$site object database',$site->db);

