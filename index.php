<?php
/*
 * Plugin Name:		Require & Limit Categories, Tags, Featured Image and taxonomies
 * Description:		Force dashboard users to select chosen fields during publishing, also limit the maximum chosen fields.
 * Text Domain:		require-taxonomy-image-category-tag
 * Domain Path:		/languages
 * Version:		1.30
 * WordPress URI:	https://wordpress.org/plugins/require-taxonomy-image-category-tag/
 * Plugin URI:		https://puvox.software/software/wordpress-plugins/?plugin=require-taxonomy-image-category-tag
 * Contributors: 	puvoxsoftware,ttodua
 * Author:		Puvox.software
 * Author URI:		https://puvox.software/
 * Donate Link:		https://paypal.me/Puvox
 * License:		GPL-3.0
 * License URI:		https://www.gnu.org/licenses/gpl-3.0.html
 
 * @copyright:		Puvox.software
*/


namespace RequireTaxonomyImageCategoryTag
{
  if (!defined('ABSPATH')) exit;
  require_once( __DIR__."/library.php" );
  require_once( __DIR__."/library_wp.php" );
  
  class PluginClass extends \Puvox\wp_plugin
  {

	public function declare_settings()
	{
		$this->initial_static_options	= 
		[
			'has_pro_version'        => 0, 
            'show_opts'              => true, 
            'show_rating_message'    => true, 
            'show_donation_footer'   => true, 
            'show_donation_popup'    => true, 
            'menu_pages'             => [
                'first' =>[
                    'title'           => 'Require Limits for post fields', 
                    'default_managed' => 'singlesite',            // network | singlesite
                    'required_role'   => 'install_plugins',
                    'level'           => 'submenu', 
                    'page_title'      => 'Require & Limit Categories, Tags, Featured Image and taxonomies',
                    'tabs'            => [],
                ],
            ]
		];
		
		$this->initial_user_options	= 
		[
			'post_types'	=> 'post',
			'taxonomies'	=> 'category,post_tag',
			'taxonomy_limits' => [ 'category'=>2,  'post_tag'=>5],
			'featured_image'=> true,
		];
	} 
	
	public function __construct_my()
	{
		add_action( 'admin_enqueue_scripts', 	[$this, 'admin_enqueue_scripts_action'] );
	}

	// ============================================================================================================== //
	// ============================================================================================================== //
 
	public function admin_enqueue_scripts_action( $page ) 
	{
		if ( ! in_array( $page, ['post.php', 'post-new.php'] ) )    return;

		global $post, $post_type;
		if ( ! in_array($post_type, $this->helpers->string_to_array($this->opts['post_types'], ',') ) )  return;
	
		$chosen_taxonomies=[];
		$existing_taxonomies = get_object_taxonomies( $post_type );
		foreach ( $this->helpers->string_to_array($this->opts['taxonomies'], ',') as $taxonomy) 
		{
			if ( taxonomy_exists( $taxonomy ) && in_array( $taxonomy, $existing_taxonomies ) ) 
			{
				$chosen_taxonomies[ $taxonomy ]['label'] 		= strtolower( get_taxonomy_labels( get_taxonomy( $taxonomy ) )->name );
				$chosen_taxonomies[ $taxonomy ]['hierarchical'] = ( is_taxonomy_hierarchical( $taxonomy ) );
				$chosen_taxonomies[ $taxonomy ]['has_items'] 	= count( wp_get_post_terms( $post->ID, $taxonomy ) ) ;
			}
		}

		$js_object = apply_filters( "rtict_javascript_object",  [
			'chosen_taxonomies'	=> $chosen_taxonomies,
			'required_message'  => __('Please add at least one %NAME%', 'require-taxonomy-image-category-tag'),
			'gutenberg'      	=> $this->helpers->is_gutenberg_page() ? "yes" : "no",
			'fetured_image'		=> [ "required" => $this->opts['featured_image'],  "has_image" => (has_post_thumbnail($post->ID) ? "yes" : "no") ],
			'limits'			=> $this->opts['taxonomy_limits'],
			'limit_message'		=> __('You have added more than allowed: %NAME%', 'require-taxonomy-image-category-tag'),
		]);

		wp_enqueue_script( $this->slug.'scripts-admin', $this->helpers->baseURL.'/assets/scripts-admin.js', ['jquery'], false, true );
		wp_localize_script( $this->slug.'scripts-admin', 'rtict_object',  $js_object );
	}

	
	// =================================== Options page ================================ //
	public function opts_page_output() 
	{
		$this->settings_page_part("start", 'first');
		?>

		<style>
		</style>

		<?php if ($this->active_tab=="Options") 
		{ ?>

			<?php 
			//if form updated
			if( $this->checkSubmission() ) 
			{
				$this->opts['post_types']  	= sanitize_text_field( $_POST[ $this->plugin_slug ]['post_types'] );
				$this->opts['taxonomies']  	= sanitize_text_field( $_POST[ $this->plugin_slug ]['taxonomies'] );
				$this->opts['taxonomy_limits']= $this->helpers->array_map_recursive('sanitize_text_field', $this->helpers->arrayFieldsResort($_POST[ $this->plugin_slug ]['taxonomy_limits']) );
				$this->opts['featured_image']= !empty( $_POST[ $this->plugin_slug ]['featured_image'] );
				$this->update_opts(); 
			}
			?> 

			<form class="mainForm" method="post" action="">

			<table class="form-table">
			<tbody>
				<tr class="def">
					<td scope="row">
						<label for="<?php echo $this->plugin_slug;?>_post_types">
							<?php _e('Insert desired post types, where these requirements will be used (comma-delimited list)', 'require-taxonomy-image-category-tag');?>
						</label>
					</td>
					<td>
						<input type="text" id="<?php echo $this->plugin_slug;?>_post_types" name="<?php echo $this->plugin_slug;?>[post_types]" value="<?php echo $this->opts['post_types'];?>" class="regular-text" />
						<p class="description">
							<?php if ( class_exists( 'WooCommerce' ) ) _e('Seems you use WooCommerce. You might want to add <code>product</code> post-type too in the field', 'require-taxonomy-image-category-tag') ?>
						</p>
					</td>
				</tr>
				<tr class="def">
					<td scope="row">
						<label for="<?php echo $this->plugin_slug;?>_taxonomies">
							<?php _e('Insert desired taxonomies, which should be required during post publish (comma-delimited list)', 'require-taxonomy-image-category-tag');?>
						</label>
					</td>
					<td>
						<input type="text" id="<?php echo $this->plugin_slug;?>_taxonomies" name="<?php echo $this->plugin_slug;?>[taxonomies]" value="<?php echo $this->opts['taxonomies'];?>" class="regular-text"  />
						<p class="description">
							<?php if ( class_exists( 'WooCommerce' ) ) _e('Seems you use WooCommerce. You might want to add <code>product_cat,product_tag</code> post-type too in the field', 'require-taxonomy-image-category-tag') ?>
						</p>
					</td>
				</tr>
				<tr class="def">
					<td scope="row">
						<label for="<?php echo $this->plugin_slug;?>_taxonomy_limits">
							<?php _e('You can set maximum allowed limit to be added to each post', 'require-taxonomy-image-category-tag');?>
						</label>
					</td>
					<td>
						<div id="rtict_maximum_limit_taxes">
							<?php
							$this->helpers->array_fields($this->opts['taxonomy_limits'],  $this->plugin_slug.'[taxonomy_limits]', true);
							?>
						</div>
					</td>
				</tr>
				<tr class="def">
					<td scope="row">
						<label for="<?php echo $this->plugin_slug;?>_featured_image">
							<?php _e('Require Featured-Image to be set during publish', 'require-taxonomy-image-category-tag');?>
						</label>
					</td>
					<td>
						<input type="checkbox" id="<?php echo $this->plugin_slug;?>_featured_image" name="<?php echo $this->plugin_slug;?>[featured_image]" value="1" <?php checked($this->opts['featured_image']);?> />
					</td>
				</tr>
			</tbody>
			</table>

			<?php $this->nonceSubmit(); ?>

			</form>

			<div>
				<p>
				<?php _e('For programmatic hook, use:', 'require-taxonomy-image-category-tag');?>
<code><pre>// To change the `$args` passed to javascript handler  
add_filter("rtict_javascript_object", "your_func");
function your_func($args) {
	...
}

// to change the javascript handler's error-output callback, define this in global JS scope:
function rtict_error_handler(args) {
	// ... console.log(args);
}
</pre></code>
				</p>
			</div>
		<?php  
		}

		
		$this->settings_page_part("end", '');
	}




  } // End Of Class

  $GLOBALS[__NAMESPACE__] = new PluginClass();

} // End Of NameSpace

?>