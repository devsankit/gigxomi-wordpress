<?php
/**
 * Minimal theme support for the Gigxomi headless WordPress CMS.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['style', 'script', 'gallery', 'caption']);
});

/**
 * WordPress remains the editorial backend. Public content is canonical on the
 * Next.js application, so legacy front-end requests are redirected there.
 */
add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
        return;
    }

    $request_path = wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $request_path = is_string($request_path) ? $request_path : '/';

    // Do not let Yoast advertise legacy HTTP sitemap URLs after the move.
    if (preg_match('/(?:^|\/)[^\/]*sitemap[^\/]*\.xml$/i', $request_path)) {
        wp_redirect('https://www.gigxomi.com/sitemap.xml', 301, 'Gigxomi Headless CMS');
        exit;
    }

    // WordPress must render robots.txt directly so crawlers can discover the
    // canonical sitemap and continue following the permanent content moves.
    if (function_exists('is_robots') && is_robots()) {
        return;
    }

    // Redirect legacy marketplace service URLs to homepage
    if (preg_match('#^/services?(?:/.*)?$#i', $request_path)) {
        wp_redirect('https://www.gigxomi.com/', 301, 'Gigxomi Headless CMS');
        exit;
    }

    // Redirect legacy freelancer URLs to canonical freelancers directory
    if (preg_match('#^/freelancer(?:/.*)?$#i', $request_path)) {
        wp_redirect('https://www.gigxomi.com/freelancers', 301, 'Gigxomi Headless CMS');
        exit;
    }

    // Redirect legacy home path
    if (preg_match('#^/home/?$#i', $request_path)) {
        wp_redirect('https://www.gigxomi.com/', 301, 'Gigxomi Headless CMS');
        exit;
    }

    // Redirect legacy tag archives directly to the canonical blog
    if (preg_match('#^/tag(?:/.*)?$#i', $request_path)) {
        wp_redirect('https://www.gigxomi.com/blog', 301, 'Gigxomi Headless CMS');
        exit;
    }

    $target = 'https://www.gigxomi.com/blog';

    if (is_singular('post')) {
        $target .= '/' . get_post_field('post_name', get_queried_object_id());
    } elseif (is_singular('service')) {
        $target = 'https://www.gigxomi.com/';
    } elseif (is_404()) {
        return;
    }

    wp_redirect(esc_url_raw($target), 301, 'Gigxomi Headless CMS');
    exit;
}, -100);

add_filter('robots_txt', static function (string $output, bool $public): string {
    unset($output, $public);

    return "User-agent: *\nAllow: /\nSitemap: https://www.gigxomi.com/sitemap.xml\n";
}, 100, 2);

// Exclude post_tag taxonomy from Yoast sitemaps to prevent crawling 1,000+ thin tag archives
add_filter('wpseo_sitemap_exclude_taxonomy', static function (bool $exclude, string $taxonomy): bool {
    if ($taxonomy === 'post_tag') {
        return true;
    }
    return $exclude;
}, 10, 2);
