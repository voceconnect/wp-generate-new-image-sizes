WP Generate New Image Sizes
==================

Contributors: kevinlangleyjr, voceplatforms  
Tags: image, sizes, resize  
Requires at least: 3.3  
Tested up to: 3.7.1  
Stable tag: 1.0  
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

**1.0**  
*Initial release.*