<?php
/**
 * Template Name: Test Page
 * Template part for Test Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<style>
    .uicore-tb-pagetitle{
        display: none !important;
    }
</style>

<main id="primary" class="site-main">
    <div class="spacer-80"></div>

    <div class="container">

        <?php print do_shortcode("[shop_featured]");?>
        
        
    <?php the_content(); ?>
    

    </div>

    <div class="spacer-80"></div>
</main>

<?php
get_footer();