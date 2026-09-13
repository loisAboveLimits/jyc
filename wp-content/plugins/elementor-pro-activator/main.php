<?php
/**
 * Plugin Name: Elementor Pro Activator
 * Plugin URI: https://www.gpltimes.com
 * Description: Allows users to activate Elementor Pro features without the typical license purchase.
 * Version: 1.0.4
 * Author: GPL Times
 * Author URI: https://www.gpltimes.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
defined( 'ABSPATH' ) || exit;

// Delay our custom functionality until WordPress is fully loaded
add_action('plugins_loaded', function() {
    $PLUGIN_NAME = 'Elementor Pro Activator';
    $PLUGIN_DOMAIN = 'elementor-pro-activator';
    
    // Load the functions file and extract the utilities
    extract(require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php');

    // Check for network activation
    if (!function_exists('is_plugin_active_for_network')) {
        require_once(ABSPATH . '/wp-admin/includes/plugin.php');
    }

    function is_network_activated() {
        return is_plugin_active_for_network(plugin_basename(__FILE__));
    }

    // License data
    $license_data = [
        'success' => true,
        "status" => "ACTIVE",
        "error" => "",
        'license' => 'valid',
        'item_id' => false,
        'item_name' => 'Elementor Pro',
        'checksum' => 'B5E0B5F8DD8689E6ACA49DD6E6E1A930',
        'expires' => 'lifetime',
        'payment_id' => '0123456789',
        'customer_email' => 'noreply@gmail.com',
        'customer_name' => 'GPL',
        'license_limit' => 1000,
        'site_count' => 1,
        'activations_left' => 999,
        'renewal_url' => '',
        'features' => [
            'template_access_level_20',
            'kit_access_level_20',
            'editor_comments',
            'activity-log',
            'breadcrumbs',
            'form',
            'posts',
            'template',
            'countdown',
            'slides',
            'price-list',
            'portfolio',
            'flip-box',
            'price-table',
            'login',
            'share-buttons',
            'theme-post-content',
            'theme-post-title',
            'nav-menu',
            'blockquote',
            'media-carousel',
            'animated-headline',
            'facebook-comments',
            'facebook-embed',
            'facebook-page',
            'facebook-button',
            'testimonial-carousel',
            'post-navigation',
            'search-form',
            'post-comments',
            'author-box',
            'call-to-action',
            'post-info',
            'theme-site-logo',
            'theme-site-title',
            'theme-archive-title',
            'theme-post-excerpt',
            'theme-post-featured-image',
            'archive-posts',
            'theme-page-title',
            'sitemap',
            'reviews',
            'table-of-contents',
            'lottie',
            'code-highlight',
            'hotspot',
            'video-playlist',
            'progress-tracker',
            'section-effects',
            'sticky',
            'scroll-snap',
            'page-transitions',
            'mega-menu',
            'nested-carousel',
            'loop-grid',
            'loop-carousel',
            'theme-builder',
            'elementor_icons',
            'elementor_custom_fonts',
            'dynamic-tags',
            'taxonomy-filter',
            'email',
            'email2',
            'mailpoet',
            'mailpoet3',
            'redirect',
            'header',
            'footer',
            'single-post',
            'single-page',
            'archive',
            'search-results',
            'error-404',
            'loop-item',
            'font-awesome-pro',
            'typekit',
            'gallery',
            'off-canvas',
            'link-in-bio-var-2',
            'link-in-bio-var-3',
            'link-in-bio-var-4',
            'link-in-bio-var-5',
            'link-in-bio-var-6',
            'link-in-bio-var-7',
            'search',
            'element-manager-permissions',
            'akismet',
            'display-conditions',
            'woocommerce-products',
            'wc-products',
            'woocommerce-product-add-to-cart',
            'wc-elements',
            'wc-categories',
            'woocommerce-product-price',
            'woocommerce-product-title',
            'woocommerce-product-images',
            'woocommerce-product-upsell',
            'woocommerce-product-short-description',
            'woocommerce-product-meta',
            'woocommerce-product-stock',
            'woocommerce-product-rating',
            'wc-add-to-cart',
            'dynamic-tags-wc',
            'woocommerce-product-data-tabs',
            'woocommerce-product-related',
            'woocommerce-breadcrumb',
            'wc-archive-products',
            'woocommerce-archive-products',
            'woocommerce-product-additional-information',
            'woocommerce-menu-cart',
            'woocommerce-product-content',
            'woocommerce-archive-description',
            'paypal-button',
            'woocommerce-checkout-page',
            'woocommerce-cart',
            'woocommerce-my-account',
            'woocommerce-purchase-summary',
            'woocommerce-notices',
            'settings-woocommerce-pages',
            'settings-woocommerce-notices',
            'popup',
            'custom-css',
            'global-css',
            'custom_code',
            'custom-attributes',
            'form-submissions',
            'form-integrations',
            'dynamic-tags-acf',
            'dynamic-tags-pods',
            'dynamic-tags-toolset',
            'editor_comments',
            'stripe-button',
            'role-manager',
            'global-widget',
            'activecampaign',
            'cf7db',
            'convertkit',
            'discord',
            'drip',
            'getresponse',
            'mailchimp',
            'mailerlite',
            'slack',
            'webhook',
            'product-single',
            'product-archive',
            'wc-single-elements'
        ],
        'tier' => 'expert',
        'generation' => 'empty'
    ];

    function activate_license_for_all_sites() {
        global $wpdb, $license_data;
        
        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        
        foreach ($site_ids as $site_id) {
            switch_to_blog($site_id);
            
            update_option('elementor_pro_license_key', md5('GPL'));
            update_option('elementor_pro_license_data', $license_data);
            
            restore_current_blog();
        }
    }

    // Set license key and data
    if (is_network_activated()) {
        add_action('admin_init', 'activate_license_for_all_sites');
    } else {
        add_action('admin_init', function () use ($license_data) {
            update_option('elementor_pro_license_key', md5('GPL'));
            update_option('elementor_pro_license_data', $license_data);
        });
    }

    // Filter HTTP requests to validate license
    add_filter('pre_http_request', function ($pre, $parsed_args, $url) use ($license_data) {
        if (strpos($url, 'https://my.elementor.com/api/v2/license/validate') !== false ||
            strpos($url, 'https://my.elementor.com/api/v2/license/activate') !== false) {
            return [
                'headers' => [],
                'body' => json_encode($license_data),
                'response' => [
                    'code' => 200,
                    'message' => 'OK'
                ]
            ];
        }

        if (strpos($url, 'https://my.elementor.com/api/v2/license/deactivate') !== false) {
            return [
                'headers' => [],
                'body' => json_encode(['success' => true]),
                'response' => [
                    'code' => 200,
                    'message' => 'OK'
                ]
            ];
        }

        if (strpos($url, 'https://my.elementor.com/api/connect/v1/activate/disconnect') !== false) {
            return [
                'headers' => [],
                'body' => 'true',
                'response' => [
                    'code' => 200,
                    'message' => 'OK'
                ]
            ];
        }

        return $pre;
    }, 99, 3);
}, 10);

// Hook into the WordPress HTTP API using the 'pre_http_request' filter
add_filter('pre_http_request', function ($preempt, $r, $url) {
    $intercept_urls = [
        'https://my.elementor.com/api/connect/v1/library/get_template_content',
        'https://my.elementor.com/api/v1/kits-library/kits/', // base URL for download-link
    ];
    $redirect_url_post = 'https://www.gpltimes.com/gpldata/elementorv2.php';
    $redirect_url_get = 'https://www.gpltimes.com/gpldata/elementorv3.php';

    $gplstatus = get_option('gplstatus');
    $gpltokenid = get_option('gpltokenid');
    $domain = parse_url(get_site_url(), PHP_URL_HOST);
    $domain = strtolower(trim($domain));

    // Skip the API call if essential parameters are missing
    if (empty($gplstatus) || empty($gpltokenid)) {          
        return $preempt;
    }

    // Check if the request URL matches any of the intercept URLs
    foreach ($intercept_urls as $intercept_url) {
        if (strpos($url, $intercept_url) !== false) {
            // Determine if the request is a GET or POST
            if (isset($r['method']) && strtoupper($r['method']) === 'POST') {
                // Handle POST requests
                $request_data = array_merge($r, array(
                    'gpltokenid' => $gpltokenid,
                    'gplstatus' => $gplstatus,
                    'domain' => $domain
                ));

                // Send the request data to the redirect URL
                $response = wp_remote_post($redirect_url_post, array(
                    'method'    => 'POST',
                    'body'      => json_encode($request_data),
                    'headers'   => array('Content-Type' => 'application/json')
                ));

                // Check for errors in the response
                if (is_wp_error($response)) {
                    return $preempt;
                }

                // Get the response body
                $response_body = wp_remote_retrieve_body($response);
                $decoded_body = json_decode($response_body, true);

                // Extract the body part of the response
                if (isset($decoded_body['body'])) {
                    $response_body = $decoded_body['body'];
                }

                // Return the response body to the original requester
                return array(
                    'headers' => array(),
                    'body' => $response_body,
                    'response' => array(
                        'code' => 200,
                        'message' => 'OK'
                    ),
                );

            } elseif (isset($r['method']) && strtoupper($r['method']) === 'GET' && strpos($url, 'download-link') !== false) {
                // Handle GET requests for the download-link
                $query_args = array(
                    'gpltokenid' => $gpltokenid,
                    'gplstatus' => $gplstatus,
                    'domain' => $domain,
                    'kit_id' => explode('/', rtrim(parse_url($url, PHP_URL_PATH), '/'))[5]
                );

                $redirected_url = add_query_arg($query_args, $redirect_url_get);

                // Redirect the GET request to the new URL with added query parameters
                $response = wp_remote_get($redirected_url);

                // Check for errors in the response
                if (is_wp_error($response)) {
                    return $preempt;
                }

                // Get the response body
                $response_body = wp_remote_retrieve_body($response);

                // Return the response body to the original requester
                return array(
                    'headers' => array(),
                    'body' => $response_body,
                    'response' => array(
                        'code' => 200,
                        'message' => 'OK'
                    ),
                );
            }
        }
    }

    // Allow other requests to proceed normally
    return $preempt;
}, 10, 3);
