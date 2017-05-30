<?php 
/* System not installed, yet!
----------------------------------*/
if( ! file_exists( dirname(__FILE__) . "/db-config.php" ) && basename( $_SERVER['REQUEST_URI'] ) !== 'install.php' ){
	header('Location: install.php');
	exit;
}

/* Save the root directory path in a constant
------------------------------------------------------*/
define( 'SITE_ROOT', dirname(__FILE__) . '/' );

/* Save the includes directory path in a constant
------------------------------------------------------*/
define( 'INC_DIR', SITE_ROOT . 'includes/' );

/* System is installed!
----------------------------*/
require_once dirname(__FILE__) . '/db-config.php';

/* Include core
--------------------*/
require_once INC_DIR . 'fields.php';
require_once INC_DIR . 'class-wp-hook.php';
require_once INC_DIR . 'plugin.php';
require_once INC_DIR . 'wp.php';

//------------------------------------//--------------------------------------//

function conectare_la_db(){
	$connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	return $connection;
}

//------------------------------------//--------------------------------------//

function selected( $val, $opt ){
	if( $val == $opt ){
		echo ' selected="selected"';
	}
}

/* Update or add option
----------------------------*/
function update_option( $option, $value ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$value    = htmlspecialchars( mysqli_real_escape_string( $conn, maybe_serialize( $value ) ) );

	$old_value = get_option( $option );

	if( isset($old_value) ){
		$sql = "UPDATE options SET value = '$value' WHERE option = '$option'";
	}
	else{
		$sql = "INSERT INTO options VALUES( NULL, '$option', '$value' )";
	}
	
	if( mysqli_query($conn, $sql) ){
		return true;
	}
	else{
		return false;
	}

	mysqli_close($conn);
}

/* Update or add option
----------------------------*/
function get_option( $option, $default_value = null ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$sql      = "SELECT * FROM options WHERE option = '$option' LIMIT 1";
	
	if( $result = mysqli_query($conn, $sql) ){
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if( !isset( $row['value'] ) ){
			return $default_value;
		}
		else{
			return maybe_unserialize( htmlspecialchars_decode( $row['value'] ) );
		}
	}
	else{
		return $default_value;
	}

	mysqli_close($conn);
}

function get_all_options(){
	$conn     = conectare_la_db();
	$sql      = "SELECT * FROM options";
	$options  = array();

	if( $result = mysqli_query($conn, $sql) ){
		$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
		foreach ($rows as $row) {
			$options[] = $row['option'];
		}
	}

	mysqli_close($conn);

	return $options;
}


/* Delete an option
----------------------------*/
function delete_option( $option ){
	$conn     = conectare_la_db();
	$option   = strip_tags( maybe_serialize( $option ) );
	$sql      = "DELETE FROM options WHERE option = '$option'";
	
	if( $result = mysqli_query($conn, $sql) ){
		return true;
	}
	else{
		return false;
	}

	mysqli_close($conn);
}

/* Source: https://stackoverflow.com/a/43699922/1050262
------------------------------------------------------------*/
/**
 * Get the base URL of the current page. For example, if the current page URL is
 * "https://example.com/dir/example.php?whatever" this function will return
 * "https://example.com/dir/" .
 *
 * @return string The base URL of the current page.
 */
function get_site_url() {

	$protocol = filter_input(INPUT_SERVER, 'HTTPS');
	if (empty($protocol)) {
		$protocol = "http";
	}

	$host = filter_input(INPUT_SERVER, 'HTTP_HOST');

	$request_uri_full = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$last_slash_pos = strrpos($request_uri_full, "/");
	if ($last_slash_pos === FALSE) {
		$request_uri_sub = $request_uri_full;
	}
	else {
		$request_uri_sub = substr($request_uri_full, 0, $last_slash_pos + 1);
	}

	return $protocol . "://" . $host . $request_uri_sub;

}


/*
-------------------------------------------------------------------------------
Forms
-------------------------------------------------------------------------------
*/

function form_fields(){
	return array(

		'Plugin config',

		'plugin_id' => array( 
			'label' => 'Plugin ID',
			'type' => 'text',
			'grid' => 'col-sm-3',
		),

		'uppercase_prefix' => array( 
			'label' => 'UPPERCASE PREFIX',
			'type' => 'text',
			'grid' => 'col-sm-3',
		),

		'lowercase_prefix' => array( 
			'label' => 'lowercase prefix',
			'type' => 'text',
			'grid' => 'col-sm-3',
		),

		'namespace' => array( 
			'label' => 'Namespace',
			'type' => 'text',
			'grid' => 'col-sm-3',
			'clear_row' => true,
		),

		'Plugin header info',

		'plugin_name' => array( 
			'label' => 'Plugin Name',	 
			'type' => 'text',
			'grid' => 'col-sm-4',
		),

		'plugin_uri' => array( 
			'label' => 'Plugin URI',	 
			'type' => 'text',
			'grid' => 'col-sm-4',
			'clear_row' => true,
			'default' => 'http://zerowp.com/',
		),

		'author' => array( 
			'label' => 'Author',
			'type' => 'text',
			'grid' => 'col-sm-4',
			'default' => 'ZeroWP Team',
		),

		'author_uri' => array( 
			'label' => 'Author URI',
			'type' => 'text',
			'grid' => 'col-sm-4',
			'default' => 'http://zerowp.com/',
		),

		'version' => array( 
			'label' => 'Version',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => '1.0',
		),

		'min_php_version' => array( 
			'label' => 'Min PHP version',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => '5.3',
		),

		'description' => array( 
			'label' => 'Short Description',
			'type' => 'text',
			'grid' => 'col-sm-12',
			'clear_row' => true,
		),

		'Readme.txt',

		'contributors' => array( 
			'label' => 'Contributors',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'default' => '_smartik_',
		),

		'donate_link' => array( 
			'label' => 'Donate link',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'default' => 'https://paypal.me/zerowp',
			'clear_row' => true,
		),

		'tags' => array( 
			'label' => 'Tags',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'default' => '',
		),

		'requires_at_least' => array( 
			'label' => 'Requires at least',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => '4.7',
		),
		'tested_up_to' => array( 
			'label' => 'Tested up to',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => '4.8',
		),
		'stable_tag' => array( 
			'label' => 'Stable tag',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => '1.0',
			'clear_row' => true,
		),

		'license' => array( 
			'label' => 'License',
			'type' => 'text',
			'grid' => 'col-sm-2',
			'default' => 'GPL-2.0+',
		),

		'license_uri' => array( 
			'label' => 'License URI',
			'type' => 'text',
			'grid' => 'col-sm-6',
			'default' => 'http://www.gnu.org/licenses/gpl-2.0.txt',
			'clear_row' => true,
		),

		'long_description' => array( 
			'label' => 'Long Description',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'clear_row' => true,
		),

		'instalation' => array( 
			'label' => 'Instalation',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'default' => '',
			'clear_row' => true,
		),

		'faq' => array( 
			'label' => 'Frequently Asked Questions',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'default' => '',
			'clear_row' => true,
		),

		'screenshots' => array( 
			'label' => 'Screenshots',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'default' => '',
			'clear_row' => true,
		),

		'changelog' => array( 
			'label' => 'Changelog',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'default' => '',
			'clear_row' => true,
		),

		'upgrade_notices' => array( 
			'label' => 'Upgrade Notices',
			'type' => 'textarea',
			'grid' => 'col-sm-12',
			'default' => '',
			'clear_row' => true,
		),

		'generate_download' => array( 
			'label' => 'Generate Download',
			'type' => 'nice_selector',
			'grid' => 'col-sm-12',
			'default' => 'no',
			'options' => [
				'yes' => 'Yes',
				'no' => 'No',
			],
			'clear_row' => true,
		),




	);
}

function form_label( $id ){
	$fields = form_fields();

	if( isset($fields[ $id ]) && !empty($fields[ $id ]['label']) ){
		return $fields[ $id ]['label'];
	}
	else{
		return $id;
	}
}

function form_value_label( $field_id, $saved_value ){
	$label = $saved_value;

	$fields = form_fields();

	$type = $fields[ $field_id ]['type'];
	$options = !empty($fields[ $field_id ]['options']) ? $fields[ $field_id ]['options'] : false;
	
	if( in_array( $type, array( 'select', 'radio', 'nice_selector' ) ) && is_array( $options ) ){
		$label = $options[ $saved_value ];
		if( is_array( $label ) ){
			$label = $label['label'];
		}
	}

	return $label;
}

function process_form(){
	if( !empty($_POST['form_sent']) ) :
		
		$data = $_POST;


		/* Escape data
		-------------------*/
		$data['plugin_id'] = htmlspecialchars($data['plugin_id']);
		$data['plugin_name'] = htmlspecialchars($data['plugin_name']);


		/* Verificare date
		-----------------------*/
		$errors = array();

		$fields = array( 
			'plugin_id',
			'plugin_name',
			'uppercase_prefix',
			'lowercase_prefix',
			'namespace',
			'version',
			'min_php_version',
			'description',
			'requires_at_least',
			'tested_up_to',
			'stable_tag',
		);

		foreach ($fields as $field) {
			if( empty( $data[ $field ] ) ){
				$errors[ $field ] = '"' . form_label( $field ) . '" is required.';
			}
		}


		if( !empty($errors) )
			return $errors;

		unset( $data['form_sent'] );

		if( update_option( $data['plugin_id'], $data ) ) {
			do_action( 'form_sent', $data['plugin_id'] );
		}
		else{
			$errors[] = 'Can\'t enter data in DB!';
		}

		/* Return response
		-----------------------*/
		if( !empty($errors) ){
			return $errors;
		}
		else{
			return true; 
		}

	endif;
}

//------------------------------------//--------------------------------------//

function get_header(){
	include "header.php";
}

//------------------------------------//--------------------------------------//

function get_footer(){
	include "footer.php";
}


/**
 * Copy a file, or recursively copy a folder and its contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @param       int      $permissions New folder creation permissions
 * @return      bool     Returns true on success, false on failure
 */
function xcopy($source, $dest, $permissions = 0755){
	// Check for symlinks
	if (is_link($source)) {
		return symlink(readlink($source), $dest);
	}

	// Simple copy for a file
	if (is_file($source)) {
		return copy($source, $dest);
	}

	// Make destination directory
	if (!is_dir($dest)) {
		mkdir($dest, $permissions);
	}

	// Loop through the folder
	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		// Skip pointers
		if ($entry == '.' || $entry == '..') {
			continue;
		}

		// Deep copy directories
		xcopy("$source/$entry", "$dest/$entry", $permissions);
	}

	// Clean up
	$dir->close();
	return true;
}

function glob_recursive($pattern, $flags = 0){
	$messages = array();

	$files = glob( $pattern, $flags );
	
	foreach ( glob( dirname( $pattern ) .'/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $dir ) {
		$files = array_merge( $files, glob_recursive( 
			$dir.'/'.basename( $pattern ), 
			$flags
		));
	}

	return $files;
}

function search_n_replace($dir, $find, $replace, $flags = 0){
	$messages = array();

	$files = glob_recursive( $dir .'/*.php' );

	foreach ( $files as $file ) {
		$temp = file_get_contents( $file );
		$temp = str_replace( $find, $replace, $temp );

		if( ! file_put_contents( $file, $temp ) ){
			$messages[] = "There was a problem (permissions?) replacing the file " . $file;
		}
		else{
			$messages[] = "File " . $file . " replaced!";
		}
	}

	return $messages;
}

function remove_dir($dir) { 
	$files = array_diff( scandir( $dir ), array( '.', '..' ) ); 
	
	foreach ( $files as $file ) { 
		if( is_dir( "$dir/$file" ) ){
			remove_dir( "$dir/$file" ); 
		}
		else{
			unlink( "$dir/$file" );
		}
	} 
	
	return rmdir($dir); 
}

function zip_dir( $folder_path, $zip_name ){
	$folder_path = realpath( $folder_path );

	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open( $zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE );

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	// $files = glob_recursive( '/*' );
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($folder_path),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file){
		// Skip directories (they would be added automatically)
		if (!$file->isDir()){
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($folder_path) + 1);

			// Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}

	// Zip archive will be created only after closing object
	$zip->close();
	return true;
}

add_action( 'form_sent', function( $option_id ){
	$data = get_option( $option_id );
	$dir = 'generated/' . $option_id;

	$messages = array();

	/* Remove the directory if it already exists
	-------------------------------------------------*/
	if( is_dir( $dir ) ){
		if( remove_dir( $dir ) ){
			$messages[] = "\"$dir\" has been removed.";
		}
	}

	/* Copy the directory
	-------------------------*/
	if( xcopy( '../wordpress/wp-content/plugins/zerowp-plugin-boilerplate', $dir )) {
		$messages[] = "\"$dir\" has been created.";
	}

	/* Rename main plugin file
	-------------------------------*/
	if( rename( $dir .'/zerowp-plugin-boilerplate.php', $dir .'/'. $option_id .'.php' ) ){
		$messages[] = '"'. $dir .'/zerowp-plugin-boilerplate.php" has been renamed to "'. $dir .'/'. $option_id .'.php"';
	}

	$replacement_messages = search_n_replace( 
		$dir, 
		array( 
			'{TEXT_DOMAIN}',
			'{PLUGIN_NAME}',
			'{PLUGIN_URI}',
			'{AUTHOR}',
			'{AUTHOR_URI}',
			'{VERSION}',
			'{MIN_PHP_VERSION}',
			'{DESCRIPTION}',
			'{NAMESPACE}',
			'{LICENSE}',
			'{LICENSE_URI}',
			'ZPB',
			'zpb',
		), 
		array( 
			$option_id,
			$data['plugin_name'],
			$data['plugin_uri'],
			$data['author'],
			$data['author_uri'],
			$data['version'],
			$data['min_php_version'],
			$data['description'],
			$data['namespace'],
			$data['license'],
			$data['license_uri'],
			$data['uppercase_prefix'],
			$data['lowercase_prefix'],
		)
	);

	$messages = $messages+$replacement_messages;

	unset( $data['form_sent'] );
	unset( $data['generate_download'] );

	$data = array_merge(
		array( 'GENERATOR' => 'ZeroWP WordPress Plugin Boilerplate', 'GENERATOR_SOURCE' => 'https://github.com/ZeroWP/Plugin-Boilerplate' ),
		$data
	);

	if( file_put_contents( $dir .'/config.json', json_encode( $data, JSON_PRETTY_PRINT ) ) ){
		$messages[] = '"config.json" has been created!';
	}
	else{
		$messages[] = 'Failed to create "config.json"!';
	}

	if( create_readme( $data, $dir .'/readme.txt' ) ){
		$messages[] = '"readme.txt" has been created!';
	}
	else{
		$messages[] = 'Failed to create "readme.txt"!';
	}

	$zip_filename = 'generated/' . $option_id .'.zip';

	if( file_exists($zip_filename) ){
		if( unlink( $zip_filename ) ){
			$messages[] = '"'. $zip_filename .'" has been removed!';
		}
	}

	if( zip_dir( $dir, $zip_filename ) ){
		$messages[] = '"'. $zip_filename .'" has been created!';
	}

	echo '<div class="alert alert-info">';
		echo '<ol>';
			foreach ($messages as $message) {
				echo "<li>$message</li>";
			}
		echo '</ol>';
	echo '</div>';

});

add_action( 'download_url', function(){
	if( !empty( $_POST['form_sent'] ) && !empty( $_POST['plugin_id'] ) && 'yes' == $_POST['generate_download'] ){
		$zip_filename = 'generated/' . $_POST['plugin_id'] .'.zip';
		if( file_exists( realpath( $zip_filename ) ) ){
			echo '<meta http-equiv="refresh" content="1;url='. get_site_url() . $zip_filename .'"/>';
		}
	}
});

function delete_plugin_from_db(){
	if( !empty($_GET['delete_plugin']) ){
		delete_option( $_GET['delete_plugin'] );
	}
}
delete_plugin_from_db();

function create_readme( $data, $file_name ){

	$text = '';
	$text .= '=== Plugin Name ===' . PHP_EOL;

	if( !empty($data['contributors']) ){
		$text .= 'Contributors: ' . $data['contributors'] . PHP_EOL;
	}

	if( !empty($data['donate_link']) ){
		$text .= 'Donate link: '. $data['donate_link'] . PHP_EOL;
	}

	if( !empty($data['tags']) ){
		$text .= 'Tags: '. $data['tags'] . PHP_EOL;
	}

	if( !empty($data['requires_at_least']) ){
		$text .= 'Requires at least: '. $data['requires_at_least'] . PHP_EOL;
	}

	if( !empty($data['tested_up_to']) ){
		$text .= 'Tested up to: '. $data['tested_up_to'] . PHP_EOL;
	}

	if( !empty($data['stable_tag']) ){
		$text .= 'Stable tag: '. $data['stable_tag'] . PHP_EOL;
	}

	if( !empty($data['license']) ){
		$text .= 'License: '. $data['license'] . PHP_EOL;
	}

	if( !empty($data['license_uri']) ){
		$text .= 'License URI: '. $data['license_uri'] . PHP_EOL;
	}

	$text .= PHP_EOL;

	if( !empty($data['description']) ){
		$text .= $data['description'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['long_description']) ){
		$text .= '== Description ==' . PHP_EOL;
		$text .= $data['long_description'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['instalation']) ){
		$text .= '== Installation ==' . PHP_EOL;
		$text .= $data['instalation'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['faq']) ){
		$text .= '== Frequently Asked Questions ==' . PHP_EOL;
		$text .= $data['faq'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['screenshots']) ){
		$text .= '== Screenshots ==' . PHP_EOL;
		$text .= $data['screenshots'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['changelog']) ){
		$text .= '== Changelog ==' . PHP_EOL;
		$text .= $data['changelog'] . PHP_EOL . PHP_EOL;
	}

	if( !empty($data['upgrade_notices']) ){
		$text .= '== Upgrade Notice ==' . PHP_EOL;
		$text .= $data['upgrade_notices'] . PHP_EOL;
	}

	return file_put_contents( $file_name, $text );

}