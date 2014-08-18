<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );

// Increase loop count
$woocommerce_loop['loop']++;

// Return Bootstrap column based on WooCommerce column setting
if ( $woocommerce_loop['columns'] == 1 ) $cols = 12;
if ( $woocommerce_loop['columns'] == 2 ) $cols = 6;
if ( $woocommerce_loop['columns'] == 3 ) $cols = 4;
if ( $woocommerce_loop['columns'] == 4 ) $cols = 3;
if ( $woocommerce_loop['columns'] == 6 ) $cols = 2;
?>
<div class="product-category col-sm-<?= $cols; ?> text-center">

	<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>" class="notextdec">

		<?php
			/**
			 * woocommerce_before_subcategory_title hook
			 *
			 * @hooked woocommerce_subcategory_thumbnail - 10
			 */
			do_action( 'woocommerce_before_subcategory_title', $category );
		?>

		<h3>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'woocommerce_subcategory_count_html', ' <span class="label label-default">' . $category->count . '</span>', $category );
			?>
		</h3>
		
		<a href="#" class="btn btn-primary space-top20">
			<i class="fa fa-cubes"></i> View Products
		</a>

		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'woocommerce_after_subcategory', $category ); ?>

</div>

<?php if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 ) echo '</div><div class="products row space-bottom20">'; ?>