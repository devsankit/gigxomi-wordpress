<?php
/**
 * Plugin Name: Gigxomi Custom API
 * Description: Custom REST API for Next.js frontend
 * Version: 1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('gigxomi/v1', '/services', [
        'methods'  => 'GET',
        'callback' => 'gigxomi_get_services',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('gigxomi/v1', '/services/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'gigxomi_get_service',
        'permission_callback' => '__return_true',
    ]);
});

function gigxomi_get_services(WP_REST_Request $request) {
    $posts = get_posts([
        'post_type'      => 'service',
        'post_status'    => 'publish',
        'posts_per_page' => 200,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    $items = [];

    foreach ($posts as $post) {
        $item = gigxomi_format_service($post->ID);
        if ($item) {
            $items[] = $item;
        }
    }

    return rest_ensure_response([
        'count' => count($items),
        'items' => $items,
    ]);
}

function gigxomi_get_service(WP_REST_Request $request) {
    $service_id = absint($request['id']);
    $item = gigxomi_format_service($service_id);

    if (!$item) {
        return new WP_Error('not_found', 'Service not found', ['status' => 404]);
    }

    return rest_ensure_response($item);
}

function gigxomi_format_service($service_id) {
    $post = get_post($service_id);

    if (!$post || $post->post_type !== 'service' || $post->post_status !== 'publish') {
        return null;
    }

    $user_id = (int) $post->post_author;
    $freelancer_post_id = gigxomi_get_freelancer_post_id_by_user($user_id);

    $service_title = get_post_meta($service_id, '_service_title', true);
    $service_category = get_post_meta($service_id, '_service_category', true);
    $service_video_url = get_post_meta($service_id, '_service_video_url', true);
    $service_gallery = get_post_meta($service_id, '_service_gallery', true);
    $service_delivery_time = get_post_meta($service_id, '_service_delivery_time', true);
    $service_price_type = get_post_meta($service_id, '_service_price_type', true);
    $service_price = get_post_meta($service_id, '_service_price', true);
    $service_price_packages = get_post_meta($service_id, '_service_price_packages', true);
    $service_description = get_post_meta($service_id, '_service_description', true);
    $service_addons = get_post_meta($service_id, '_service_addons', true);
    $service_faq = get_post_meta($service_id, '_service_faq', true);

    $featured_image = get_the_post_thumbnail_url($service_id, 'full');
    if (!$featured_image) {
        $featured_image = get_post_meta($service_id, '_service_featured_image', true);
    }

    return [
        'service_id' => $service_id,
        'service_title' => $service_title ?: get_the_title($service_id),
        'service_slug' => $post->post_name,
        'service_url' => get_permalink($service_id),
        'featured_image' => $featured_image ?: '',
        'category' => gigxomi_format_category($service_category),
        'video_url' => $service_video_url ?: '',
        'gallery' => gigxomi_normalize_meta_value($service_gallery),
        'delivery_time' => $service_delivery_time ?: '',
        'price_type' => $service_price_type ?: '',
        'price' => $service_price !== '' ? (float) $service_price : null,
        'currency' => function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'INR',
        'price_packages' => gigxomi_normalize_meta_value($service_price_packages),
        'description' => $service_description ?: '',
        'addons' => gigxomi_normalize_meta_value($service_addons),
        'faq' => gigxomi_normalize_meta_value($service_faq),
        'freelancer' => gigxomi_format_freelancer($freelancer_post_id, $user_id),
    ];
}

function gigxomi_get_freelancer_post_id_by_user($user_id) {
    if (!$user_id) {
        return 0;
    }

    $posts = get_posts([
        'post_type'      => 'freelancer',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => '_freelancer_attached_user',
                'value'   => $user_id,
                'compare' => '=',
            ],
        ],
    ]);

    return !empty($posts) ? (int) $posts[0] : 0;
}

function gigxomi_format_freelancer($freelancer_post_id, $user_id = 0) {
    $user = $user_id ? get_user_by('id', $user_id) : false;

    $first_name_from_user = $user ? get_user_meta($user_id, 'first_name', true) : '';
    $last_name_from_user = $user ? get_user_meta($user_id, 'last_name', true) : '';
    $full_name_from_user = trim($first_name_from_user . ' ' . $last_name_from_user);

    $user_data = [
        'freelancer_post_id' => $freelancer_post_id ?: 0,
        'user_id' => $user_id ?: 0,
        'username' => $user ? $user->user_login : '',
        'display_name' => $user ? $user->display_name : '',
        'first_name' => $first_name_from_user ?: '',
        'last_name' => $last_name_from_user ?: '',
        'full_name' => $full_name_from_user ?: ($user ? $user->display_name : ''),
        'title' => '',
        'category' => '',
        'featured_image' => $user_id ? get_avatar_url($user_id, ['size' => 300]) : '',
        'english_level' => '',
        'freelancer_type' => '',
        'description' => '',
        'education' => [],
        'experience' => [],
        'skills' => [],
        'faq' => [],
    ];

    if (!$freelancer_post_id) {
        return $user_data;
    }

    $first_name = get_post_meta($freelancer_post_id, '_freelancer_firstname', true);
    $last_name = get_post_meta($freelancer_post_id, '_freelancer_lastname', true);
    $full_name = trim($first_name . ' ' . $last_name);

    if ($full_name === '') {
        $full_name = get_the_title($freelancer_post_id);
    }

    $featured_image = get_the_post_thumbnail_url($freelancer_post_id, 'full');
    if (!$featured_image) {
        $featured_image = get_post_meta($freelancer_post_id, '_freelancer_featured_image', true);
    }

    return [
        'freelancer_post_id' => $freelancer_post_id,
        'user_id' => (int) get_post_meta($freelancer_post_id, '_freelancer_attached_user', true) ?: $user_id,
        'username' => $user ? $user->user_login : '',
        'display_name' => $user ? $user->display_name : '',
        'first_name' => $first_name ?: $user_data['first_name'],
        'last_name' => $last_name ?: $user_data['last_name'],
        'full_name' => $full_name ?: $user_data['full_name'],
        'title' => get_post_meta($freelancer_post_id, '_freelancer_title', true) ?: '',
        'category' => gigxomi_format_category(get_post_meta($freelancer_post_id, '_freelancer_category', true)),
        'featured_image' => $featured_image ?: $user_data['featured_image'],
        'english_level' => get_post_meta($freelancer_post_id, '_freelancer_english_level', true) ?: '',
        'freelancer_type' => get_post_meta($freelancer_post_id, '_freelancer_freelancer_type', true) ?: '',
        'description' => get_post_meta($freelancer_post_id, '_freelancer_description', true) ?: '',
        'education' => gigxomi_normalize_meta_value(get_post_meta($freelancer_post_id, '_freelancer_education', true)),
        'experience' => gigxomi_normalize_meta_value(get_post_meta($freelancer_post_id, '_freelancer_experience', true)),
        'skills' => gigxomi_normalize_meta_value(get_post_meta($freelancer_post_id, '_freelancer_skill', true)),
        'faq' => gigxomi_normalize_meta_value(get_post_meta($freelancer_post_id, '_freelancer_faq', true)),
    ];
}

function gigxomi_format_category($value) {
    if (empty($value)) {
        return '';
    }

    if (is_array($value)) {
        $formatted = [];
        foreach ($value as $item) {
            $formatted[] = gigxomi_format_category($item);
        }
        return array_values(array_filter($formatted));
    }

    if (is_numeric($value)) {
        $term = get_term((int) $value);
        if ($term && !is_wp_error($term)) {
            return $term->name;
        }
    }

    return $value;
}

function gigxomi_normalize_meta_value($value) {
    if (is_array($value)) {
        return $value;
    }

    if (!is_string($value) || $value === '') {
        return [];
    }

    $maybe_json = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $maybe_json;
    }

    $maybe_unserialized = maybe_unserialize($value);
    if (is_array($maybe_unserialized)) {
        return $maybe_unserialized;
    }

    return [$value];
}
