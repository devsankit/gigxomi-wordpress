<?php
/**
 * Fallback template. Normal public requests are redirected by functions.php.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<main>
    <div>
        <h1>Gigxomi publishing CMS</h1>
        <p>The public Gigxomi blog is available at <a href="https://www.gigxomi.com/blog">www.gigxomi.com/blog</a>.</p>
    </div>
</main>
<?php wp_footer(); ?>
</body>
</html>
