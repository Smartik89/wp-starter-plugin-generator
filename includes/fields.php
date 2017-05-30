<?php 

class Field{

	//Field 'text'
	public static function text( $name, $value = '', $type = 'text'  ){
		return '<input name="'. $name .'" type="'. $type .'" value="'. self::get_value( $name, $value ) .'" class="form-control" />';
	}

	//Field 'number'
	public static function number( $name, $value = '', $settings = array()  ){
		$min = isset($settings['min']) ? ' min="'. $settings['min'] .'"' : '';
		$max = isset($settings['max']) ? ' max="'. $settings['max'] .'"' : '';
		$step = isset($settings['step']) ? ' step="'. $settings['step'] .'"' : '';

		return '<input name="'. $name .'" type="number" value="'. self::get_value( $name, $value ) .'" class="form-control"'. $min . $max . $step .' />';
	}

	//Field 'textarea'
	public static function textarea( $name, $value = '', $settings = array() ){
		$rows = ( !empty($settings['rows']) ) ? intval( $settings['rows'] ) : 5;
		$class = ( !empty($settings['class']) ) ? ' '. $settings['class'] : '';
		return '<textarea class="form-control'. $class .'" name="'. $name .'" rows="'. $rows .'">'. htmlspecialchars( self::get_value( $name, $value ) ) .'</textarea>';
	}

	//Field 'select'
	public static function select( $name, $value = '', $settings = false ){
		$options = ! empty($settings['options']) ? $settings['options'] : $settings;
		$options = is_callable($options) ? call_user_func($options) : $options;

		$out = '';

		if( is_array($options) ){

			$out .= '<select class="form-control" name="'. $name .'">';

				foreach ($options as $key => $v) {
					$user_value =  self::get_value( $name, $value );

					if( $key == $user_value ){
						$selected = ' selected="selected"';
					}
					else{
						$selected = '';
					}

					$out .= '<option value="'. htmlspecialchars( $key ) .'"'. $selected .'>'. $v .'</option>';
				}

			$out .= '</select>';

		}


		return $out;

	}

	//Field 'radio'
	public static function radio( $name, $value = '', $settings = false ){
		$user_value =  self::get_value( $name, $value );
		$options = ! empty($settings['options']) ? $settings['options'] : $settings;
		$options = is_callable($options) ? call_user_func($options) : $options;

		$out = '';

		if( is_array($options) ){

			$out .= '<div>';
			foreach ($options as $key => $v) {

				if( empty($user_value) ){
					$user_value = $key;
				}

				if( $key == $user_value ){
					$checked = ' checked="checked"';
				}
				else{
					$checked = '';
				}

				$out .= '<label class="radio-inline"><input type="radio" name="'. $name .'" value="'. htmlspecialchars( $key ) .'"'. $checked .'>'. $v .'</label>';
			}
			$out .= '</div>';

		}

		return $out;
	}

	//Field 'nice_selector'
	public static function nice_selector( $name, $value = '', $settings = false ){
		$user_value =  self::get_value( $name, $value );

		$size = !empty($settings['size']) ? ' '. $settings['size'] : '';
		$options = ! empty($settings['options']) ? $settings['options'] : $settings;
		$options = is_callable($options) ? call_user_func($options) : $options;

		$out = '';
		if( is_array($options) ){

			$out .= '<div class="form-nice-selector'. $size .'">';
				foreach ($options as $key => $v) {

					if( empty($user_value) ){
						$user_value = $key;
					}

					if( $key == $user_value ){
						$active = ' class="active"';
					}
					else{
						$active = '';
					}

					if( is_array( $v ) ){
						$label = $v['label'];
						$data_img = !empty($v['img']) ? ' data-img="'. $v['img'] .'"' : '';
						$data_color = !empty($v['color']) ? ' data-color="'. $v['color'] .'"' : '';
					}
					else{
						$label = $v;
						$data_img = '';
						$data_color = '';
					}

					$out .= '<span'. $data_img . $data_color .' data-value="'. htmlspecialchars( $key ) .'"'. $active .'>'. $label .'</span>';
				}
				$out .= '<input type="hidden" name="'. $name .'" value="">';
			$out .= '</div>';

		}

		return $out;
	}

	public static function persoane_admin_la_volan( $name, $value = array()  ){
		$value = self::get_value( $name, $value );

		$person_nume = array();

		$person_nume[1] = !empty($value['person_1']['nume']) ? $value['person_1']['nume'] : '';
		$person_cod_personal[1] = !empty($value['person_1']['cod_personal']) ? $value['person_1']['cod_personal'] : '';
		
		$person_nume[2] = !empty($value['person_2']['nume']) ? $value['person_2']['nume'] : '';
		$person_cod_personal[2] = !empty($value['person_2']['cod_personal']) ? $value['person_2']['cod_personal'] : '';
		
		$person_nume[3] = !empty($value['person_3']['nume']) ? $value['person_3']['nume'] : '';
		$person_cod_personal[3] = !empty($value['person_3']['cod_personal']) ? $value['person_3']['cod_personal'] : '';

		$output = '';

		$output .= '<div id="'. $name .'">';
		for ($i=1; $i <= 3; $i++) { 
			$output .= '<div class="row persoane_admin_la_volan">';
				$output .= '<div class="col-xs-6">';
					$output .= '<input name="'. $name .'[person_'. $i .'][nume]" type="text" value="'. $person_nume[$i] .'" class="form-control ignore-changes" placeholder="Nume Prenume" />';
				$output .= '</div>';
				$output .= '<div class="col-xs-6">';
					$output .= '<input name="'. $name .'[person_'. $i .'][cod_personal]" type="text" value="'. $person_cod_personal[$i] .'" class="form-control ignore-changes" placeholder="Cod personal" />';
				$output .= '</div>';
			$output .= '</div>';
		}
		$output .= '</div>';

		return $output;
	}

	public static function get_value( $name, $default_value ){
		if( isset( $_POST[ $name ] ) ){
			return !empty( $_POST[ $name ] ) ? $_POST[ $name ] : '';
		}
		else{
			return $default_value;
		}
	}

}