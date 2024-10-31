<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

global $wpdb, $wp_filesystem;

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}optimum_gravatar_cache");

$options = get_option('OGC_options');

if ($options !== false) {
    delete_option('OGC_apacheConfig');
    delete_option('OGC_resolved');
    delete_option('OGC_options');
    delete_option('OGC_avatarUsedSizes');

    if ($options['cacheDirectory']) {
        if (is_dir(ABSPATH.$options['cacheDirectory']) && validateCacheDirectory($options['cacheDirectory'])) {
            $wp_filesystem->rmdir(ABSPATH.$options['cacheDirectory'], true);
        }
    }
}

function validateCacheDirectory($path)
{
    $systemDirectoriesConstants=array(
    ABSPATH,
    WP_CONTENT_DIR
  );
    $systemDirectories=array(
    ABSPATH."wp-admin",
    ABSPATH."wp-includes",
    WP_LANG_DIR,
    WPMU_PLUGIN_DIR,
    WP_PLUGIN_DIR,
    WP_CONTENT_DIR."/themes",
  );
    $systemDirectories[]=get_temp_dir();

    foreach ($systemDirectoriesConstants as $systemDirectory) {
        if (ABSPATH.$path == trailingslashit($systemDirectory)) {
            return false;
        }
    }
    foreach ($systemDirectories as $systemDirectory) {
        if (ABSPATH.$path == trailingslashit($systemDirectory) || strpos(ABSPATH.$path, trailingslashit($systemDirectory)) === 0) {
            return false;
        }
    }
    return true;
}
