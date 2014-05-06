WP Generate New Image Sizes
==================

Contributors: kevinlangleyjr, voceplatforms  
Tags: image, sizes, resize  
Requires at least: 3.3  
Tested up to: 3.9  
Stable tag: 1.3  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description
Generates newly sized images upon request of new registered image size

## Installation

### As standard plugin:
> See [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

### As theme or plugin dependency:
> After dropping the plugin into the containing theme or plugin, add the following:

```php
if( ! class_exists( 'WP_Generate_New_Image_Sizes' ) ) {
	require_once( $path_to_plugin . '/wp-generate-new-image-sizes.php' );
}
```

## Usage
Upon request of image with a newly registered size, the plugin will generate a new image file with that specific size

# Changelog

**1.3**  
*Using our own `_load_image_to_edit_path` if the file does not exist.*

**1.2**  
*Making directory for file if it does not exist - related to the fix for 1.1.*

**1.1**  
*Fixing remote file issue with 3.9.*

**1.0**  
*Initial release.*