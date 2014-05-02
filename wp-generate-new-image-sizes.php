<?php
/*
Plugin Name: WP Generate New Image Sizes
Plugin URI: http://voceconnect.com/
Description: Generates new images added to the theme
Version: 1.1
Author: Kevin Langley
Author URI: http://voceconnect.com/
*/

class WP_Generate_New_Image_Sizes {
	public static function init(){
		add_filter('image_downsize', array(__CLASS__, 'image_downsize'), 3, 10);
	}

	private static function _generate_image_size( $attachment_id, $size ) {
		$attachment = get_post( $attachment_id );

		$file = self::_load_image_to_edit_path( $attachment_id );

		$size_data = self::get_image_size( $size );

		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( !$metadata || !is_array( $metadata ) ) {
			if( $attached_file = get_post_meta( $attachment_id, '_wp_attached_file', true ) ){
				$upload_dir = wp_upload_dir();
				if( !function_exists('wp_generate_attachment_metadata') )
					require_once(ABSPATH . 'wp-admin/includes/image.php');

				$metadata = wp_generate_attachment_metadata( $attachment_id, $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $attached_file );
				if( !$metadata ){
					return false;
				}
			} else {
				return false;
			}
		}

		if ( $size && !empty($size_data) && $file && preg_match( '!^image/!', get_post_mime_type( $attachment ) ) && self::file_is_displayable_image( $file ) ) {
			if(function_exists('wp_get_image_editor')){
				$editor = wp_get_image_editor($file);
				if( !is_wp_error( $editor ) && $editor ){
					$new_size = $editor->multi_resize( array( $size => $size_data ) );
					if ( empty( $metadata['sizes'] ) ) {
						$metadata['sizes'] = $new_size;
					} else {
						$metadata['sizes'] = array_merge( $metadata['sizes'], $new_size );
					}
				}
			} else {
				extract($size_data);
				$file_path = image_resize( $file, $width, $height, $crop );
				if(!is_wp_error($file_path) && $file_path){
					$file = basename($file_path);
					$metadata['sizes'][$size] = array('file' => $file, 'width' => $width, 'height' => $height);
				}

			}
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	public static function get_image_size( $image_size ) {
		global $_wp_additional_image_sizes;
		if ( empty( $_wp_additional_image_sizes[ $image_size ] ) )
			return null;

		return $_wp_additional_image_sizes[ $image_size ];
	}

	public static function image_size_exists( $image_size ) {
		return (bool) self::get_image_size( $image_size );
	}

	private static function _load_image_to_edit_path( $attachment_id, $size = 'full' ) {
		if(function_exists('_load_image_to_edit_path'))
			return _load_image_to_edit_path($attachment_id, $size);

		$filepath = get_attached_file( $attachment_id );

		if ( $filepath && file_exists( $filepath ) ) {
			if ( 'full' != $size && ( $data = image_get_intermediate_size( $attachment_id, $size ) ) ) {
				$filepath = apply_filters( 'load_image_to_edit_filesystempath', path_join( dirname( $filepath ), $data['file'] ), $attachment_id, $size );
			}
		} else {
			// if file doesn't exist locally fetch the file and store in the location we're expecting it to be in...
			if ( !file_exists( dirname($filepath) ) )
				mkdir(dirname($filepath), 0777, true);
			$response = wp_remote_get( wp_get_attachment_url( $attachment_id ) );
			if( wp_remote_retrieve_response_code( $response ) == 200 ){
				file_put_contents( $filepath, wp_remote_retrieve_body( $response ) );
			}
		}

		return apply_filters( 'load_image_to_edit_path', $filepath, $attachment_id, $size );
	}

	public static function file_is_displayable_image($path) {
		if(function_exists('file_is_displayable_image'))
			return file_is_displayable_image($path);

		$info = @getimagesize($path);
		if ( empty($info) )
			$result = false;
		elseif ( !in_array($info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) )	// only gif, jpeg and png images can reliably be displayed
			$result = false;
		else
			$result = true;

		return apply_filters('file_is_displayable_image', $result, $path);
	}

	public static function image_downsize($ret, $id, $size){
		if( is_array($size) || in_array($size, array('thumbnail', 'medium', 'large', 'full') ))
			return false;

		if ( !( $intermediate = image_get_intermediate_size($id, $size) ) ){
			self::_generate_image_size($id, $size);
		}

		return false;
	}
}
add_action('init', array('WP_Generate_New_Image_Sizes', 'init'));