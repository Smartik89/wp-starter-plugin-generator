<?php
require_once dirname(__FILE__) . "/functions.php";

get_header();

echo '<h1 class="h1-title">Plugin Generator</h1>';

$errors = process_form();

if( !empty( $errors ) && is_array( $errors ) ){
	echo '<div class="alert alert-danger">';
	echo 'Please fix the following errors';
	echo '</div>';
}

?>
<div class="row">
<div class="col-xs-8">
<form method="post">

<?php 
$form = form_fields();

echo '<div class="row">';
foreach ($form as $field_id => $field) {
	$grid = !empty($field['grid']) ? ' '. $field['grid'] : ' col-xs-12';
	
	// Is field
	if( is_array( $field ) ){

		$show_if = '';
		if( !empty($field['show_if']) ){
			$conditions = (array) $field['show_if'];
			foreach ($conditions as $condition) {
				$condition = explode('::', $condition);
				$show_if .= ' data-show-if-'. htmlspecialchars( $condition[0] ) .'="' . htmlspecialchars( $condition[1] ) .'"';
			}
		}


		echo '<div class="form-group'. $grid .'"'. $show_if .'>';

		if( !empty( $field[ 'label' ] ) ){
			echo '<label>';
			echo $field[ 'label' ];
			
			if( !empty( $field[ 'img_tip' ] ) ){
				echo '&nbsp;<span 
					class="glyphicon glyphicon-question-sign" 
					data-toggle="popover" 
					title="'. htmlspecialchars( $field[ 'label' ] ) .'" 
					data-img-tip="'. htmlspecialchars( $field_id ) .'"
				></span>';
			}
			
			echo '</label>';
		}

		if( !empty($_POST['form_sent']) && isset($_POST[ $field_id ]) ){
			$value = $_POST[ $field_id ];
		}
		elseif( isset($_GET['edit_plugin']) ){
			$option = get_option( htmlspecialchars( $_GET['edit_plugin'] ) );
			$value = isset( $option[ $field_id ] ) ? $option[ $field_id ] : '';
		}
		else{
			$value = isset( $field['default'] ) ? $field['default'] : '';
		}
		
		switch ($field[ 'type' ]) {
			
			case 'text';
				echo Field::text( $field_id, $value );
				break;

			case 'textarea';
				echo Field::textarea( $field_id, $value, $field );
				break;

			case 'number':
					echo Field::number( $field_id, $value, $field );
				break;
			
			case 'select':
					echo Field::select( $field_id, $value, $field);
				break;
			
			case 'radio':
					echo Field::radio( $field_id, $value, $field);
				break;
			
			case 'nice_selector':
					echo Field::nice_selector( $field_id, $value, $field);
				break;
			
			case 'persoane_admin_la_volan':
					echo Field::persoane_admin_la_volan( $field_id, $value);
				break;
			
			default:
				echo '<div class="alert alert-danger">Invalid field type!</div>';
				break;

		} // switch

		if( !empty($errors[ $field_id ]) ){
			echo '<div class="text-danger">' . $errors[ $field_id ] .'</div>';
		}

		echo '</div>';

	}

	// .. is section
	elseif( is_string( $field ) ){
		echo '<h3 class="form-section'. $grid .'"><span>'. $field .'</span></h3>';
	}
	
	if( !empty($field['clear_row']) ){
		echo '</div><div class="row">';
	}

} // foreach

echo '</div>'; //row;

?>

	<div class="form-group">
		<hr>
		<input type="hidden" name="form_sent" value="1" />
		<input type="submit" value="Trimite" class="btn btn-primary" />
	</div>

</form>

</div> <!-- .col-xs-8 -->

<div class="col-xs-4">
	<h3 class="form-section"><span>Generated</span></h3>
	<?php 
	$all_options = get_all_options();

	foreach ($all_options as $option) {
		$opt = get_option( $option );
		if( !empty($opt) ){
			echo '<div class="plugin-single-link">
				<a href="'. add_query_arg( array( 'edit_plugin' => $opt['plugin_id'] ), get_site_url() ) .'" class="plugin-link">
					<div class="plugin-name">'. $opt['plugin_name'] .'</div>
					<div class="plugin-id">'. $opt['plugin_id'] .'</div>
				</a>
				<a href="'. add_query_arg( array( 'delete_plugin' => $opt['plugin_id'] ), get_site_url() ) .'" title="Delete" class="the-icon">
				<span class="glyphicon glyphicon-trash"></span>
				</a>
			</div>';
		}
	}

	?>
</div> <!-- .col-xs-4 -->
</div> <!-- .row -->

<?php 
get_footer();