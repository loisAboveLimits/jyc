<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load custom child-theme functionality.
 */
require_once get_stylesheet_directory() . '/inc/acf.php';
require_once get_stylesheet_directory() . '/inc/translatepress.php';

/**
 * ONE-TIME:
 * Copy all parent theme mods to UiCore child.

function uicore_child_force_parent_settings() {

    if ( get_option( 'uicore_child_settings_copied' ) ) {
        return;
    }

    $parent_theme = wp_get_theme()->parent();

    if ( ! $parent_theme ) {
        return;
    }

    $parent_stylesheet = $parent_theme->get_stylesheet();
    $child_stylesheet  = get_stylesheet();

    $parent_mods = get_option(
        'theme_mods_' . $parent_stylesheet,
        array()
    );

    if ( ! empty( $parent_mods ) ) {

        update_option(
            'theme_mods_' . $child_stylesheet,
            $parent_mods
        );

        update_option(
            'uicore_child_settings_copied',
            1
        );
    }
}

add_action(
    'after_setup_theme',
    'uicore_child_force_parent_settings',
    99
);
 */

/**
 * Enqueue UiCore Pro Child Theme assets.
 */
function uicore_child_enqueue_assets() {

    // Main child theme stylesheet: style.css
    wp_enqueue_style(
        'uicore-child',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );

    // Custom CSS
    wp_enqueue_style(
        'uicore-child-custom',
        get_stylesheet_directory_uri() . '/assets/css/custom-al.css',
        array( 'uicore-child' ),
        wp_get_theme()->get( 'Version' )
    );

    // Custom JavaScript
    wp_enqueue_script(
        'uicore-child-custom',
        get_stylesheet_directory_uri() . '/assets/js/custom-al.js',
        array( 'jquery' ),
        wp_get_theme()->get( 'Version' ),
        true
    );
}

add_action( 'wp_enqueue_scripts', 'uicore_child_enqueue_assets', 20 );

/**
 * Get Featured Image URL.
 */
function get_featured_image_url($post_id){

    $post_thumbnail_id = get_post_thumbnail_id($post_id);

    $post_thumbnail_url = get_template_directory_uri()."/img/icons/no-image.png";

    if ($post_thumbnail_id) {
        $post_thumbnail_url = wp_get_attachment_url($post_thumbnail_id);
    }

    return $post_thumbnail_url;
}

/**
 * Get Shop Featured Post (2) | Shortcode.
 */

function elementor_shop_feature_shortcode( $atts, $content = null ) {

    ob_start();

    $atts = shortcode_atts(
        array(
            'class' => '',
        ),
        $atts,
        'shop_featured'
    );

    $args = array(
        'post_type' => 'shops',
        'posts_per_page' => 2,
        'orderby' => 'date',
        'order' => 'DESC',
    );    

    $args['meta_query'] = array(
        'relation' => 'AND',
            array(
                'key' => 'featured',
                'value' => true,
                'compare' => '=',
            ),
    );  

    $query = get_posts($args); 

    $count = 0; 

    foreach($query as $row){

        //print_r($row);

        $class = ( $count % 2 === 0 ) ? 'even' : 'odd';

        $postId = $row->ID;

        if ( function_exists('trp_translate') ) {

            $title     = trp_translate($row->post_title);
            $address     = trp_translate(get_field("location", $postId));
            $hours       = trp_translate(get_field("working_hours", $postId));
            $description = trp_translate($row->post_content);
        }        
   
?>
    
    <div class="spacer-80"></div>
    
    <?php if($class == "even"){ ?>

        <div class="elementor-element e-con e-flex shop-location <?php print $class; ?>">

            <!-- COLUMN 1: CONTENT -->
            <div class="elementor-element e-con e-flex shop-location__content">

                <h3 class="elementor-heading-title shop-location__title">
                    <?php print $title;?>
                </h3>

                <p class="shop-location__address">
                    <span class="ue-grid-item-meta-data-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="19" viewBox="0 0 14 19" fill="none"><path d="M6.58397 17.8107C6.6767 17.9498 6.83283 18.0333 7 18.0333C7.16717 18.0333 7.3233 17.9498 7.41603 17.8107C8.59933 16.0358 10.3422 13.8437 11.5568 11.6144C12.5279 9.83184 13 8.31154 13 6.96668C13 3.65827 10.3084 0.966675 7 0.966675C3.6916 0.966675 1 3.65827 1 6.96668C1 8.31154 1.4721 9.83184 2.44323 11.6144C3.65687 13.842 5.4031 16.0394 6.58397 17.8107ZM7 1.96667C9.757 1.96667 12 4.20968 12 6.96668C12 8.14014 11.5678 9.50394 10.6786 11.136C9.63173 13.0576 8.1378 15.0069 7 16.6456C5.86237 15.0071 4.36833 13.0577 3.32137 11.136C2.43223 9.50394 2 8.14014 2 6.96668C2 4.20968 4.243 1.96667 7 1.96667Z" fill="#555555" stroke="#555555" stroke-width="0.2"></path><path d="M7 9.96668C8.6542 9.96668 10 8.62087 10 6.96668C10 5.31247 8.6542 3.96667 7 3.96667C5.3458 3.96667 4 5.31247 4 6.96668C4 8.62087 5.3458 9.96668 7 9.96668ZM7 4.96667C8.1028 4.96667 9 5.86387 9 6.96668C9 8.06948 8.1028 8.96668 7 8.96668C5.8972 8.96668 5 8.06948 5 6.96668C5 5.86387 5.8972 4.96667 7 4.96667Z" fill="#555555" stroke="#555555" stroke-width="0.2"></path></svg></span>

                    <span>
                        <b><?php print $address; ?></b>
                    </span>
                </p>

                <p class="shop-location__hours">
                    <b><?php print $hours; ?></b>
                </p>

                <p class="shop-location__description">
                    <?php print $description;?>
                </p>

            </div>

            <!-- COLUMN 2: IMAGE -->
            <div class="elementor-element e-con shop-location__image">

                <img
                    src="<?php print get_featured_image_url($postId); ?>"
                    alt="<?php print $row->post_title;?>"
                    loading="lazy"
                >

            </div>

        </div>

    <?php }?>

    <?php if($class == "odd"){ ?>

        <div class="elementor-element e-con e-flex shop-location <?php print $class; ?>">

            <!-- COLUMN 2: IMAGE -->
            <div class="elementor-element e-con shop-location__image">

                <img
                    src="<?php print get_featured_image_url($postId); ?>"
                    alt="<?php print $row->post_title;?>"
                    loading="lazy"
                >

            </div>            

            <!-- COLUMN 1: CONTENT -->
            <div class="elementor-element e-con e-flex shop-location__content">

                <h3 class="elementor-heading-title shop-location__title">
                    <?php print $title;?>
                </h3>

                <p class="shop-location__address">
                    <span class="ue-grid-item-meta-data-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="19" viewBox="0 0 14 19" fill="none"><path d="M6.58397 17.8107C6.6767 17.9498 6.83283 18.0333 7 18.0333C7.16717 18.0333 7.3233 17.9498 7.41603 17.8107C8.59933 16.0358 10.3422 13.8437 11.5568 11.6144C12.5279 9.83184 13 8.31154 13 6.96668C13 3.65827 10.3084 0.966675 7 0.966675C3.6916 0.966675 1 3.65827 1 6.96668C1 8.31154 1.4721 9.83184 2.44323 11.6144C3.65687 13.842 5.4031 16.0394 6.58397 17.8107ZM7 1.96667C9.757 1.96667 12 4.20968 12 6.96668C12 8.14014 11.5678 9.50394 10.6786 11.136C9.63173 13.0576 8.1378 15.0069 7 16.6456C5.86237 15.0071 4.36833 13.0577 3.32137 11.136C2.43223 9.50394 2 8.14014 2 6.96668C2 4.20968 4.243 1.96667 7 1.96667Z" fill="#555555" stroke="#555555" stroke-width="0.2"></path><path d="M7 9.96668C8.6542 9.96668 10 8.62087 10 6.96668C10 5.31247 8.6542 3.96667 7 3.96667C5.3458 3.96667 4 5.31247 4 6.96668C4 8.62087 5.3458 9.96668 7 9.96668ZM7 4.96667C8.1028 4.96667 9 5.86387 9 6.96668C9 8.06948 8.1028 8.96668 7 8.96668C5.8972 8.96668 5 8.06948 5 6.96668C5 5.86387 5.8972 4.96667 7 4.96667Z" fill="#555555" stroke="#555555" stroke-width="0.2"></path></svg></span>

                    <span>
                        <b><?php print $address; ?></b>
                    </span>
                </p>

                <p class="shop-location__hours">
                    <b><?php print $hours; ?></b>
                </p>

                <p class="shop-location__description">
                    <?php print $description;?>
                </p>

            </div>


        </div>

    <?php }?>

<?php

        $count++;

     }

     return ob_get_clean();
}

add_shortcode( 'shop_featured', 'elementor_shop_feature_shortcode' );