<?php
/*
Plugin Name: Optimum Gravatar Cache
Plugin URI:   https://www.ncdc.pt/groups/wordpress-optimum-gravatar-cache/
Version: 1.4.10
Author: José Miguel Silva Caldeira
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Description: It cache the gravatars locally, reducing the total number of requests per post. This will speed up the loading of the site and consequently improve the user experience.
Author URI: https://www.ncdc.pt/members/admin
Text Domain: OGC
Domain Path:  /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

class OGC
{
    protected $options;
    protected $phpModulesRequired;
    protected $mimeTypes=array("image/jpeg" => "jpg","image/png" => "png","image/gif" => "gif", "image/svg+xml"=>"svg");
    protected $pluginName = 'Optimum Gravatar Cache';
    protected $pluginInternalName = 'optimum_gravatar_cache';
    protected $pluginSlug = 'optimum-gravatar-cache';
    protected $pluginDirectory;
    protected $pluginVersion = '1.4.10';
    protected $cacheDirectory;
    protected $expireTime;
    protected $activated;
    protected $errorMessages=array();
    protected $cacheTableName;
    protected $searchExpiredTime;
    protected $maxUpdateEachTime;
    protected $maxOptimizeEachTime;
    protected $precompress;
    protected $defaultAvatar;
    protected $customAvatarExt;
    protected $learningMode;
    protected $resolved;
    protected $avatarUsedSizes;
    protected $avatarRating;
    protected $optimizeAvatars;
    protected $curl=false;
    protected $curlOptimize=false;
    protected $curlCloudflare=false;
    protected $avatarCached=array();
    protected $hashsOnthisUrl=array();
    protected $currentUrl;
    protected $useCurrentUrl=true;
    protected $currentVersion=false;

    protected $cachePagePlugins=null;
    protected $postsToClear=array();

    protected $hashsToCleanOnPageCachePlugins=array();
    protected $urlsToClearOnCloudflare=array();


    protected $clearPageChachePluginsCache;
    protected $customAvatarFileTime;
    protected $apacheConfigSaved;
    protected $useQueryStrings;
    protected $sendUserAgent;
    protected $userAgent;
    protected $directoriesMode;
    protected $avatarsMode;
    protected $htaccessMode;
    protected $resolvHostFromContentURL;
    protected $firstSave;
    protected $removeFromCacheWhenRemovedFromGravatar;
    protected $additionalClasses;
    protected $synchronizedEmail;
    protected $useSourceAsLongNotParsed;
    protected $lazyLoad;
    protected $scriptInFooter;
    protected $offset;
    protected $notInThisClass;
    protected $usePlaceholder;
    protected $onlyOnTheFrontend;
    protected $host;
    protected $scheme;
    protected $cacheURL;
    protected $placeholderURL;
    protected $placeholderFileTime;
    protected $defaultPlaceholder;
    protected $placeholderExt;
    protected $useLazyOnAdminPages;
    protected $clearCloudflareCache;
    protected $clearCloudflareCacheAuthEmail;
    protected $clearCloudflareCacheAuthKey;
    protected $clearCloudflareCacheZoneID;


    public function __construct()
    {
        global $wpdb,$pagenow, $wp;

        $this->pluginDirectory = dirname(__FILE__);

        $this->cacheTableName=$wpdb->prefix.$this->pluginInternalName;
        $this->resolved=get_option('OGC_resolved');
        $this->options = get_option('OGC_options');
        if(is_array($this->options)) {
            $this->currentVersion=$this->options['currentVersion'];
        }
        $this->avatarUsedSizes = get_option('OGC_avatarUsedSizes');

        if ($this->resolved===false || $this->options===false || $this->avatarUsedSizes===false || $this->pluginVersion != $this->currentVersion) {
            $this->activate();
        }

        $this->avatarRating=get_option('avatar_rating');
        $this->cacheDirectory = $this->options['cacheDirectory'];
        $this->expireTime=$this->options['expireTime'];
        $this->activated=$this->options['activated'];
        $this->searchExpiredTime=$this->options['searchExpiredTime'];
        $this->maxUpdateEachTime=$this->options['maxUpdateEachTime'];
        $this->optimizeAvatars=$this->options['optimizeAvatars'];
        $this->defaultAvatar=$this->options['defaultAvatar'];
        $this->precompress=$this->options['precompress'];
        $this->customAvatarExt=$this->options['customAvatarExt'];
        $this->customAvatarFileTime=$this->options['customAvatarFileTime'];
        $this->learningMode=$this->options['learningMode'];
        $this->maxOptimizeEachTime=$this->options['maxOptimizeEachTime'];
        $this->apacheConfigSaved=$this->options['apacheConfigSaved'];
        $this->useQueryStrings=$this->options['useQueryStrings'];
        $this->sendUserAgent=$this->options['sendUserAgent'];
        $this->userAgent=$this->options['userAgent'];
        $this->directoriesMode=$this->options['directoriesMode'];
        $this->avatarsMode=$this->options['avatarsMode'];
        $this->htaccessMode=$this->options['htaccessMode'];
        $this->resolvHostFromContentURL=$this->options['resolvHostFromContentURL'];
        $this->firstSave=$this->options['firstSave'];
        $this->phpModulesRequired=$this->phpModulesRequired();
        $this->removeFromCacheWhenRemovedFromGravatar=$this->options['removeFromCacheWhenRemovedFromGravatar'];
        $this->additionalClasses=$this->options['additionalClasses'];
        $this->synchronizedEmail=$this->options['synchronizedEmail'];
        $this->useSourceAsLongNotParsed=$this->options['useSourceAsLongNotParsed'];

        $this->lazyLoad=$this->options['lazyLoad'];
        $this->scriptInFooter=$this->options['scriptInFooter'];
        $this->offset=$this->options['offset'];
        $this->notInThisClass=$this->options['notInThisClass'];
        $this->usePlaceholder=$this->options['usePlaceholder'];
        $this->onlyOnTheFrontend=$this->options['onlyOnTheFrontend'];

        if ($this->resolvHostFromContentURL) {
            $this->host=wp_parse_url(content_url(), PHP_URL_HOST);
            $this->scheme=parse_url(content_url(), PHP_URL_SCHEME);
        } else {
            $this->host=wp_parse_url(site_url(), PHP_URL_HOST);
            $this->scheme=parse_url(site_url(), PHP_URL_SCHEME);
        }

        $this->cacheURL=$this->scheme .'://'.$this->host.parse_url(site_url(), PHP_URL_PATH).'/'.$this->cacheDirectory;

        $this->placeholderURL=$this->options['placeholderURL'];
        $this->placeholderFileTime=$this->options['placeholderFileTime'];
        $this->defaultPlaceholder=$this->options['defaultPlaceholder'];
        $this->placeholderExt=$this->options['placeholderExt'];

        $this->useLazyOnAdminPages=true;
        if ($this->lazyLoad && $this->onlyOnTheFrontend && is_admin()) {
            $this->useLazyOnAdminPages=false;
        }

        $this->clearCloudflareCache=$this->options['clearCloudflareCache'];
        $this->clearCloudflareCacheAuthEmail=$this->options['clearCloudflareCacheAuthEmail'];
        $this->clearCloudflareCacheAuthKey=$this->options['clearCloudflareCacheAuthKey'];
        $this->clearCloudflareCacheZoneID=$this->options['clearCloudflareCacheZoneID'];

        $this->clearPageChachePluginsCache=$this->options['clearPageChachePluginsCache'];

        add_action('wp_loaded', array( $this,'pluginsLoad' ));
    }

    public function pluginsLoad()
    {
        global $pagenow;

        if (!is_admin()  && !get_option('show_avatars')) {
            return;
        }

        if ($this->hasPermissionsToRun() && $this->phpModulesRequired && $this->activated) {
            add_action('delete_user', array( $this,'deleteUserAvatarsCache' ), 1, 1);
            add_action('shutdown', array( $this,'shutdown' ));

            if ($this->lazyLoad && !is_admin()) {
                add_action('wp_enqueue_scripts', array( $this, 'clientScripts'), 0);
                if ($this->scriptInFooter) {
                    add_filter('the_content', array( $this, 'clientScriptsIfNeed'), 9999, 1);
                }
            }

            add_filter('get_avatar', array( $this,'getWordPressCachedAvatar' ), 5, 6);
            add_filter('get_avatar_url', array( $this,'getWordPressCachedAvatarURL' ), 5, 3);
            add_filter('get_avatar', array( $this,'deleteWordPressCachedAvatarSRCSet' ), 9999, 6);

            if (function_exists('bp_is_active')) {
                add_filter('bp_core_fetch_avatar', array( $this,'getBPressCachedAvatar' ), 99, 3);
                add_filter('bp_core_fetch_avatar_url', array( $this,'getBPressCachedAvatarURL' ), 5, 2);
                add_filter('bp_activity_allowed_tags', array( $this,'overrideAllowedBuddyPressTags' ), 10, 1);
            }

            if (!$this->learningMode) {
                add_filter('cron_schedules', array( $this, 'schedules'));
                $this->setCronEvent();
                add_action('OGC_CronEvent', array( $this, 'updateCache'));
            }
        }

        $this->checkThePageCachePluginThatIsInUse();

        if (is_admin() && !defined('DOING_AJAX')) {
            add_action('admin_enqueue_scripts', array( $this, 'adminScripts'));

            if (strpos(__FILE__, WPMU_PLUGIN_DIR) !== false) {
                load_muplugin_textdomain("OGC", $this->pluginSlug . '/languages/');
            } else {
                load_plugin_textdomain("OGC", false, $this->pluginSlug . '/languages/');
            }

            add_action('admin_notices', array( $this, 'adminPermissionsNotices'));

            if (current_user_can('activate_plugins')) {
                register_activation_hook(__FILE__, array( $this, 'activate' ));
                register_deactivation_hook(__FILE__, array( $this, 'deactivate' ));
                add_filter("plugin_action_links_".plugin_basename(__FILE__), array( $this, 'addSettingsLink'));
                add_filter('plugin_row_meta', array( $this, 'addProjectLinks'), 10, 2);
            }

            if (current_user_can('manage_options')) {

                add_action('admin_menu', array( $this,'addAdminMenu'));

                if ($pagenow=="options-general.php" && isset($_GET['page']) && $_GET['page'] == $this->pluginSlug) {
                    $this->adminHasPermissionsToRun();
                    $this->hasCronEnabled();

                    if ($_SERVER['REQUEST_METHOD']=='POST') {
                        if (!wp_verify_nonce($_POST['OGC_options']['nonce'], "OGC")) {
                            return;
                        }
                        if (isset($_POST['syncUsersAndCommenters'])) {
                            $this->syncUsersAndCommenters();
                        }
                        if (isset($_POST['clearCache'])) {
                            $this->clearCache();
                        }
                        if (isset($_POST['updateCacheOptions'])) {
                            $this->updateCacheOptions();
                        }
                        if (isset($_POST['updateDefaultAvatarOptions'])) {
                            $this->updateDefaultAvatarOptions();
                        }
                        if (isset($_POST['updateOptimizationOptions'])) {
                            $this->updateOptimizationOptions();
                        }
                        if (isset($_POST['updateOtherOptions'])) {
                            $this->updateOtherOptions();
                        }
                        if (isset($_POST['updateApacheConfiguration'])) {
                            $this->updateApacheConfiguration();
                        }
                        if (isset($_POST['updateLazyLoadOptions'])) {
                            $this->updateLazyLoadOptions();
                        }
                        if (isset($_POST['updateCloudflareOptions'])) {
                            $this->updateCloudflareOptions();
                        }
                    }
                }
            }
        }
    }

    protected function phpModulesRequired()
    {
        if ((extension_loaded('imagick') || extension_loaded('gd')) && extension_loaded('curl')) {
            return true;
        }
        return false;
    }

    protected function adminPhpModulesRequired()
    {
        if (!$this->phpModulesRequired()) {
            $this->options['messages']['phpModules'] = array(
              'type' => "error notice",
              'message' => __("This plugin is requires some PHP modules that are not currently loaded:<br>Imagick/GD - One of these modules is required to allow the plugin to resize the cached Gravatars;<br>Curl - This module is required to allow the plugin to download several Gravatars via the same connection.<br><br>Please activate these modules, to allow the plugin to work.", "OGC"),
              'args'=>array()
            );
            update_option('OGC_options', $this->options);
            return false;
        } else {
            if (isset($this->options['messages']['phpModules'])) {
                unset($this->options['messages']['phpModules']);
            }
            update_option('OGC_options', $this->options);
        }
        return true;
    }

    public function addSettingsLink($links)
    {
        $settingsLink = '<a href="options-general.php?page='.$this->pluginSlug.'">' . __("Settings", "OGC") . '</a>';
        array_push($links, $settingsLink);
        return $links;
    }

    public function addProjectLinks($links, $file)
    {
        if (basename(__FILE__) === basename($file)) {
            $newLinks = array(
                    'discussionGroup' => '<a href="//www.ncdc.pt/groups/wordpress-optimum-gravatar-cache" target="_blank">'.__("Discussion Group", "OGC").'</a>',
                    'donate' => '<a href="//www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ETTJQCUA5Q6CE" target="_blank">'.__("Donate", "OGC").'</a>'
                    );
            $links = array_merge($links, $newLinks);
        }
        return $links;
    }

    public function schedules($schedules)
    {
        $schedules["OGC_job"] = array(
            'interval' => $this->searchExpiredTime*60,
            'display' => $this->pluginName.' cron job'
          );
        return $schedules;
    }

    protected function setCronEvent()
    {
        if (! wp_next_scheduled('OGC_CronEvent')) {
            wp_schedule_event(time(), 'OGC_job', 'OGC_CronEvent');
        }
    }
    public function reSetCronEvent()
    {
        if (wp_next_scheduled('OGC_CronEvent')) {
            wp_clear_scheduled_hook('OGC_CronEvent');
        }
        wp_schedule_event(time() + 5, 'OGC_job', 'OGC_CronEvent');
    }

    protected function getCurrentDefaultAvatar()
    {
        if ($this->customAvatarExt=='svg') {
            if ($this->defaultAvatar) {
                $file= plugins_url('/avatar/default.svg', __FILE__);
            } else {
                $file= home_url($this->cacheDirectory.'custom/custom.svg');
            }
        } else {
            $file=$this->cacheDirectory."custom/custom.".$this->customAvatarExt;
            $file= home_url($this->cacheDirectory.'custom/custom.'.$this->customAvatarExt);
        }
        $queryString="";
        if ($this->useQueryStrings) {
            $fileTime=base_convert($this->customAvatarFileTime, 10, 35);
            $queryString="?d={$fileTime}";
        }
        return "{$file}{$queryString}";
    }

    protected function getCurrentPlaceholder()
    {
        $queryString="";
        if ($this->useQueryStrings) {
            $fileTime=base_convert($this->placeholderFileTime, 10, 35);
            $queryString="?d={$fileTime}";
        }
        return "{$this->placeholderURL}{$queryString}";
    }

    protected function getLogo()
    {
        return plugins_url('/admin/images/logo.svg', __FILE__);
    }

    protected function updateDefaultAvatarsExt()
    {
        global $wpdb;

        $wpdb->query("UPDATE `{$this->cacheTableName}` SET `ext`='{$this->customAvatarExt}' WHERE def='1'");
    }

    protected function deleteDefaultAvatars()
    {
        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            foreach (glob(ABSPATH.$this->cacheDirectory."0*") as $fileName) {
                unlink($fileName);
            }
        }
    }

    protected function deleteDefaultPlaceholder()
    {
        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            foreach (glob(ABSPATH.$this->cacheDirectory."_.*") as $fileName) {
                unlink($fileName);
            }
        }
    }

    protected function deleteOldCustomAvatar()
    {
        $currentCustomAvatar=ABSPATH.$this->cacheDirectory."custom/custom.".$this->customAvatarExt;

        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            foreach (glob(ABSPATH.$this->cacheDirectory."custom/custom.*") as $fileName) {
                if ($fileName != $currentCustomAvatar) {
                    unlink($fileName);
                }
            }
        }
    }

    protected function deleteOldCustomPlaceholder()
    {
        $currentCustomPlaceholder=ABSPATH.$this->cacheDirectory."custom/placeholder.".$this->placeholderExt;

        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            foreach (glob(ABSPATH.$this->cacheDirectory."custom/placeholder.*") as $placeholder) {
                if ($placeholder != $currentCustomPlaceholder) {
                    unlink($placeholder);
                }
            }
        }
    }

    protected function optimizeDefaultGravatars()
    {
        $needUpdate=0;

        $optimizableTypes=array_values($this->mimeTypes);
        if (($key = array_search('svg', $optimizableTypes)) !== false) {
            unset($optimizableTypes[$key]);
        }

        foreach (glob(ABSPATH.$this->cacheDirectory.'0*.{'.implode(",", $optimizableTypes).'}', GLOB_BRACE) as $avatarFile) {
            $optimezedAvatarStat=$avatarFile.".O";
            $avatarBaseName=basename($avatarFile);


            if (!file_exists($optimezedAvatarStat)) {
                $avatarURL="{$this->cacheURL}{$avatarBaseName}";
                $optimizedDefaultAvatarRequest=$this->sendResmushRequest($avatarFile);
                if (!$optimizedDefaultAvatarRequest->error) {
                    $optimizedDefaultAvatar=$this->getOptimizedAvatar($optimizedDefaultAvatarRequest->optimizedURL);
                    if (!$optimizedDefaultAvatar->error && $optimizedDefaultAvatar->status == 200) {
                        if (file_put_contents($avatarFile, $optimizedDefaultAvatar->content)) {
                            $needUpdate++;
                            $this->urlsToClearOnCloudflare[]=$avatarURL;
                            touch($avatarFile, $this->customAvatarFileTime);
                            chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
                            touch($avatarFile.".O", $this->customAvatarFileTime);
                            chmod($avatarFile.".O", base_convert($this->avatarsMode, 8, 10));
                        }
                    }
                }
            }
        }
        if ($needUpdate > 0) {
            $this->getDefaultGravatarHashs();
        }
    }

    protected function validateCacheDirectory($path)
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
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("<b>Gravatar cache directory</b>: It is not possible to use the default WordPress directory '%s' as the Gravatar cache directory. However it is possible to create a directory within it.<br>For example: '%s'.<br><br>This will help avoid loss of data, which could be caused by clearing the Gravatar cache or uninstalling the plug-in.", "OGC"),
                  "args"=>array(trailingslashit($systemDirectory), trailingslashit($systemDirectory)."optimum-gravatar-cache")
                );
                return false;
            }
        }

        foreach ($systemDirectories as $systemDirectory) {
            if (ABSPATH.$path == trailingslashit($systemDirectory) || strpos(ABSPATH.$path, trailingslashit($systemDirectory)) === 0) {
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("<b>Gravatar cache directory</b>: It is not possible to use the default WordPress directory '%s' as the Gravatar cache directory. Please manually create a folder at the root of your site, or create a folder within the '%s' folder.<br><br>This will help avoid loss of data, which could be caused by clearing the Gravatar cache or uninstalling the plug-in.", "OGC"),
                  "args"=>array($systemDirectory, WP_CONTENT_DIR)
                );
                return false;
            }
        }

        return true;
    }

    protected function isSizeInteger($data)
    {
        $options = array(
        'options' => array(
            'min_range' => 1
          )
        );
        if (filter_var($data, FILTER_VALIDATE_INT, $options)) {
            return true;
        } else {
            return false;
        }
    }

    protected function addUsersAndCommentersToCache()
    {
        global $wpdb;
        $maxValue = max($this->avatarUsedSizes);

        $commentrsSQL = "SELECT DISTINCT(`comment_author_email`) FROM `{$wpdb->prefix}comments` WHERE `user_id` = 0";
        $commentrsResults = $wpdb->get_results($commentrsSQL, ARRAY_N);

        foreach ($commentrsResults as $commenter) {
            $r=$this->getCachedAvatar(null, $commenter[0], $maxValue, null, '', false, false, false);
        }

        $usersSQL  = "SELECT `user_email` FROM `{$wpdb->prefix}users`";
        $usersResults = $wpdb->get_results($usersSQL, ARRAY_N);

        foreach ($usersResults as $user) {
            $r=$this->getCachedAvatar(null, $user[0], $maxValue, null, '', false, false, false);
        }
    }



    protected function updateCacheOptions()
    {
        $valideInput=true;
        $newOptions=array();

        if (isset($_POST['OGC_options']['activated']) && (int)$_POST['OGC_options']['activated']==1) {
            $newOptions['activated']=1;
        } else {
            $newOptions['activated']=0;
        }

        $newOptions['cacheDirectory']="";
        if (isset($_POST['OGC_options']['cacheDirectory'])) {
            $newOptions['cacheDirectory']=trim($_POST['OGC_options']['cacheDirectory'], DIRECTORY_SEPARATOR);
            $newOptions['cacheDirectory'] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $newOptions['cacheDirectory']);
            $newOptions['cacheDirectory'] = array_filter(explode(DIRECTORY_SEPARATOR, $newOptions['cacheDirectory']), 'strlen');
            if (!in_array('.', $newOptions['cacheDirectory']) && !in_array('..', $newOptions['cacheDirectory'])) {
                $newOptions['cacheDirectory'] = implode(DIRECTORY_SEPARATOR, $newOptions['cacheDirectory']);
                if ($newOptions['cacheDirectory']) {
                    $newOptions['cacheDirectory']=$newOptions['cacheDirectory'].DIRECTORY_SEPARATOR;
                }
                if (!$this->validateCacheDirectory($newOptions['cacheDirectory'])) {
                    $valideInput=false;
                }
            } else {
                $newOptions['cacheDirectory']=$_POST['OGC_options']['cacheDirectory'];
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("<b>Gravatar cache directory</b>: You cannot use relative directories, such as ./ or ../.", "OGC"),
                  "args"=>array()
                );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['expireTime'])) {
            $newOptions['expireTime']=(int)$_POST['OGC_options']['expireTime'];
            if ($newOptions['expireTime'] < 1) {
                $this->errorMessages[]=array(
                      "type" => "error notice",
                      "message" => __("<b>Refresh Gravatar cache every</b>: Please enter a whole number greater than or equal to 1.", "OGC"),
                      "args"=>array()
                    );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['searchExpiredTime'])) {
            $newOptions['searchExpiredTime']=(int)$_POST['OGC_options']['searchExpiredTime'];
            if ($newOptions['searchExpiredTime'] < 1) {
                $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("<b>Check for outdated Gravatars every</b>: Please enter a whole number greater than or equal to 1.", "OGC"),
                    "args"=>array()
                  );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['maxUpdateEachTime'])) {
            $newOptions['maxUpdateEachTime']=(int)$_POST['OGC_options']['maxUpdateEachTime'];
            if ($newOptions['maxUpdateEachTime'] < 1) {
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("<b>Number of users to check simultaneously</b>: Please enter a whole number greater than or equal to 1.", "OGC"),
                  "args"=>array()
                );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['directoriesMode'])) {
            $newOptions['directoriesMode'] = sanitize_text_field($_POST['OGC_options']['directoriesMode']);
            if (!$this->validatePermissionsMode($newOptions['directoriesMode'])) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("<b>Cache directory permissions</b>: Please enter an octal value for the permissions to be used for the cache directory.", "OGC"),
                "args"=>array()
                );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['avatarsMode'])) {
            $newOptions['avatarsMode'] = sanitize_text_field($_POST['OGC_options']['avatarsMode']);
            if (!$this->validatePermissionsMode($newOptions['avatarsMode'])) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("<b>Gravatar files permissions</b>: Please enter an octal value for the permissions to be used for the cached Gravatar files.", "OGC"),
                "args"=>array()
                );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['avatarUsedSizes'])) {
            $avatarUsedSizes=array_map('trim', explode(',', $_POST['OGC_options']['avatarUsedSizes']));
            $avatarUsedSizes = array_filter($avatarUsedSizes, array( $this,'isSizeInteger' ));
            $avatarUsedSizes = array_values($avatarUsedSizes);
            $avatarUsedSizes=array_unique($avatarUsedSizes, SORT_REGULAR);
            rsort($avatarUsedSizes);
            if (count($avatarUsedSizes)<1) {
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("<b>Gravatar sizes used</b>: You must enter at least one size.<br>You can only enter whole numbers, greater than or equal to 1.<br>Multiple values must be separated by a comma.", "OGC"),
                  "args"=>array()
                );
                $valideInput=false;
            }
        }

        $oldCacheDirectory=$this->cacheDirectory;

        if ($valideInput) {
            $this->activated=$newOptions['activated'];
            $this->cacheDirectory=$newOptions['cacheDirectory'];
            $this->expireTime=$newOptions['expireTime'];
            $this->searchExpiredTime=$newOptions['searchExpiredTime'];
            $this->maxUpdateEachTime=$newOptions['maxUpdateEachTime'];
            $this->avatarUsedSizes=$avatarUsedSizes;
            $this->directoriesMode=$newOptions['directoriesMode'];
            $this->avatarsMode=$newOptions['avatarsMode'];
        }

        if ($valideInput) {
            clearstatcache();

            if (is_writable(dirname(ABSPATH.$this->cacheDirectory, 1))) {
                if ($this->firstSave && ABSPATH.$oldCacheDirectory != ABSPATH.$this->cacheDirectory && is_dir(ABSPATH.$oldCacheDirectory)) {
                    if (!$this->copyOldCacheDirectoryFiles(ABSPATH.$oldCacheDirectory, ABSPATH.$this->cacheDirectory)) {
                        $this->errorMessages[]=array(
                          "type" => "error notice",
                          "message" => __("Could not copy files from old cache directory.", "OGC"),
                          "args"=>array(ABSPATH.$this->cacheDirectory, dirname(ABSPATH.$this->cacheDirectory, 1))
                        );
                        $valideInput=false;
                    }
                }

                if (!$this->makeCacheDirectory()) {
                    $valideInput=false;
                } else {
                    if (!$this->setCachePermissions()) {
                        $valideInput=false;
                    }
                }
            } else {
                $this->errorMessages[]=array(
                  "type" => "error notice",
                  "message" => __("Could not create directory '%s'.<br>Please make sure the '%s' directory exists and you have write permissions on this directory.", "OGC"),
                  "args"=>array(ABSPATH.$this->cacheDirectory, dirname(ABSPATH.$this->cacheDirectory, 1))
                );
                $valideInput=false;
            }
        }

        if (!$this->adminPhpModulesRequired()) {
            $valideInput=false;
        }

        if ($valideInput) {
            $this->deleteUnneededDefaultAvatarsSize();
            $this->createMissingDefaultAvatarSizes();

            if (isset($this->options['messages']['no-cache-dir'])) {
                unset($this->options['messages']['no-cache-dir']);
            }
            if (isset($this->options['messages']['no-writable-cache-dir'])) {
                unset($this->options['messages']['no-writable-cache-dir']);
            }
            if (isset($this->options['messages']['no-executable-cache-dir'])) {
                unset($this->options['messages']['no-executable-cache-dir']);
            }
            if (isset($this->options['messages']['no-cache-tmp-dir'])) {
                unset($this->options['messages']['no-cache-tmp-dir']);
            }
            if (isset($this->options['messages']['no-writable-cache-tmp-dir'])) {
                unset($this->options['messages']['no-writable-cache-tmp-dir']);
            }
            if (isset($this->options['messages']['no-executable-cache-tmp-dir'])) {
                unset($this->options['messages']['no-executable-cache-tmp-dir']);
            }
            if (isset($this->options['messages']['no-cache-custom-dir'])) {
                unset($this->options['messages']['no-cache-custom-dir']);
            }
            if (isset($this->options['messages']['no-writable-custom-tmp-dir'])) {
                unset($this->options['messages']['no-writable-custom-tmp-dir']);
            }
            if (isset($this->options['messages']['no-executable-custom-tmp-dir'])) {
                unset($this->options['messages']['no-executable-custom-tmp-dir']);
            }



            if (isset($this->options['messages']['firstSave'])) {
                unset($this->options['messages']['firstSave']);
            }

            $newOptions['firstSave']=1;

            $newOptions = wp_parse_args($newOptions, $this->options);

            $this->firstSave=1;

            update_option('OGC_avatarUsedSizes', $avatarUsedSizes);
            update_option('OGC_options', $newOptions);

            $this->errorMessages[]=array(
            "type" => "notice notice-success",
            "message" => __("Options have been updated.", "OGC"),
            "args"=>array()
            );
            $this->reSetCronEvent();
        }

        return true;
    }

    protected function updateDefaultAvatarOptions()
    {
        $newOptions=array();
        $needCleanCache=false;
        $valideInput=true;


        if (isset($_FILES['file'])) {
            if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
                $avatar=$this->adminSaveCustomAvatar("custom");
                if ($avatar) {
                    $newOptions['defaultAvatar']=$avatar['default'];
                    $newOptions['customAvatarExt']=$avatar['ext'];
                    $newOptions['customAvatarFileTime']=$avatar['time'];
                    $needCleanCache=true;
                }
            }
        }

        if (isset($_POST['OGC_options']['resetDefaultAvatar'])) {
            $newOptions['defaultAvatar']=true;
            $newOptions['customAvatarExt']="svg";
            $newOptions['customAvatarFileTime']=filemtime($this->pluginDirectory . '/avatar/default.svg');
            $needCleanCache=true;
        }

        if (!$this->firstSave) {
            $this->errorMessages[]=array(
            "type" => "error notice",
            "message" => __("Please set the cache first.", "OGC"),
            "args"=>array()
            );
            $needCleanCache=false;
        }

        if ($needCleanCache==true) {
            $this->defaultAvatar=$newOptions['defaultAvatar'];
            $this->customAvatarExt=$newOptions['customAvatarExt'];
            $this->customAvatarFileTime=$newOptions['customAvatarFileTime'];

            $this->updateDefaultAvatarsExt();
            $this->deleteOldCustomAvatar();
            $this->deleteDefaultAvatars();
            $this->createMissingDefaultAvatarSizes();

            $newOptions = wp_parse_args($newOptions, $this->options);
            update_option('OGC_options', $newOptions);
        }

        if ($valideInput) {
            $this->errorMessages[]=array(
            "type" => "notice notice-success",
            "message" => __("Options have been updated.", "OGC"),
            "args"=>array()
            );
        }
    }

    protected function updateOptimizationOptions()
    {
        $valideInput=true;
        $newOptions=array();

        if (isset($_POST['OGC_options']['optimizeAvatars'])) {
            $newOptions['optimizeAvatars']=1;
        } else {
            $newOptions['optimizeAvatars']=0;
        }

        if (isset($_POST['OGC_options']['maxOptimizeEachTime'])) {
            $newOptions['maxOptimizeEachTime']=$_POST['OGC_options']['maxOptimizeEachTime'];
            if ((int)$newOptions['maxOptimizeEachTime'] < 1) {
                $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("<b>Number of cached Gravatars to optimise simultaneously</b>: Please enter a whole number greater than or equal to 1.", "OGC"),
                    "args"=>array()
                );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['precompress'])) {
            $newOptions['precompress']=1;
        } else {
            $newOptions['precompress']=0;
        }

        $this->optimizeAvatars=$newOptions['optimizeAvatars'];
        $this->maxOptimizeEachTime=$newOptions['maxOptimizeEachTime'];
        $this->precompress=$newOptions['precompress'];

        if (!$this->firstSave) {
            $this->errorMessages[]=array(
            "type" => "error notice",
            "message" => __("Please set the cache first.", "OGC"),
            "args"=>array()
            );
            $valideInput=false;
        }

        if ($valideInput) {
            if ($this->precompress) {
                $this->compressDefaultAvatar();
                $this->compressPlaceholder();
            } else {
                $this->deleteCompressDefaultAvatar();
                $this->deleteCompressPlaceholder();
            }

            $newOptions = wp_parse_args($newOptions, $this->options);
            update_option('OGC_options', $newOptions);

            $this->errorMessages[]=array(
            "type" => "notice notice-success",
            "message" => __("Options have been updated.", "OGC"),
            "args"=>array()
            );
        }
    }

    protected function updateOtherOptions()
    {
        if (isset($_POST['OGC_options']['useQueryStrings'])) {
            $newOptions['useQueryStrings']=1;
        } else {
            $newOptions['useQueryStrings']=0;
        }
        if (isset($_POST['OGC_options']['clearPageChachePluginsCache'])) {
            $newOptions['clearPageChachePluginsCache']=1;
        } else {
            $newOptions['clearPageChachePluginsCache']=0;
        }
        if (isset($_POST['OGC_options']['useSourceAsLongNotParsed'])) {
            $newOptions['useSourceAsLongNotParsed']=1;
        } else {
            $newOptions['useSourceAsLongNotParsed']=0;
        }
        if (isset($_POST['OGC_options']['removeFromCacheWhenRemovedFromGravatar'])) {
            $newOptions['removeFromCacheWhenRemovedFromGravatar']=1;
        } else {
            $newOptions['removeFromCacheWhenRemovedFromGravatar']=0;
        }
        if (isset($_POST['OGC_options']['sendUserAgent'])) {
            $newOptions['sendUserAgent']=1;
        } else {
            $newOptions['sendUserAgent']=0;
        }
        if (isset($_POST['OGC_options']['userAgent'])) {
            $newOptions['userAgent'] = sanitize_text_field($_POST['OGC_options']['userAgent']);
        }
        if (isset($_POST['OGC_options']['resolvHostFromContentURL'])) {
            $newOptions['resolvHostFromContentURL']=1;
        } else {
            $newOptions['resolvHostFromContentURL']=0;
        }
        if (isset($_POST['OGC_options']['learningMode'])) {
            $newOptions['learningMode']=1;
        } else {
            $newOptions['learningMode']=0;
        }
        if (isset($_POST['OGC_options']['additionalClasses'])) {
            $newOptions['additionalClasses']=array_map('trim', explode(' ', sanitize_text_field($_POST['OGC_options']['additionalClasses'])));
        }

        $this->useQueryStrings=$newOptions['useQueryStrings'];
        $this->clearPageChachePluginsCache=$newOptions['clearPageChachePluginsCache'];
        $this->sendUserAgent=$newOptions['sendUserAgent'];
        $this->userAgent=$newOptions['userAgent'];
        $this->resolvHostFromContentURL=$newOptions['resolvHostFromContentURL'];
        $this->learningMode=$newOptions['learningMode'];
        $this->removeFromCacheWhenRemovedFromGravatar=$newOptions['removeFromCacheWhenRemovedFromGravatar'];
        $this->additionalClasses=$newOptions['additionalClasses'];
        $this->useSourceAsLongNotParsed=$newOptions['useSourceAsLongNotParsed'];

        $newOptions = wp_parse_args($newOptions, $this->options);
        update_option('OGC_options', $newOptions);

        $this->errorMessages[]=array(
          "type" => "notice notice-success",
          "message" => __("Options have been updated.", "OGC"),
          "args"=>array()
        );
    }

    protected function updateLazyLoadOptions()
    {
        $valideInput=true;
        $newOptions=array();
        $updatePlaceholder=false;
        if (isset($_POST['OGC_options']['lazyLoad'])) {
            $newOptions['lazyLoad']=1;
        } else {
            $newOptions['lazyLoad']=0;
        }

        if (isset($_POST['OGC_options']['onlyOnTheFrontend']) && (int)$_POST['OGC_options']['onlyOnTheFrontend']==1) {
            $newOptions['onlyOnTheFrontend']=1;
        } else {
            $newOptions['onlyOnTheFrontend']=0;
        }

        if (isset($_POST['OGC_options']['scriptInFooter']) && (int)$_POST['OGC_options']['scriptInFooter']==1) {
            $newOptions['scriptInFooter']=1;
        } else {
            $newOptions['scriptInFooter']=0;
        }

        if (isset($_POST['OGC_options']['offset'])) {
            $newOptions['offset']=(int)$_POST['OGC_options']['offset'];
            if ($newOptions['offset'] < 1) {
                $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("<b>Download Gravatars that are this distance from being scrolled into view</b>: The size must be greater than or equal to 1 pixel (1px).", "OGC"),
                    "args"=>array()
                  );
                $valideInput=false;
            }
        }

        if (isset($_POST['OGC_options']['notInThisClass'])) {
            $newOptions['notInThisClass']=sanitize_text_field($_POST['OGC_options']['notInThisClass']);
        }

        if (isset($_POST['OGC_options']['usePlaceholder']) && (int)$_POST['OGC_options']['usePlaceholder']==1) {
            $newOptions['usePlaceholder']=1;
        } else {
            $newOptions['usePlaceholder']=0;
        }

        if (isset($_FILES['file'])) {
            if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
                $placeholder=$this->adminSaveCustomAvatar("placeholder");

                if ($placeholder) {
                    $placeholderSrc=ABSPATH.$this->cacheDirectory."custom/placeholder.".$placeholder['ext'];
                    touch($placeholderSrc, $placeholder['time']);
                    chmod($placeholderSrc, base_convert($this->avatarsMode, 8, 10));

                    $newOptions['placeholderURL']="{$this->cacheURL}_.".$placeholder['ext'];
                    $newOptions['placeholderFileTime']=$placeholder['time'];
                    $newOptions['defaultPlaceholder']=0;
                    $newOptions['placeholderExt']=$placeholder['ext'];

                    $updatePlaceholder=true;
                }
            } else {
                $valideInput=false;

                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Please set the cache first.", "OGC"),
                "args"=>array()
                );
            }
        }

        if ($valideInput) {
            $this->lazyLoad=$newOptions['lazyLoad'];
            $this->scriptInFooter=$newOptions['scriptInFooter'];
            $this->offset=$newOptions['offset'];
            $this->notInThisClass=$newOptions['notInThisClass'];
            $this->usePlaceholder=$newOptions['usePlaceholder'];
            $this->onlyOnTheFrontend=$newOptions['onlyOnTheFrontend'];


            if ($updatePlaceholder) {
                $this->placeholderURL=$newOptions['placeholderURL'];
                $this->placeholderFileTime=$newOptions['placeholderFileTime'];
                $this->defaultPlaceholder=$newOptions['defaultPlaceholder'];
                $this->placeholderExt=$newOptions['placeholderExt'];
                $this->deleteOldCustomPlaceholder();
                $valideInput=$this->createPlaceholder();
                $this->deleteUnneededPlaceholder();
                $this->compressPlaceholder();
            }
        }
        if ($valideInput) {
            $newOptions = wp_parse_args($newOptions, $this->options);
            update_option('OGC_options', $newOptions);

            $this->errorMessages[]=array(
            "type" => "notice notice-success",
            "message" => __("Options have been updated.", "OGC"),
            "args"=>array()
            );
        }
    }

    protected function updateCloudflareOptions()
    {
        $valideInput=true;
        $newOptions=array();
        if (isset($_POST['OGC_options']['clearCloudflareCache'])) {
            $newOptions['clearCloudflareCache']=1;
        } else {
            $newOptions['clearCloudflareCache']=0;
        }

        if (isset($_POST['OGC_options']['clearCloudflareCacheAuthEmail'])) {
            $newOptions['clearCloudflareCacheAuthEmail']=sanitize_email($_POST['OGC_options']['clearCloudflareCacheAuthEmail']);
        }
        if (isset($_POST['OGC_options']['clearCloudflareCacheAuthKey'])) {
            $newOptions['clearCloudflareCacheAuthKey']=sanitize_text_field($_POST['OGC_options']['clearCloudflareCacheAuthKey']);
        }

        if ($newOptions['clearCloudflareCache']) {
            $verifyZone=$this->verifyCloudflareZone($newOptions['clearCloudflareCacheAuthEmail'], $newOptions['clearCloudflareCacheAuthKey']);
            if ($verifyZone->error ==true && $verifyZone->authVerified ==false) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Could not communicate with Cloudflare. Please check your email and your global key.", "OGC"),
                "args"=>array()
                );
                $valideInput=false;
            }
            if ($verifyZone->error ==false && $verifyZone->zoneVerified==false) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("There is no domain '%s' registered in your Cloudflare account.", "OGC"),
                "args"=>array($this->host)
                );
                $valideInput=false;
            }
        }

        if ($valideInput && $newOptions['clearCloudflareCache']) {
            $newOptions['clearCloudflareCacheZoneID']=$verifyZone->zoneID;
            $this->clearCloudflareCacheZoneID=$newOptions['clearCloudflareCacheZoneID'];
        } else {
            $newOptions['clearCloudflareCacheZoneID']="";
            $this->clearCloudflareCacheZoneID=$newOptions['clearCloudflareCacheZoneID'];
        }

        $this->clearCloudflareCache=$newOptions['clearCloudflareCache'];
        $this->clearCloudflareCacheAuthEmail=$newOptions['clearCloudflareCacheAuthEmail'];
        $this->clearCloudflareCacheAuthKey=$newOptions['clearCloudflareCacheAuthKey'];

        if ($valideInput) {
            $newOptions = wp_parse_args($newOptions, $this->options);
            update_option('OGC_options', $newOptions);

            $this->errorMessages[]=array(
            "type" => "notice notice-success",
            "message" => __("Options have been updated.", "OGC"),
            "args"=>array()
            );
        }
    }

    protected function updateApacheConfiguration()
    {
        $noError=true;

        if (isset($_POST['OGC_options']['htaccessMode'])) {
            $newOptions['htaccessMode'] = sanitize_text_field($_POST['OGC_options']['htaccessMode']);
            if (!$this->validatePermissionsMode($newOptions['htaccessMode'])) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("<b>.htaccess file permissions</b>: Please enter an octal value for the permissions to be used for the .htaccess file.", "OGC"),
                "args"=>array()
                );
                $noError=false;
            }
        }

        $apacheHtaccess = stripcslashes($_POST['OGC_options']['apacheHtaccess']);
        if ($noError && isset($_POST['OGC_options']['updateHtaccess'])) {
            if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
                if (file_put_contents(ABSPATH."{$this->cacheDirectory}.htaccess", $apacheHtaccess) === false) {
                    $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("Could not write to/update the .htaccess file '%s.htaccess'.", "OGC"),
                    "args"=>array(ABSPATH.$this->cacheDirectory)
                    );
                    $noError=false;
                }
            }
        }

        if ($noError && isset($_POST['OGC_options']['htaccessMode'])) {
            if ($this->firstSave && file_exists(ABSPATH."{$this->cacheDirectory}.htaccess")) {
                if (!chmod(ABSPATH."{$this->cacheDirectory}.htaccess", base_convert($newOptions['htaccessMode'], 8, 10))) {
                    $this->errorMessages[]=array(
                          "type" => "error notice",
                          "message" => __("Could not amend the permissions for '%s' in the .htaccess file '%s.htaccess'.", "OGC"),
                          "args"=>array($this->htaccessMode, ABSPATH.$this->cacheDirectory)
                        );
                    $noError=false;
                }
            }
        }

        if ($noError && isset($_POST['OGC_options']['deleteHtaccess'])) {
            if ($this->firstSave && file_exists(ABSPATH."{$this->cacheDirectory}.htaccess")) {
                if (!unlink(ABSPATH."{$this->cacheDirectory}.htaccess")) {
                    $this->errorMessages[]=array(
                      "type" => "error notice",
                      "message" => __("Could not remove the .htaccess file '%s.htaccess'.", "OGC"),
                      "args"=>array(ABSPATH.$this->cacheDirectory)
                    );
                    $noError=false;
                }
            }
        }

        if (!$this->firstSave) {
            $this->errorMessages[]=array(
            "type" => "error notice",
            "message" => __("Please set the cache first.", "OGC"),
            "args"=>array()
            );
            $noError=false;
        }

        if ($noError) {
            $this->htaccessMode=$newOptions['htaccessMode'];
            $newOptions['apacheConfigSaved']=1;



            update_option('OGC_apacheConfig', $apacheHtaccess);

            $newOptions = wp_parse_args($newOptions, $this->options);
            update_option('OGC_options', $newOptions);

            $this->errorMessages[]=array(
              "type" => "notice notice-success",
              "message" => __("Options have been updated.", "OGC"),
              "args"=>array()
            );
        }
    }

    protected function readDefaultApacheConfig()
    {
        return file_get_contents($this->pluginDirectory . '/apache/htaccess');
    }

    public function adminSaveCustomAvatar($name)
    {
        if (!isset($_FILES['file'])) {
            return false;
        }
        if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
            return false;
        }

        switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            $ext=pathinfo(strtolower($_FILES["file"]["name"]), PATHINFO_EXTENSION);
            if (array_key_exists($_FILES["file"]["type"], $this->mimeTypes) && in_array($ext, $this->mimeTypes)) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], ABSPATH.$this->cacheDirectory."/custom/{$name}.{$ext}")) {
                    $options=array(
                      'name'=> $name,
                      'default'=>false,
                      'time'=>filemtime(ABSPATH.$this->cacheDirectory."/custom/{$name}.{$ext}"),
                      'ext'=>$ext,
                    );
                    return $options;
                } else {
                    $this->errorMessages[]=array(
                          "type" => "error notice",
                          "message" => __("Gravatar could not be saved. Please check file/folder permissions.", "OGC"),
                          "args"=>array()
                        );
                }
            } else {
                $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("The file type is not supported.", "OGC"),
                    "args"=>array()
                  );
            }
            break;
        case UPLOAD_ERR_INI_SIZE:
            $this->errorMessages[]=array(
              "type" => "error notice",
              "message" => __("The uploaded file exceeds the upload_max_filesize directive in php.ini.", "OGC"),
              "args"=>array()
            );
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.", "OGC"),
                    "args"=>array()
                  );
            break;
        case UPLOAD_ERR_PARTIAL:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("The uploaded file was only partially uploaded.", "OGC"),
                    "args"=>array()
                  );
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("Missing a temporary folder.", "OGC"),
                    "args"=>array()
                  );
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("Failed to write file to disk.", "OGC"),
                    "args"=>array()
                  );
            break;
        case UPLOAD_ERR_EXTENSION:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("File upload stopped by extension.", "OGC"),
                    "args"=>array()
                  );
            break;
        default:
            $this->errorMessages[]=array(
                    "type" => "error notice",
                    "message" => __("Unknown upload error.", "OGC"),
                    "args"=>array()
                  );
            break;
        }

        return false;
    }

    protected function makeCacheDirectory()
    {
        $oldumask = umask(0);
        if (!is_dir(ABSPATH.$this->cacheDirectory)) {
            if (!mkdir(ABSPATH.$this->cacheDirectory)) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Could not create directory '%s'.<br>Please make sure you have write permissions on directory '%s'.", "OGC"),
                "args"=>array(ABSPATH.$this->cacheDirectory, dirname(ABSPATH.$this->cacheDirectory, 1))
                );
                umask($oldumask);
                return false;
            }
        }
        if (is_dir(ABSPATH.$this->cacheDirectory) && !is_dir(ABSPATH.$this->cacheDirectory.'tmp')) {
            if (!mkdir(ABSPATH.$this->cacheDirectory.'tmp')) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Could not create the directory '%s'.", "OGC"),
                "args"=>array(ABSPATH.$this->cacheDirectory.'tmp')
                );
                umask($oldumask);
                return false;
            }
        }
        if (is_dir(ABSPATH.$this->cacheDirectory) && !is_dir(ABSPATH.$this->cacheDirectory.'custom')) {
            if (!mkdir(ABSPATH.$this->cacheDirectory.'custom')) {
                $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Could not create the directory '%s'.", "OGC"),
                "args"=>array(ABSPATH.$this->cacheDirectory.'custom')
                );
                umask($oldumask);
                return false;
            }
        }
        umask($oldumask);
        return true;
    }

    protected function setCachePermissions()
    {
        if (!chmod(ABSPATH.$this->cacheDirectory, base_convert($this->directoriesMode, 8, 10))) {
            $this->errorMessages[]=array(
              "type" => "error notice",
              "message" => __("Could not apply the permissions '%s' to the '%s' directory.", "OGC"),
              "args"=>array($this->directoriesMode,ABSPATH.$this->cacheDirectory)
            );
            return false;
        }
        if (!chmod(ABSPATH.$this->cacheDirectory."tmp", base_convert($this->directoriesMode, 8, 10))) {
            $this->errorMessages[]=array(
              "type" => "error notice",
              "message" => __("Could not apply the permissions '%s' to the '%s' directory.", "OGC"),
              "args"=>array($this->directoriesMode,ABSPATH.$this->cacheDirectory."tmp")
            );
            return false;
        }
        if (!chmod(ABSPATH.$this->cacheDirectory."custom", base_convert($this->directoriesMode, 8, 10))) {
            $this->errorMessages[]=array(
              "type" => "error notice",
              "message" => __("Could not apply the permissions '%s' to the '%s' directory.", "OGC"),
              "args"=>array($this->directoriesMode,ABSPATH.$this->cacheDirectory."custom")
            );
            return false;
        }
        $files = glob(ABSPATH.$this->cacheDirectory.'*', GLOB_BRACE);
        $errorFiles=array();
        $countError=0;
        foreach ($files as $i => $file) {
            if (!is_dir($file)) {
                if (!chmod($file, base_convert($this->avatarsMode, 8, 10))) {
                    if ($countError<6) {
                        $errorFiles[]=$file;
                    }
                    $countError++;
                }
            }
        }
        if ($countError) {
            $this->errorMessages[]=array(
            "type" => "error notice",
            "message" => __("Could not apply the permissions '%s' to '%s' files.<br>The first:%s", "OGC"),
            "args"=>array($this->avatarsMode,$countError,"<ul><li>".implode('</li><li>', $errorFiles)."</li></ul>...")
            );
            return false;
        }
        if (file_exists(ABSPATH."{$this->cacheDirectory}.htaccess") && !chmod(ABSPATH."{$this->cacheDirectory}.htaccess", base_convert($this->htaccessMode, 8, 10))) {
            $this->errorMessages[]=array(
              "type" => "error notice",
              "message" => __("Could not apply permissions '%s' to the file '%s'.", "OGC"),
              "args"=>array($this->htaccessMode,ABSPATH."{$this->cacheDirectory}.htaccess")
            );
            return false;
        }
        return true;
    }

    protected function hasCronEnabled()
    {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            $this->errorMessages[]=array(
                "type" => "notice notice-warning",
                "message" => __("WP Cron is inactive in your configuration, if you use cron of your system to run your WP Cron tasks, this warning is not important, otherwise it is necessary to activate WP Cron.", "OGC"),
                "args"=>array()
              );
            return false;
        }
        return true;
    }
    protected function adminHasPermissionsToRun()
    {
        $cacheDir=ABSPATH.$this->cacheDirectory;
        $cacheDirTmp=$cacheDir."tmp";
        $cacheDirCustom=$cacheDir."custom";
        $noError=true;

        clearstatcache();

        if ($noError && !is_dir($cacheDir)) {
            $this->options['messages']['no-cache-dir'] = array(
              "type" => "error notice",
              "message" => __("The cache directory '%s' does not exist.", "OGC"),
              "args"=>array($cacheDir)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-cache-dir'])) {
                unset($this->options['messages']['no-cache-dir']);
            }
        }
        if ($noError && !is_writable($cacheDir)) {
            $this->options['messages']['no-writable-cache-dir'] = array(
              "type" => "error notice",
              "message" => __("The cache directory '%s' does not have write permissions.", "OGC"),
              "args"=>array($cacheDir)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-writable-cache-dir'])) {
                unset($this->options['messages']['no-writable-cache-dir']);
            }
        }
        if ($noError && !is_executable($cacheDir)) {
            $this->options['messages']['no-executable-cache-dir'] = array(
              "type" => "error notice",
              "message" => __("The cache directory '%s' does not have file read/list permissions.", "OGC"),
              "args"=>array($cacheDir)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-executable-cache-dir'])) {
                unset($this->options['messages']['no-executable-cache-dir']);
            }
        }
        if ($noError && !is_dir($cacheDirTmp)) {
            $this->options['messages']['no-cache-tmp-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory '%s', which is used to store temporary files for the cache, does not exist.", "OGC"),
              "args"=>array($cacheDirTmp)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-cache-tmp-dir'])) {
                unset($this->options['messages']['no-cache-tmp-dir']);
            }
        }
        if ($noError && !is_writable($cacheDirTmp)) {
            $this->options['messages']['no-writable-cache-tmp-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory '%s', which is used to store temporary files for the cache, does not have write permissions.", "OGC"),
              "args"=>array($cacheDirTmp)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-writable-cache-tmp-dir'])) {
                unset($this->options['messages']['no-writable-cache-tmp-dir']);
            }
        }
        if ($noError && !is_executable($cacheDirTmp)) {
            $this->options['messages']['no-executable-cache-tmp-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory '%s', which is used to store temporary files for the cache, does not have file read/list permissions.", "OGC"),
              "args"=>array($cacheDirTmp)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-executable-cache-tmp-dir'])) {
                unset($this->options['messages']['no-executable-cache-tmp-dir']);
            }
        }
        if ($noError && !is_dir($cacheDirCustom)) {
            $this->options['messages']['no-cache-custom-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory to create custom files from cache '%s' does not exist.", "OGC"),
              "args"=>array($cacheDirCustom)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-cache-custom-dir'])) {
                unset($this->options['messages']['no-cache-custom-dir']);
            }
        }
        if ($noError && !is_writable($cacheDirCustom)) {
            $this->options['messages']['no-writable-cache-custom-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory to create custom files from cache '%s' does not allow writing.", "OGC"),
              "args"=>array($cacheDirCustom)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-writable-cache-custom-dir'])) {
                unset($this->options['messages']['no-writable-cache-custom-dir']);
            }
        }
        if ($noError && !is_executable($cacheDirCustom)) {
            $this->options['messages']['no-executable-cache-custom-dir'] = array(
              "type" => "error notice",
              "message" => __("The directory for creating custom files from the cache '%s' does not allow listing the files.", "OGC"),
              "args"=>array($cacheDirCustom)
            );
            $noError=false;
        } else {
            if (isset($this->options['messages']['no-executable-cache-custom-dir'])) {
                unset($this->options['messages']['no-executable-cache-custom-dir']);
            }
        }

        update_option('OGC_options', $this->options);
        return $noError;
    }

    protected function hasPermissionsToRun()
    {
        clearstatcache();
        if (!is_dir(ABSPATH.$this->cacheDirectory)) {
            return false;
        }
        if (!is_writable(ABSPATH.$this->cacheDirectory)) {
            return false;
        }
        if (!is_executable(ABSPATH.$this->cacheDirectory)) {
            return false;
        }
        if (!is_dir(ABSPATH."{$this->cacheDirectory}tmp")) {
            return false;
        }
        if (!is_writable(ABSPATH."{$this->cacheDirectory}tmp")) {
            return false;
        }
        if (!is_executable(ABSPATH."{$this->cacheDirectory}tmp")) {
            return false;
        }
        if (!is_dir(ABSPATH."{$this->cacheDirectory}custom")) {
            return false;
        }
        if (!is_writable(ABSPATH."{$this->cacheDirectory}custom")) {
            return false;
        }
        if (!is_executable(ABSPATH."{$this->cacheDirectory}custom")) {
            return false;
        }
        return true;
    }

    public function adminPermissionsNotices()
    {
        if (count($this->options['messages'])) {
            $this->errorMessages = array_merge($this->options['messages'], $this->errorMessages);
        }

        if (count($this->errorMessages)) {
            foreach ($this->errorMessages as $index => $contents) {
                echo "<div class=\"{$contents['type']}\"><p><b>{$this->pluginName}</b></p><p>".vsprintf($contents['message'], $contents['args'])."</p></div>";
            }
        }
    }

    public function getBPressCachedAvatarURL($url, $params)
    {
        if ($params['object'] == 'user' && $params['class'] == 'avatar') {
            return $this->getCachedAvatar($url, $params['item_id'], $params['width'], null, $params['alt'], false, false, false);
        }
        return $url;
    }


    public function getBPressCachedAvatar($source, $params, $id)
    {
        if (is_array($params) && $params['object'] == 'user') {
            return $this->getWordPressCachedAvatar($source, $id, $params['width'], null, $params['alt'], $params);
        }
        return $source;
    }

    public function getWordPressCachedAvatarURL($url, $id_or_email, $args)
    {
        return $this->getCachedAvatar($url, $id_or_email, $args['width'], null, false, false, false, false);
    }

    public function deleteWordPressCachedAvatarSRCSet($source, $idOrEmail, $size, $default, $alt, $args)
    {
        $source=preg_replace('/(.*)\ssrcset=[\'"](?P<srcset>.+?)[\'"](.*)/i', "$1$3", $source);
        return $source;
    }

    public function getWordPressCachedAvatar($source, $idOrEmail, $size, $default, $alt, $args)
    {
        $classSize=array('avatar-'.$size);
        $argClassArray=array();
        if ($args['class']  && is_array($args['class'])) {
            $argClassArray=$args['class'];
        }
        if ($args['class']  && is_string($args['class'])) {
            $argClassArray=explode(' ', $args['class']);
        }

        $class=array_unique(array_merge($argClassArray, $classSize, $this->additionalClasses), SORT_REGULAR);

        $useLazy=true;
        if (in_array($this->notInThisClass, $class) || !$this->useLazyOnAdminPages) {
            $useLazy=false;
        }

        $classNames=implode(' ', $class);
        $url=$this->getCachedAvatar($source, $idOrEmail, $size, $default, $alt, $classNames, true, $useLazy);

        return $url;
    }


    public function overrideAllowedBuddyPressTags($allowedTags)
    {
        $allowedTags['img']['data-src'] = array();

        return $allowedTags;
    }


    // Activate plugin and update default option
    public function activate()
    {
        global $wpdb;

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `{$this->cacheTableName}` (
				  `id` int(10) UNSIGNED NOT NULL auto_increment,
				  `hash` char(32) NOT NULL,
				  `optimized` enum('0','1') NOT NULL,
				  `size` smallint(5) UNSIGNED NOT NULL,
				  `ext` enum('svg','jpg','png','gif') NOT NULL,
				  `lastCheck` int(10) UNSIGNED NOT NULL,
				  `lastModified` int(10) UNSIGNED NOT NULL,
				  `def` enum('0','1') NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `unicSize` (`hash`,`size`),
          KEY `optimizedAvatar` (`optimized`,`def`),
          KEY `getIdByHashAndSize` (`hash`,`size`) USING BTREE,
          KEY `DistinctDefault` (`def`) USING BTREE,
          KEY `lastCheck` (`lastCheck`)
				)"
        );

        // remove debug table from 1.4.0 and 1.4.1
        $wpdb->query("DROP TABLE IF EXISTS `{$this->cacheTableName}_urls`;");

        $avatarUsedSizes=get_option('OGC_avatarUsedSizes');
        if ($avatarUsedSizes == false) {
            $avatarUsedSizes=array(96, 64, 50, 32, 26, 20);
        }

        $resolved=get_option('OGC_resolved');
        if ($resolved == false) {
            $resolved=0;
        }

        $options=get_option('OGC_options');
        if (!$options || !$options['apacheConfigSaved']) {
            $apacheConfig=$this->readDefaultApacheConfig();
            update_option('OGC_apacheConfig', $apacheConfig);
        }

        $defaultOptions = array(
            'activated'   => 0,
            'expireTime' => 10,
            'cacheDirectory' => 'wp-content/uploads/optimum-gravatar-cache/',
            'searchExpiredTime'=> 5,
            'maxUpdateEachTime' => 10,
            'maxOptimizeEachTime' => 10,
            'precompress' => 0,
            'messages' => array(),
            'defaultAvatar'=> true,
            'customAvatarExt'=>  'svg',
            'customAvatarFileTime' => filemtime($this->pluginDirectory . '/avatar/default.svg'),
            'optimizeAvatars'=> 0,
            'learningMode' => 0,
            'apacheConfigSaved' => 0,
            'useQueryStrings' => 0,
            'sendUserAgent' => 0,
            'userAgent'=> "Optimum Gravatar Cache at ".site_url(),
            'resolvHostFromContentURL'=>0,
            'directoriesMode'=>'0777',
            'avatarsMode'=>'0777',
            'htaccessMode'=>'0777',
            'removeFromCacheWhenRemovedFromGravatar' => 0,
            'additionalClasses'=>array('avatar','photo'),
            'lazyLoad' => 0,
            'scriptInFooter' => 0,
            'onlyOnTheFrontend' => 0,
            'notInThisClass'=> '',
            'usePlaceholder' => 0,
            'offset'=> 300,
            'placeholderURL'=> plugins_url('/avatar/placeholder.svg', __FILE__),
            'placeholderFileTime'=> filemtime($this->pluginDirectory . '/avatar/placeholder.svg'),
            'defaultPlaceholder' => 1,
            'placeholderExt' => 'svg',
            'synchronizedEmail' => 0,
            'currentVersion' => sprintf("%s", $this->pluginVersion),
            'firstSave'=> 0,
            'useSourceAsLongNotParsed'=> 1,
            'clearPageChachePluginsCache' => 0,
            'clearCloudflareCache' => 0,
            'clearCloudflareCacheAuthEmail' => '',
            'clearCloudflareCacheAuthKey' => '',
            'clearCloudflareCacheZoneID' => '',
        );

        $options['messages']=array();

        if ($this->adminPhpModulesRequired()) {
            if (empty($options['firstSave'])) {
                $options['messages']['firstSave'] = array(
                    'type' => "notice notice-info",
                    'message' => __("The plugin has been activated, but requires some configuration to work. You can access the configuration page through the menu. You need to specify the directory in which the Gravatars will be saved, as a minimum.", "OGC"),
                    'args'=>array()
                  );
            }
        }

        $options['currentVersion']=$this->pluginVersion;

        $newOptions = wp_parse_args($options, $defaultOptions);

        wp_schedule_event(time(), 'OGC_job', 'OGC_CronEvent');

        update_option('OGC_avatarUsedSizes', $avatarUsedSizes);
        update_option('OGC_resolved', $resolved);
        update_option('OGC_options', $newOptions);

        $this->resolved=$resolved;
        $this->options = $newOptions;
        $this->avatarUsedSizes = $avatarUsedSizes;
    }

    // Deactivate plugin
    public function deactivate()
    {
        wp_clear_scheduled_hook('OGC_CronEvent');
    }

    protected function getResource($url, $noBody, $postData, $headers, &$connection)
    {
        $properties = new stdClass();
        $properties->error=false;

        if ($connection === false) {
            $connection = curl_init();
        }

        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($connection, CURLOPT_VERBOSE, 1);
        curl_setopt($connection, CURLOPT_TIMEOUT, 600);
        if ($this->sendUserAgent) {
            curl_setopt($connection, CURLOPT_USERAGENT, $this->userAgent);
        }

        if ($postData && !empty($postData)) {
            curl_setopt($connection, CURLOPT_POST, 1);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $postData);
        }
        if ($headers && !empty($headers)) {
            curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($connection, CURLOPT_HEADER, true);
        curl_setopt($connection, CURLOPT_NOBODY, $noBody);
        curl_setopt($connection, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($connection, CURLOPT_FILETIME, true);

        $response    = curl_exec($connection);

        if ($response === false) {
            $properties->error=true;
        } else {
            $properties->status = curl_getinfo($connection, CURLINFO_HTTP_CODE);

            if ($properties->status == 200) {
                $properties->contentType=curl_getinfo($connection, CURLINFO_CONTENT_TYPE);
                if (isset($this->mimeTypes[$properties->contentType])) {
                    $properties->ext = $this->mimeTypes[$properties->contentType];
                } else {
                    $properties->ext = null;
                }
                $properties->lastModified = curl_getinfo($connection, CURLINFO_FILETIME);
                $properties->content = substr($response, curl_getinfo($connection, CURLINFO_HEADER_SIZE));
            }
        }
        return $properties;
    }

    protected function getGravatarOnline($options)
    {
        return $this->getResource("https://www.gravatar.com/avatar/{$options}", false, null, null, $this->curl);
    }
    protected function getGravatarStatusOnline($options)
    {
        return $this->getResource("https://www.gravatar.com/avatar/{$options}", true, null, null, $this->curl);
    }

    protected function sendCloudflareRequest($urls)
    {
        $headers = array("Content-Type: application/json", "X-Auth-Email: {$this->clearCloudflareCacheAuthEmail}", "X-Auth-Key: {$this->clearCloudflareCacheAuthKey}");
        $json = new stdClass();
        $json->files=$urls;
        $data=json_encode($json);

        $properties=$this->getResource("https://api.cloudflare.com/client/v4/zones/{$this->clearCloudflareCacheZoneID}/purge_cache", false, $data, $headers, $this->curlCloudflare);

        if (!$properties->error && $properties->status == 200 && $properties->contentType == 'application/json') {
            $data=json_decode($properties->content);
            if ($data !== null) {
                if (isset($data->success) && $data->success !==true) {
                    $properties->error = true;
                }
            } else {
                $properties->error = true;
            }
        } else {
            $properties->error = true;
        }
        return $properties;
    }

    protected function verifyCloudflareZone($authEmail, $authKey)
    {
        $headers = array("Content-Type: application/json", "X-Auth-Email: {$authEmail}", "X-Auth-Key: {$authKey}");

        $properties=$this->getResource("https://api.cloudflare.com/client/v4/zones", false, null, $headers, $this->curlCloudflare);

        $properties->zoneVerified = false;
        $properties->authVerified = false;

        if (!$properties->error) {
            if ($properties->status == 200) {
                if ($properties->contentType == 'application/json') {
                    $data=json_decode($properties->content);
                    if ($data !== null) {
                        if (isset($data->success) && $data->success !==true) {
                            $properties->error = true;
                        } else {
                            $properties->authVerified = true;
                            foreach ($data->result as $zone) {
                                if (strpos($this->host, $zone->name) !== false) {
                                    $properties->zoneID = $zone->id;
                                    $properties->zoneVerified = true;
                                }
                            }
                        }
                    } else {
                        $properties->error = true;
                    }
                } else {
                    $properties->error = true;
                }
            } else {
                $properties->error = true;
            }
        }
        return $properties;
    }

    protected function sendResmushRequest($file)
    {
        if (function_exists('curl_file_create')) {
            $curlFile = curl_file_create($file);
        } else {
            $curlFile = '@' . realpath($file);
        }
        $data=array('files' => $curlFile);
        $properties=$this->getResource("http://api.resmush.it/ws.php", false, $data, null, $this->curlOptimize);

        if (!$properties->error && $properties->status == 200 && $properties->contentType == 'application/json') {
            $data=json_decode($properties->content);
            if ($data !== null) {
                if (isset($data->error)) {
                    $properties->error = true;
                } else {
                    if (isset($data->dest)) {
                        $properties->optimizedURL = $data->dest;
                    } else {
                        $properties->error = true;
                    }
                }
            } else {
                $properties->error = true;
            }
        } else {
            $properties->error = true;
        }
        return $properties;
    }

    protected function getOptimizedAvatar($url)
    {
        return $this->getResource($url, false, null, null, $this->curlOptimize);
    }

    protected function updateAndResizeAllAvatarsSizes($hash)
    {
        global $wpdb;

        // avatars sizes used
        $sql = "SELECT DISTINCT(`size`), id FROM `{$this->cacheTableName}` WHERE `hash` = '$hash' ORDER BY `size` DESC";
        $sizeResults = $wpdb->get_results($sql, OBJECT);
        $maxValue = max($this->avatarUsedSizes);
        $options = "{$hash}?s={$maxValue}&r={$this->avatarRating}&d=404";
        $newGravatar=$this->getGravatarOnline($options);

        if (!$newGravatar->error && $newGravatar->status == 200) {
            $tmpAvatar=ABSPATH."{$this->cacheDirectory}tmp/{$hash}.{$newGravatar->ext}";
            file_put_contents($tmpAvatar, $newGravatar->content);
            $img = wp_get_image_editor($tmpAvatar);
            if (! is_wp_error($img)) {
                $avatarsOptions = array();
                foreach ($this->avatarUsedSizes as $size) {
                    $avatarsOptions[] =array('width' => $size, 'height' => $size, 'crop' => true);
                }
                $resize = $img->set_quality(100);
                $resize = $img->multi_resize($avatarsOptions);
                if (!$resize) {
                    return false;
                }
            } else {
                return false;
            }

            rename($tmpAvatar, ABSPATH."{$this->cacheDirectory}tmp/{$hash}-{$maxValue}x{$maxValue}.{$newGravatar->ext}");
            foreach ($sizeResults as $size) {
                $avatarId=base_convert($size -> id, 10, 35);
                $resizedAvatar=ABSPATH."{$this->cacheDirectory}{$avatarId}.{$newGravatar->ext}";
                rename(ABSPATH."{$this->cacheDirectory}tmp/{$hash}-{$size->size}x{$size->size}.{$newGravatar->ext}", $resizedAvatar);
                touch($resizedAvatar, $newGravatar->lastModified);
                chmod($resizedAvatar, base_convert($this->avatarsMode, 8, 10));
                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}{$avatarId}.{$newGravatar->ext}";
            }

            if ($this->removeFromCacheWhenRemovedFromGravatar) {
                $this->deleteNotNeededUserAvatarsCacheFiles($hash, $newGravatar->ext);
            }
            $lastCheck = time();
            $wpdb->query("UPDATE `{$this->cacheTableName}` SET `optimized`='0', `lastCheck`={$lastCheck}, `def`='0', `ext`='{$newGravatar->ext}', `lastModified`={$newGravatar->lastModified} WHERE `hash`='{$hash}'");
            $this->hashsToCleanOnPageCachePlugins[]=$hash;
            return true;
        }
        return false;
    }

    protected function getUserAvatarCachedSizes($hash)
    {
        global $wpdb;

        $sql = "SELECT `size` FROM `{$this->cacheTableName}` WHERE `hash` = '$hash' ORDER BY `size` DESC";
        $sizeResults = $wpdb->get_results($sql, OBJECT);
        if ($sizeResults[0]) {
            $sizes=array();
            foreach ($sizeResults as $size) {
                $sizes[]=$size->size;
            }
            return $sizes;
        } else {
            return false;
        }
    }

    protected function createMissingDefaultAvatarSize($size)
    {
        global $wpdb;

        if ($this->customAvatarExt =="svg") {
            $avatarFile = ABSPATH.$this->cacheDirectory.'0.svg';
            if ($this->defaultAvatar) {
                $defaultAvatar=$this->pluginDirectory . '/avatar/default.svg';
                if (!copy($defaultAvatar, $avatarFile)) {
                    return false;
                }
            } else {
                $customAvatar=ABSPATH.$this->cacheDirectory.'custom/custom.svg';
                if (!copy($customAvatar, $avatarFile)) {
                    return false;
                }
            }
            chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
            touch($avatarFile, $this->customAvatarFileTime);
            $this->compressDefaultAvatar();

            if (class_exists("Imagick")) {
                if (file_exists($avatarFile)) {
                    try {
                        $im = new Imagick();
                        $im->setBackgroundColor(new ImagickPixel('transparent'));
                        $im->readimage($avatarFile);
                        $im->setImageFormat("png32");

                        $tmpGravatar=ABSPATH."{$this->cacheDirectory}tmp/0$size.png";
                        $avatarFileMD5=md5($tmpGravatar);
                        $sizeId=base_convert($size, 10, 35);
                        $failBackGravatar=ABSPATH."{$this->cacheDirectory}0{$sizeId}_{$avatarFileMD5}.png";

                        $im->resizeImage($size, $size, imagick::FILTER_LANCZOS, 1);
                        $im->writeimage($tmpGravatar);

                        rename($tmpGravatar, $failBackGravatar);
                        chmod($failBackGravatar, base_convert($this->avatarsMode, 8, 10));
                        touch($failBackGravatar, $this->customAvatarFileTime);
                        $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0{$sizeId}_{$avatarFileMD5}.png";
                    } catch (Exception $e) {
                        // svg support!
                    }
                }
            }
            return;
        } else {
            $customAvatar=ABSPATH.$this->cacheDirectory.'custom/custom.'.$this->customAvatarExt;
            $sizeId=base_convert($size, 10, 35);
            $avatarFile = ABSPATH."{$this->cacheDirectory}0{$sizeId}.{$this->customAvatarExt}";

            $avatar = wp_get_image_editor($customAvatar);
            if (! is_wp_error($avatar)) {
                $avatar->set_quality(100);
                $avatar->resize($size, $size, true);
                $avatar->save($avatarFile);
                chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
                touch($avatarFile, $this->customAvatarFileTime);
                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0{$sizeId}.{$this->customAvatarExt}";

                $avatarFileMD5=md5($avatarFile);
                $failBackGravatar=ABSPATH."{$this->cacheDirectory}0{$sizeId}_{$avatarFileMD5}.{$this->customAvatarExt}";
                if (copy($avatarFile, $failBackGravatar)) {
                    chmod($failBackGravatar, base_convert($this->avatarsMode, 8, 10));
                    touch($failBackGravatar, $this->customAvatarFileTime);
                    $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0{$sizeId}_{$avatarFileMD5}.{$this->customAvatarExt}";
                }
            } else {
                return;
            }
        }
    }

    protected function createMissingDefaultAvatarSizes()
    {
        if ($this->customAvatarExt =="svg") {
            $avatarFile = ABSPATH."{$this->cacheDirectory}0.svg";
            if (!file_exists($avatarFile)) {
                if ($this->defaultAvatar) {
                    $defaultAvatar=$this->pluginDirectory . '/avatar/default.svg';
                    if (!copy($defaultAvatar, $avatarFile)) {
                        return false;
                    }
                    touch($avatarFile, filemtime($defaultAvatar));
                } else {
                    $customAvatar=ABSPATH.$this->cacheDirectory.'custom/custom.svg';
                    if (!copy($customAvatar, $avatarFile)) {
                        return false;
                    }
                    touch($avatarFile, filemtime($customAvatar));
                }
                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0.svg";
                chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
                $this->compressDefaultAvatar();
            }


            if (class_exists("Imagick")) {
                if (file_exists($avatarFile)) {
                    try {
                        $im = new Imagick();
                        $im->setBackgroundColor(new ImagickPixel('transparent'));
                        $im->readimage($avatarFile);
                        $im->setImageFormat("png32");
                        $fileListExists = glob(ABSPATH.$this->cacheDirectory.'0*.{'.implode(",", array_values($this->mimeTypes)).'}', GLOB_BRACE);

                        foreach ($this->avatarUsedSizes as $size) {
                            $tmpGravatar=ABSPATH."{$this->cacheDirectory}tmp/0$size.png";

                            $avatarFileMD5=md5($tmpGravatar);
                            $sizeId=base_convert($size, 10, 35);
                            $failBackGravatar=ABSPATH."{$this->cacheDirectory}0{$sizeId}_{$avatarFileMD5}.png";

                            if (!in_array($failBackGravatar, $fileListExists)) {
                                $im->resizeImage($size, $size, imagick::FILTER_LANCZOS, 1);
                                $im->writeimage($tmpGravatar);

                                rename($tmpGravatar, $failBackGravatar);
                                chmod($failBackGravatar, base_convert($this->avatarsMode, 8, 10));
                                touch($failBackGravatar, $this->customAvatarFileTime);
                                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0{$sizeId}_{$avatarFileMD5}.png";
                            }
                        }
                    } catch (Exception $e) {
                        // svg support!
                    }
                }
            }
        } else {
            $customAvatar=ABSPATH.$this->cacheDirectory.'custom/custom.'.$this->customAvatarExt;
            $fileListExists = glob(ABSPATH.$this->cacheDirectory.'0*.{'.implode(",", array_values($this->mimeTypes)).'}', GLOB_BRACE);
            foreach ($this->avatarUsedSizes as $size) {
                $sizeId=base_convert($size, 10, 35);
                $avatarFile = ABSPATH."{$this->cacheDirectory}0$sizeId.{$this->customAvatarExt}";
                if (!in_array($avatarFile, $fileListExists)) {
                    $avatar = wp_get_image_editor($customAvatar);
                    if (! is_wp_error($avatar)) {
                        $avatar->set_quality(100);
                        $avatar->resize($size, $size, true);
                        $avatar->save($avatarFile);

                        chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
                        touch($avatarFile, $this->customAvatarFileTime);

                        $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0$sizeId.{$this->customAvatarExt}";
                    } else {
                        return;
                    }
                }

                $avatarFileMD5=md5($avatarFile);
                $failBackGravatar=ABSPATH."{$this->cacheDirectory}0{$sizeId}_{$avatarFileMD5}.{$this->customAvatarExt}";
                if (!in_array($failBackGravatar, $fileListExists)) {
                    if (copy($avatarFile, $failBackGravatar)) {
                        chmod($failBackGravatar, base_convert($this->avatarsMode, 8, 10));
                        touch($failBackGravatar, $this->customAvatarFileTime);
                        $this->urlsToClearOnCloudflare[]="{$this->cacheURL}0{$sizeId}_{$avatarFileMD5}.{$this->customAvatarExt}";
                    }
                }
            }
        }

        if (!defined('DOING_CRON')) {
            $this->getDefaultGravatarHashs();
        }
    }

    protected function deleteUnneededDefaultAvatarsSize()
    {
        global $wpdb;
        $neededAvatarSizes=implode(',', $this->avatarUsedSizes);

        $b10 = array_map(
            function ($n) {
                return base_convert($n, 10, 35);
            }, $this->avatarUsedSizes
        );

        $fileListALL = glob(ABSPATH.$this->cacheDirectory.'0*.{'.implode(",", array_values($this->mimeTypes)).'}', GLOB_BRACE);
        $fileListValid = glob(ABSPATH.$this->cacheDirectory.'0{'.implode(",", $b10).'}*.{'.implode(",", array_values($this->mimeTypes)).'}', GLOB_BRACE);
        $fileListValid[]= ABSPATH.$this->cacheDirectory.'0.svg';
        $fileListToRemove = array_diff($fileListALL, $fileListValid);

        foreach ($fileListToRemove as $file) {
            if (file_exists($file)) {
                unlink($file);
                if (file_exists($file.".O")) {
                    unlink($file.".O");
                }
            }
        }
        $wpdb->query("DELETE FROM `{$this->cacheTableName}` WHERE `def` = '1' AND `size` not in({$neededAvatarSizes})");
    }

    protected function deleteUnneededAvatarsSizes()
    {
        global $wpdb;
        $neededAvatarSizes=implode(',', $this->avatarUsedSizes);
        $sql = "SELECT `id`,`ext`,`def` FROM `{$this->cacheTableName}` WHERE `def` = '0' AND `size` NOT IN({$neededAvatarSizes})";
        $avatars = $wpdb->get_results($sql, OBJECT);

        foreach ($avatars as $avatar) {
            if ($avatar -> def == 0) {
                $b35Id=base_convert($avatar -> id, 10, 35);
                if (file_exists(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$avatar -> ext)) {
                    unlink(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$avatar -> ext);
                }
            }
        }
        $wpdb->query("DELETE FROM `{$this->cacheTableName}` WHERE `def` = '0' AND `size` not in({$neededAvatarSizes})");
    }

    protected function addMissingAvatarsSizes()
    {
        global $wpdb;
        $numberOfSizes=count($this->avatarUsedSizes);
        $sql = "SELECT * from(Select `hash`, count(`hash`) as h from `{$this->cacheTableName}` GROUP BY `hash` ) as t WHERE h < {$numberOfSizes}";
        $lackingHashs = $wpdb->get_results($sql, OBJECT);
        if (count($lackingHashs)) {
            foreach ($lackingHashs as $lackingHash) {
                $sizes=$this->getUserAvatarCachedSizes($lackingHash->hash);
                $maxSizeCached=max($sizes);
                $missingSizes = array_diff($this->avatarUsedSizes, $sizes);
                rsort($missingSizes);
                $maxSizeMissing=max($missingSizes);
                $needResizeALL=false;

                $sqlAvatarValues=array();
                $otherSize=$this->DBGetOtherAvatarSizeForUserCached($lackingHash->hash);

                if ($maxSizeMissing > $maxSizeCached) {
                    if ($otherSize) {
                        if ($otherSize->def==0) {
                            foreach ($missingSizes as $missingSize) {
                                $sqlAvatarValues[]=sprintf("('%s', '%s', %d, '%s', %d, %d, '%s')", $lackingHash->hash, '0', $missingSize, $otherSize->ext, $otherSize->lastCheck, $otherSize->lastModified, '0');
                            }
                            $needResizeALL=true;
                        } else {
                            foreach ($missingSizes as $missingSize) {
                                $sqlAvatarValues[]=sprintf("('%s', '%s', %d, '%s', %d, %d, '%s')", $lackingHash->hash, '0', $missingSize, $otherSize->ext, $otherSize->lastCheck, $otherSize->lastModified, '1');
                            }
                        }
                    }
                } else {
                    foreach ($missingSizes as $missingSize) {
                        if ($otherSize) {
                            if ($otherSize->def==0) {
                                $sqlAvatarValues[]=sprintf("('%s', '%s', %d, '%s', %d, %d, '%s')", $lackingHash->hash, '0', $missingSize, $otherSize->ext, $otherSize->lastCheck, $otherSize->lastModified, '0');
                            } else {
                                $sqlAvatarValues[]=sprintf("('%s', '%s', %d, '%s', %d, %d, '%s')", $lackingHash->hash, '0', $missingSize, $otherSize->ext, $otherSize->lastCheck, $otherSize->lastModified, '1');
                            }
                        }
                    }
                }

                $query="INSERT IGNORE INTO `{$this->cacheTableName}` (`hash`, `optimized`, `size`, `ext`, `lastCheck`, `lastModified`, `def`) VALUES ";
                $query .= implode(",\n", $sqlAvatarValues);
                $wpdb->query($query);

                if ($maxSizeMissing > $maxSizeCached && $needResizeALL) {
                    $this->updateAndResizeAllAvatarsSizes($lackingHash->hash);
                } else {
                    $this->hashsToCleanOnPageCachePlugins[]=$lackingHash->hash;
                    foreach ($missingSizes as $missingSize) {
                        if ($otherSize->def==0) {
                            $this->resizeCustomAvatarFromLargerCached($otherSize, $lackingHash->hash, $missingSize);
                        }
                    }
                }
            }
        }
    }

    protected function deleteNotNeededUserAvatarsCacheFiles($hash, $ext=false)
    {
        global $wpdb;
        $sql = "SELECT `id`, `ext` FROM `{$this->cacheTableName}` WHERE `hash` = '{$hash}' AND `def` = '0'";
        $avatars = $wpdb->get_results($sql, OBJECT);
        if (isset($avatars[0])) {
            if ($ext && $ext == $avatars[0] -> ext) {
                return;
            }
            foreach ($avatars as $avatar) {
                $b35Id=base_convert($avatar -> id, 10, 35);
                if (file_exists(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$avatar -> ext)) {
                    unlink(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$avatar -> ext);
                }
            }
        }
    }

    public function updateCache()
    {
        global $wpdb;
        $fp = fopen(ABSPATH."{$this->cacheDirectory}lock", 'w+');
        chmod(ABSPATH."{$this->cacheDirectory}lock", base_convert($this->avatarsMode, 8, 10));
        if (!flock($fp, LOCK_EX|LOCK_NB)) {
            return;
        }

        $this->deleteUnneededDefaultAvatarsSize();
        $this->createMissingDefaultAvatarSizes();

        $this->deleteUnneededAvatarsSizes();
        $this->addMissingAvatarsSizes();

        if (!$this->synchronizedEmail) {
            $this->addUsersAndCommentersToCache();
            $this->options['synchronizedEmail']=1;
            update_option('OGC_options', $this->options);
        }

        $time=time()-$this->expireTime * 86400;//86400 1 day
        $sql = "SELECT DISTINCT(`hash`), `def`, `lastModified`, `lastCheck`, `ext` FROM `{$this->cacheTableName}` WHERE lastCheck < {$time} ORDER BY `lastCheck` ASC LIMIT {$this->maxUpdateEachTime}";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results) {
            foreach ($results as $user) {
                $maxValue=max($this->avatarUsedSizes);
                $lastCheck = time();
                $options = "{$user->hash}?s={$maxValue}&r={$this->avatarRating}&d=404";
                $gravatarStatus=$this->getGravatarStatusOnline($options);
                if (!$gravatarStatus->error) {
                    if ($gravatarStatus->status == 404 && $user->def == 1 && $this->useSourceAsLongNotParsed && $user -> lastCheck == 0) {
                        $this->hashsToCleanOnPageCachePlugins[]=$user->hash;
                        $wpdb->query("UPDATE `{$this->cacheTableName}` SET `lastCheck`={$lastCheck} WHERE `hash`='{$user->hash}'");
                        continue;
                    } elseif ($gravatarStatus->status == 404 && $user->def == 1) {
                        $wpdb->query("UPDATE `{$this->cacheTableName}` SET `lastCheck`={$lastCheck} WHERE `hash`='{$user->hash}'");
                        continue;
                    } elseif ($gravatarStatus->status == 404 && $user->def == 0) {
                        if ($this->removeFromCacheWhenRemovedFromGravatar) {
                            $this->deleteNotNeededUserAvatarsCacheFiles($user->hash);
                        }
                        $this->hashsToCleanOnPageCachePlugins[]=$user->hash;
                        $wpdb->query("UPDATE `{$this->cacheTableName}` SET `optimized`='0', `lastCheck`={$lastCheck}, `def`='1', `lastModified`=0 WHERE `hash`='{$user->hash}'");
                        continue;
                    } elseif ($gravatarStatus->status == 200 && $user->def == 0) {
                        if ($user->lastModified == $gravatarStatus->lastModified) {
                            $wpdb->query("UPDATE `{$this->cacheTableName}` SET `lastCheck`={$lastCheck} WHERE `hash`='{$user->hash}'");
                            continue;
                        }
                        $this->updateAndResizeAllAvatarsSizes($user->hash);
                        continue;
                    } elseif ($gravatarStatus->status == 200 && $user->def == 1) {
                        $this->updateAndResizeAllAvatarsSizes($user->hash);
                    }
                }
            }
        }

        if ($this->optimizeAvatars) {
            $this->optimizeDefaultGravatars();
            $this->optimizeCacheCustomGravatars();
        }

        $this->clearPostsIDsOnPageCachePlugins();
        $this->resolveURLsFromHashs();
        $this->clearCloudflareCacheGravatars();
        flock($fp, LOCK_UN);
    }

    protected function optimizeCacheCustomGravatars()
    {
        global $wpdb;
        $sql = "SELECT `id`, `size`, `ext`, `lastModified` FROM `{$this->cacheTableName}` WHERE (optimized='0' AND def='0') ORDER BY lastCheck DESC LIMIT {$this->maxOptimizeEachTime}";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results) {
            foreach ($results as $avatar) {
                $b35Id=base_convert($avatar -> id, 10, 35);
                $avatarFile=ABSPATH."{$this->cacheDirectory}{$b35Id}.{$avatar -> ext}";
                if (!file_exists($avatarFile)) {
                    continue;
                }
                $avatarURL="{$this->cacheURL}{$b35Id}.{$avatar -> ext}";
                $optimizedAvatarRequest=$this->sendResmushRequest($avatarFile);

                if (!$optimizedAvatarRequest->error) {
                    $optimizedAvatar=$this->getOptimizedAvatar($optimizedAvatarRequest->optimizedURL);
                    if (!$optimizedAvatar->error && $optimizedAvatar->status == 200) {
                        if (file_put_contents($avatarFile, $optimizedAvatar->content)) {
                            $this->urlsToClearOnCloudflare[]=$avatarURL;
                            touch($avatarFile, $avatar->lastModified);
                            chmod($avatarFile, base_convert($this->avatarsMode, 8, 10));
                            $wpdb->query("UPDATE `{$this->cacheTableName}` SET optimized='1' WHERE id={$avatar->id}");
                        }
                    }
                }
            }
        }
    }

    protected function getIdByHashAndSize($hash, $size)
    {
        global $wpdb;
        $sql = "SELECT `id` FROM `{$this->cacheTableName}` where hash='{$hash}' AND size={$size}";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0] && $results[0] -> id) {
            return $results[0] -> id;
        }
        return false;
    }

    protected function compressDefaultAvatar()
    {
        if ($this->precompress) {
            if ($this->customAvatarExt=='svg') {
                $avatarFile = ABSPATH.$this->cacheDirectory.'0.svg';
                if (file_exists($avatarFile)) {
                    $theOutput = gzencode(file_get_contents($avatarFile), 9);
                    file_put_contents($avatarFile.".gz", $theOutput);
                    chmod($avatarFile.".gz", base_convert($this->avatarsMode, 8, 10));
                    touch($avatarFile.".gz", $this->customAvatarFileTime);
                }
            }
        }
    }

    protected function createPlaceholder()
    {
        $placeholderDest=ABSPATH.$this->cacheDirectory."_.".$this->placeholderExt;
        if ($this->defaultPlaceholder) {
            $defaultPlaceholderSrc=$this->pluginDirectory . '/avatar/placeholder.svg';
            if (!copy($defaultPlaceholderSrc, $placeholderDest)) {
                return false;
            }
        } else {
            $placeholderSrc=ABSPATH.$this->cacheDirectory."custom/placeholder.".$this->placeholderExt;
            if (!copy($placeholderSrc, $placeholderDest)) {
                return false;
            }
        }
        touch($placeholderDest, $this->placeholderFileTime);
        chmod($placeholderDest, base_convert($this->avatarsMode, 8, 10));
        return true;
    }

    protected function deleteUnneededPlaceholder()
    {
        $currentPlaceholder=ABSPATH.$this->cacheDirectory."_.".$this->placeholderExt;

        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            foreach (glob(ABSPATH.$this->cacheDirectory."_.*") as $placeholder) {
                if ($placeholder != $currentPlaceholder) {
                    unlink($placeholder);
                }
            }
        }
    }

    protected function compressPlaceholder()
    {
        if ($this->precompress && $this->placeholderExt =='svg') {
            $placeholderFile=ABSPATH."{$this->cacheDirectory}_.svg";
            if (file_exists($placeholderFile)) {
                $theOutput = gzencode(file_get_contents($placeholderFile), 9);
                file_put_contents($placeholderFile.".gz", $theOutput);
                touch($placeholderFile.".gz", $this->placeholderFileTime);
                chmod($placeholderFile.".gz", base_convert($this->avatarsMode, 8, 10));
            }
        }
    }

    protected function deleteCompressDefaultAvatar()
    {
        $avatarFile = ABSPATH.$this->cacheDirectory.'0.svg.gz';
        if (file_exists($avatarFile)) {
            unlink($avatarFile);
        }
    }
    protected function deleteCompressPlaceholder()
    {
        $avatarFile = ABSPATH.$this->cacheDirectory.'_.svg.gz';
        if (file_exists($avatarFile)) {
            unlink($avatarFile);
        }
    }
    protected function buildDefaultAvatarFailBack($mailHash, $size, $html, $classList, $useLazy)
    {
        $sizeId=base_convert($size, 10, 35);
        if ($this->customAvatarExt =="svg") {
            $avatarFile = glob(ABSPATH."{$this->cacheDirectory}0{$sizeId}_*.png");
            if (count($avatarFile) && file_exists($avatarFile[0])) {
                $avatarBaseName=basename($avatarFile[0]);
                $avatarURL="https://www.gravatar.com/avatar/{$mailHash}?s={$size}&r={$this->avatarRating}&d=". urlencode("{$this->cacheURL}{$avatarBaseName}");
            } else {
                $avatarURL="https://www.gravatar.com/avatar/{$mailHash}?s={$size}&r={$this->avatarRating}&d=mp";
            }
        } else {
            $avatarFileMD5=md5(ABSPATH."{$this->cacheDirectory}0$sizeId.{$this->customAvatarExt}");
            $avatarURL="https://www.gravatar.com/avatar/{$mailHash}?s={$size}&r={$this->avatarRating}&d=". urlencode("{$this->cacheURL}0{$sizeId}_{$avatarFileMD5}.{$this->customAvatarExt}");
        }

        $queryString="";
        $placeholderqQueryString="";
        if ($this->useQueryStrings) {
            $placeholderFileTime=base_convert($this->placeholderFileTime, 10, 35);
            $placeholderqQueryString="?d={$placeholderFileTime}";
        }

        if ($html) {
            $dataSrc="";
            $src="";
            $lazyClass="";
            if ($this->lazyLoad && $useLazy) {
                if ($this->usePlaceholder) {
                    $src=" src='{$this->placeholderURL}{$placeholderqQueryString}'";
                }
                $dataSrc=" data-src='{$avatarURL}{$queryString}'";
                $lazyClass=' ogc-lazy';
            } else {
                $src=" src='{$avatarURL}{$queryString}'";
            }
            return "<img alt{$src}{$dataSrc} class='{$classList}{$lazyClass}' width='{$size}' height='{$size}' />";
        } else {
            return "{$avatarURL}{$queryString}";
        }
    }

    protected function buildDefaultAvatar($size, $html, $classList)
    {
        if ($this->customAvatarExt =="svg") {
            $avatarFile = ABSPATH.$this->cacheDirectory.'0.svg';
            $avatarURL="{$this->cacheURL}0.svg";
        } else {
            $sizeId=base_convert($size, 10, 35);
            $avatarFile = ABSPATH."{$this->cacheDirectory}0$sizeId.{$this->customAvatarExt}";
            $avatarURL="{$this->cacheURL}0$sizeId.{$this->customAvatarExt}";
        }
        $queryString="";
        if ($this->useQueryStrings) {
            $avatarFileTime=base_convert($this->customAvatarFileTime, 10, 35);
            $queryString="?d={$avatarFileTime}";
        }
        if ($html) {
            $src=" src='{$avatarURL}{$queryString}'";
            return "<img alt{$src} class='{$classList}' width='{$size}' height='{$size}' />";
        } else {
            return "{$avatarURL}{$queryString}";
        }
    }

    protected function tryDefaultAvatar($source, $size, $html, $classList)
    {
        $avatarTag=$this->buildDefaultAvatar($size, $html, $classList);
        if ($avatarTag) {
            return $avatarTag;
        } else {
            return $source;
        }
    }

    protected function isThereAnyLargerCached($hash, $size)
    {
        global $wpdb;
        $sql = "SELECT id, ext, lastModified, lastCheck, def, size FROM `{$this->cacheTableName}` WHERE `hash` = '{$hash}' ORDER BY `size` DESC LIMIT 1";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0] && $results[0]->size > $size) {
            return $results[0];
        }
        return false;
    }

    protected function isThereAnySmallerCached($hash, $size)
    {
        global $wpdb;
        $sql = "SELECT id, ext, lastModified, lastCheck, def, size FROM `{$this->cacheTableName}` WHERE `hash` = '{$hash}' ORDER BY `size` ASC LIMIT 1";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0] && $results[0]->size < $size) {
            return $results[0];
        }
        return false;
    }

    protected function DBGetOtherAvatarSizeForUserCached($hash)
    {
        global $wpdb;
        $sql = "SELECT id, ext, lastModified, lastCheck, def, size FROM `{$this->cacheTableName}` WHERE `hash` = '{$hash}' ORDER BY `size` ASC LIMIT 1";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0]) {
            return $results[0];
        }
        return false;
    }

    protected function resizeCustomAvatarFromLargerCached($largerCached, $hash, $size)
    {
        global $wpdb;
        $sourceB35Id=base_convert($largerCached->id, 10, 35);
        $destB35Id=base_convert($this->getIdByHashAndSize($hash, $size), 10, 35);
        $destFile=ABSPATH."{$this->cacheDirectory}{$destB35Id}.{$largerCached->ext}";
        $img = wp_get_image_editor(ABSPATH."{$this->cacheDirectory}{$sourceB35Id}.{$largerCached->ext}");
        if (! is_wp_error($img)) {
            $resize = $img->set_quality(100);
            $resize = $img->resize($size, $size, true);
            $resize = $img->save($destFile);
            touch($destFile, $largerCached->lastModified);
            chmod($destFile, base_convert($this->avatarsMode, 8, 10));
        } else {
            return false;
        }
    }

    protected function resizeFromLargerCached($largerCached, $hash, $size, $html, $classList, $useLazy)
    {
        global $wpdb;

        $result=$wpdb->insert(
            $this->cacheTableName,
            array(
              'hash' => $hash,
              'optimized' => '0',
              'size' => $size,
              'ext' => $largerCached->ext,
              'lastCheck' => $largerCached->lastCheck,
              'lastModified' => $largerCached->lastModified,
              'def' => '0'
            ),
            array('%s', '%s', '%d', '%s', '%d', '%d', '%s')
        );

        if ($result) {
            $sourceB35Id=base_convert($largerCached->id, 10, 35);
            $destB35Id=base_convert($this->getIdByHashAndSize($hash, $size), 10, 35);
            $destFile=ABSPATH."{$this->cacheDirectory}{$destB35Id}.{$largerCached->ext}";
            $img = wp_get_image_editor(ABSPATH."{$this->cacheDirectory}{$sourceB35Id}.{$largerCached->ext}");
            if (! is_wp_error($img)) {
                $resize = $img->set_quality(100);
                $resize = $img->resize($size, $size, true);
                $resize = $img->save($destFile);
                touch($destFile, $largerCached->lastModified);
                chmod($destFile, base_convert($this->avatarsMode, 8, 10));
                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}{$destB35Id}.{$largerCached->ext}";
                $this->hashsToCleanOnPageCachePlugins[]=$hash;
            } else {
                return false;
            }
            $queryString="";
            $placeholderqQueryString="";
            if ($this->useQueryStrings) {
                $fileTime=base_convert($largerCached->lastModified, 10, 35);
                $queryString="?d={$fileTime}";
                $placeholderFileTime=base_convert($this->placeholderFileTime, 10, 35);
                $placeholderqQueryString="?d={$placeholderFileTime}";
            }
            if ($html) {
                $src="";
                $dataSrc="";
                $lazyClass="";
                if ($this->lazyLoad  && $useLazy) {
                    if ($this->usePlaceholder) {
                        $src=" src='{$this->placeholderURL}{$placeholderqQueryString}'";
                    }

                    $dataSrc=" data-src='{$this->cacheURL}{$destB35Id}.{$largerCached->ext}{$queryString}'";
                    $lazyClass=' ogc-lazy';
                } else {
                    $src=" src='{$this->cacheURL}{$destB35Id}.{$largerCached->ext}{$queryString}'";
                }
                return "<img alt{$src}{$dataSrc} class='{$classList}{$lazyClass}' width='{$size}' height='{$size}' />";
            } else {
                return "{$this->cacheURL}{$destB35Id}.{$largerCached->ext}{$queryString}";
            }
        }
        return false;
    }

    protected function getUserAvatarSize($hash, $size)
    {
        global $wpdb;
        $sql = "SELECT id, ext, lastModified FROM `{$this->cacheTableName}` WHERE `hash` = '{$hash}' AND `size` = '{$size}' LIMIT 1";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0]) {
            return $results[0];
        }
        return false;
    }

    protected function cacheNewAvatarSize($smallerCached, $hash, $size, $html, $classList, $useLazy)
    {
        global $wpdb;

        $result=$wpdb->insert(
            $this->cacheTableName,
            array(
              'hash' => $hash,
              'optimized' => '0',
              'size' => $size,
              'ext' => $smallerCached->ext,
              'lastCheck' => $smallerCached->lastCheck,
              'lastModified' => $smallerCached->lastModified,
              'def' => '0'
            ),
            array('%s', '%s', '%d', '%s', '%d', '%d', '%s')
        );

        if ($this->updateAndResizeAllAvatarsSizes($hash)) {
            $avatar=$this->getUserAvatarSize($hash, $size);
            $b35Id=base_convert($avatar->id, 10, 35);

            $queryString="";
            $placeholderqQueryString="";
            if ($this->useQueryStrings) {
                $fileTime=base_convert($avatar->lastModified, 10, 35);
                $queryString="?d={$fileTime}";
                $placeholderFileTime=base_convert($this->placeholderFileTime, 10, 35);
                $placeholderqQueryString="?d={$placeholderFileTime}";
            }

            if ($html) {
                $dataSrc="";
                $src="";
                $lazyClass="";
                if ($this->lazyLoad && $useLazy) {
                    if ($this->usePlaceholder) {
                        $src=" src='{$this->placeholderURL}{$placeholderqQueryString}'";
                    }
                    $dataSrc=" data-src='{$this->cacheURL}{$b35Id}.{$avatar->ext}{$queryString}'";
                    $lazyClass=' ogc-lazy';
                } else {
                    $src=" src='{$this->cacheURL}{$b35Id}.{$avatar->ext}{$queryString}'";
                }
                return "<img alt{$src}{$dataSrc} class='{$classList}{$lazyClass}' width='{$size}' height='{$size}' />";
            } else {
                return "{$this->cacheURL}{$b35Id}.{$avatar->ext}{$queryString}";
            }
        }

        return $this->tryDefaultAvatar($source, $size, $html, $classList);
    }

    protected function cacheNewDefaultSize($largerCached, $hash, $size, $html, $classList, $useLazy)
    {
        global $wpdb;

        $result=$wpdb->insert(
            $this->cacheTableName,
            array(
              'hash' => $hash,
              'optimized' => '0',
              'size' => $size,
              'ext' => $largerCached->ext,
              'lastCheck' => $largerCached->lastCheck,
              'lastModified' => $largerCached->lastModified,
              'def' => '1'
            ),
            array('%s', '%s', '%d', '%s', '%d', '%d', '%s')
        );


        if ($this->useSourceAsLongNotParsed && $largerCached->lastCheck==0) {
            $fail=$this->buildDefaultAvatarFailBack($hash, $size, $html, $classList, $useLazy);

            return $fail;
        }

        return $this->buildDefaultAvatar($size, $html, $classList);
    }

    protected function updateResolved()
    {
        $this->resolved+=1;
    }

    protected function isFirstAvatar($hash)
    {
        global $wpdb;
        $sql = "SELECT COUNT(id) as num FROM `{$this->cacheTableName}` WHERE `hash`='{$hash}'";
        $results = $wpdb->get_results($sql, OBJECT);
        if ($results[0] && $results[0]->num == 0) {
            return true;
        }
        return false;
    }

    public function shutdown()
    {
        global $wpdb;
        rsort($this->avatarUsedSizes);
        update_option('OGC_avatarUsedSizes', $this->avatarUsedSizes);
        update_option('OGC_resolved', $this->resolved);

        if (!defined('DOING_CRON')) {
            $this->clearPostsIDsOnPageCachePlugins();
            $this->clearCloudflareCacheGravatars();
        }
        return true;
    }

    protected function updateAvatarUsedSizes($size)
    {
        if (!in_array($size, $this->avatarUsedSizes)) {
            $this->avatarUsedSizes[]=$size;
            $this->createMissingDefaultAvatarSize($size);
        }
        return true;
    }

    protected function createDefaultAvatarUsedSizes($hash)
    {
        global $wpdb;

        $sqlAvatarValues=array();
        foreach ($this->avatarUsedSizes as $usedSize) {
            $sqlAvatarValues[]=sprintf("('%s', '%s', %d, '%s', %d, %d, '%s')", $hash, '0', $usedSize, $this->customAvatarExt, 0, 0, '1');
        }

        $query="INSERT IGNORE INTO `{$this->cacheTableName}` (`hash`, `optimized`, `size`, `ext`, `lastCheck`, `lastModified`, `def`) VALUES ";
        $query .= implode(",\n", $sqlAvatarValues);
        $wpdb->query($query);

        return;
    }

    public function getCachedAvatar($source, $idOrEmail, $size, $default, $alt, $classList, $html=true, $useLazy=false)
    {
        global $wpdb;

        if ($this->learningMode) {
            $this->updateAvatarUsedSizes($size);
            return $source;
        }

        if ($source!==null && strpos($source, 'gravatar.com') === false) {
            return $source;
        }

        $md5Source=false;
        if ($source!==null) {
            $md5Source=md5($source);
            if (isset($this->avatarCached[$md5Source])) {
                return $this->avatarCached[$md5Source];
            }
        }

        $email=false;
        $user=false;

        if (is_numeric($idOrEmail)) {
            $id = (int) $idOrEmail;
            $user = get_userdata($id);
            if (is_object($user)) {
                $email=$user->user_email;
            }
        } elseif (is_object($idOrEmail)) {
            if (!empty($idOrEmail->user_id)) {
                $id = (int) $idOrEmail->user_id;
                $user = get_userdata($id);
                $email=$user->user_email;
            } elseif (!empty($idOrEmail->comment_author_email)) {
                $email=$idOrEmail->comment_author_email;
            }
        } elseif (is_email($idOrEmail)) {
            $email=$idOrEmail;
        }

        $this->updateResolved();
        $this->updateAvatarUsedSizes($size);
        $email=strtolower(trim($email));
        $mailHash=md5($email);

        $this->hashsOnthisUrl[]=$mailHash;

        $sql = $wpdb->prepare("SELECT `id`, `hash`,`ext`,`def`,`lastModified`,`lastCheck` FROM `{$this->cacheTableName}` WHERE `hash` = '%s' AND `size` = %d LIMIT 1", $mailHash, $size);
        $results = $wpdb->get_results($sql, OBJECT);

        $img=null;

        if (isset($results[0] -> id)) {
            if ($results[0] -> def == 0) {
                $b35Id=base_convert($results[0] -> id, 10, 35);
                $queryString="";
                $placeholderqQueryString="";
                if ($this->useQueryStrings) {
                    $fileTime=base_convert($results[0]->lastModified, 10, 35);
                    $queryString="?d={$fileTime}";
                    $placeholderFileTime=base_convert($this->placeholderFileTime, 10, 35);
                    $placeholderqQueryString="?d={$placeholderFileTime}";
                }

                if ($html) {
                    $dataSrc="";
                    $src="";
                    $lazyClass="";
                    if ($this->lazyLoad && $useLazy) {
                        if ($this->usePlaceholder) {
                            $src=" src='{$this->placeholderURL}{$placeholderqQueryString}'";
                        }
                        $dataSrc=" data-src='{$this->cacheURL}{$b35Id}.{$results[0]->ext}{$queryString}'";
                        $lazyClass=' ogc-lazy';
                    } else {
                        $src=" src='{$this->cacheURL}{$b35Id}.{$results[0]->ext}{$queryString}'";
                    }
                    $img="<img alt{$src}{$dataSrc} class='{$classList}{$lazyClass}' width='{$size}' height='{$size}' />";
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$img;
                    }
                    return $img;
                } else {
                    $img="{$this->cacheURL}{$b35Id}.{$results[0]->ext}{$queryString}";
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$img;
                    }
                    return $img;
                    return $img;
                }
            } elseif ($results[0] -> def == 1) {
                if ($this->useSourceAsLongNotParsed && $results[0] -> lastCheck == 0) {
                    $fail=$this->buildDefaultAvatarFailBack($mailHash, $size, $html, $classList, $useLazy);
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$fail;
                    }
                    return $fail;
                }

                $img=$this->buildDefaultAvatar($size, $html, $classList);
                if ($md5Source) {
                    $this->avatarCached[$md5Source]=$img;
                }
                return $img;
            }
        }

        if ($this->isFirstAvatar($mailHash)) {
            $this->createDefaultAvatarUsedSizes($mailHash);

            if ($this->useSourceAsLongNotParsed) {
                $fail=$this->buildDefaultAvatarFailBack($mailHash, $size, $html, $classList, $useLazy);
                if ($md5Source) {
                    $this->avatarCached[$md5Source]=$fail;
                }
                return $fail;
            }

            $img=$this->buildDefaultAvatar($size, $html, $classList);
            if ($md5Source) {
                $this->avatarCached[$md5Source]=$img;
            }
            return $img;
        }

        $largerCached=$this->isThereAnyLargerCached($mailHash, $size);
        if ($largerCached) {
            if ($largerCached->def==0) {
                $resizedFromLarger=$this->resizeFromLargerCached($largerCached, $mailHash, $size, $html, $classList, $useLazy);
                if ($resizedFromLarger) {
                    $img=$resizedFromLarger;
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$img;
                    }
                    return $img;
                }
            } else {
                $img=$this->cacheNewDefaultSize($largerCached, $mailHash, $size, $html, $classList, $useLazy);
                if ($md5Source) {
                    $this->avatarCached[$md5Source]=$img;
                }
                return $img;
            }
        } else {
            $smallerCached=$this->isThereAnySmallerCached($mailHash, $size);
            if ($smallerCached) {
                if ($smallerCached->def==0) {
                    $img=$this->cacheNewAvatarSize($smallerCached, $mailHash, $size, $html, $classList, $useLazy);
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$img;
                    }
                    return $img;
                } else {
                    $img=$this->cacheNewDefaultSize($smallerCached, $mailHash, $size, $html, $classList, $useLazy);
                    if ($md5Source) {
                        $this->avatarCached[$md5Source]=$img;
                    }
                    return $img;
                }
            }
        }
        return $this->tryDefaultAvatar($source, $size, $html, $classList);
    }

    protected function getDBStats()
    {
        global $wpdb;
        $stats=array();

        $sql = "SELECT count(id) as num FROM `{$this->cacheTableName}`";
        $total = $wpdb->get_results($sql, OBJECT);
        if (isset($total[0])) {
            $total = ((int)$total[0]->num);
        } else {
            $total = 0;
        }

        $sql = "SELECT count( DISTINCT(hash) ) as num FROM `{$this->cacheTableName}` WHERE def='1'";
        $default = $wpdb->get_results($sql, OBJECT);
        if (isset($default[0])) {
            $default = ((int)$default[0]->num);
        } else {
            $default = 0;
        }

        $sql = "SELECT count( DISTINCT(hash) ) as num FROM `{$this->cacheTableName}` WHERE def='0'";
        $custom = $wpdb->get_results($sql, OBJECT);
        if (isset($custom[0])) {
            $custom = ((int)$custom[0]->num);
        } else {
            $custom = 0;
        }

        $customPercent=0;
        $defaultPercent=0;

        if ($default || $custom) {
            $customPercent=number_format_i18n(round(($custom * 100 / ($custom + $default)), 2), 2);
            $defaultPercent=number_format_i18n(round(($default * 100 / ($custom + $default)), 2), 2);
        }

        $stats['default']=$default;
        $stats['custom']=$custom;
        $stats['customPercent']=$customPercent;
        $stats['total']=$total;
        $stats['defaultPercent']=$defaultPercent;
        $stats['sizes']=implode(', ', $this->avatarUsedSizes);
        return $stats;
    }

    public function addAdminMenu()
    {
        add_options_page('Optimum Gravatar Cache ', $this->pluginName, 'manage_options', $this->pluginSlug, array( $this,'settingsViewPage' ));
    }

    public function settingsViewPage()
    {
        echo '<div class="wrap"><h1>'.$this->pluginName.'</h1>';
        if (isset($_GET['tab'])) {
            $current = $_GET['tab'];
        } else {
            $current = 'cache';
        }

        $tabs = array( 'cache' => __("Cache", "OGC"), 'defaultAvatar' => __("Default/Placeholder Gravatar", "OGC"), 'optimization' => __("Optimization", "OGC"), 'apacheServer' => __("Apache Server", "OGC"), 'nginxServer' => __("NGinx Server", "OGC"), 'otherOptions' => __("Other Options", "OGC"), 'lazyLoad' => __("Lazy Load", "OGC"), 'cloudflare' => __("Cloudflare", "OGC"), 'stats' => __("Stats", "OGC"));
        $links = array();
        foreach ($tabs as $tab => $name) {
            if ($tab == $current) {
                $links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->pluginSlug."&tab=$tab'>$name</a>";
            } else {
                $links[] = "<a class='nav-tab' href='?page=".$this->pluginSlug."&tab=$tab'>$name</a>";
            }
        }
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($links as $link) {
            echo $link;
        }
        echo '</h2>';

        switch ($current) {
        case 'cache':
            $fileCacheInfo = $this->getCacheDetails();
            include "admin/templates/cache.php";
            break;
        case 'defaultAvatar':
            include "admin/templates/default-avatar.php";
            break;
        case 'optimization':
            include "admin/templates/optimization.php";
            break;
        case 'otherOptions':
            include "admin/templates/other-options.php";
            break;
        case 'apacheServer':
            $apacheConfig = get_option('OGC_apacheConfig');
            include "admin/templates/apache-server.php";
            break;
        case 'nginxServer':
            include "admin/templates/nginx-server.php";
            break;
        case 'lazyLoad':
            include "admin/templates/lazy-load.php";
            break;
        case 'cloudflare':
            include "admin/templates/cloudflare.php";
            break;
        case 'stats':
            $dbCacheInfo = $this->getDBStats();
            $fileCacheInfo = $this->getCacheDetails();
            include "admin/templates/stats.php";
            break;
        }
        echo "</div>";
    }

    protected function syncUsersAndCommenters()
    {
        $this->addUsersAndCommentersToCache();

        $this->errorMessages[]=array(
          "type" => "notice notice-success",
          "message" => __("Users and commenters successfully synced.", "OGC"),
          "args"=>array()
        );
    }


    protected function clearCache()
    {
        global $wpdb;
        $noError=true;
        $needReactivateCache=false;

        if ($this->activated) {
            $needReactivateCache=true;
            $this->options['activated']=0;
            update_option('OGC_options', $this->options);
        }

        if (!$wpdb->query("TRUNCATE TABLE `{$this->cacheTableName}`")) {
            $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Unable to remove data from the database table.", "OGC")
            );
            $noError=false;
        }

        if ($noError && !$wpdb->query("ALTER TABLE `{$this->cacheTableName}` AUTO_INCREMENT = 1")) {
            $this->errorMessages[]=array(
                "type" => "error notice",
                "message" => __("Unable to reset AUTO_INDEX on the database table.", "OGC")
            );
            $noError=false;
        }

        if ($noError && $this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            $fileList = glob(ABSPATH.$this->cacheDirectory.'*.{'.implode(",", array_values($this->mimeTypes)).',O,svg.gz}', GLOB_BRACE);
            $excludeList = glob(ABSPATH.$this->cacheDirectory.'{_*,0*}', GLOB_BRACE);
            $fileList = array_diff($fileList, $excludeList);

            foreach ($fileList as $file) {
                if (!unlink($file)) {
                    $this->errorMessages[]=array(
                      "type" => "error notice",
                      "message" =>  __("File '%s' could not be deleted. Please check file/folder permissions.", "OGC"),
                      "args"=>array($file)
                    );
                    $noError=false;
                }
            }
        }

        if ($needReactivateCache) {
            $this->clearAllOnPageCachePlugins();
            $this->options['activated']=1;
        }

        if ($noError) {
            $this->options['synchronizedEmail']=0;
            $this->errorMessages[]=array(
              "type" => "notice notice-success",
              "message" => __("The Gravatar cache has been successfully cleared.", "OGC"),
              "args"=>array()
            );
            $this->reSetCronEvent();
        }
        update_option('OGC_options', $this->options);
    }

    protected function getCacheDetails()
    {
        $fileList=array();
        $size = 0;
        $mimeTypesList=array();
        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            $fileList = glob(ABSPATH.$this->cacheDirectory.'/*.{'.implode(",", array_values($this->mimeTypes)).'}', GLOB_BRACE);
            foreach ($fileList as $file) {
                $mimeType=mime_content_type($file);
                if (!in_array($mimeType, $mimeTypesList)) {
                    $mimeTypesList[]=$mimeType;
                }
                $size  += filesize($file);
            }
        }
        return array( 'images' => count($fileList), 'usedSpace' => $this->sizeToByte($size), 'typesUsed' => implode(", ", $mimeTypesList) );
    }

    protected function sizeToByte($size)
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format_i18n($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function adminScripts()
    {
        if ($this->useLazyOnAdminPages) {
            wp_enqueue_script('ogc-lazy-script', plugins_url('/js/lazyLoadAvatar.js', __FILE__), array(), '1.1', true);

            wp_add_inline_script(
                'ogc-lazy-script',
                'var OGC={
                        offset:'.$this->offset.'
                      };',
                'before'
            );
        }

        $current_page = get_current_screen()->base;
        if ($current_page == 'settings_page_'.basename(__FILE__, '.php')) {
            wp_enqueue_script('ogc-main-script', plugins_url('/admin/js/main.js', __FILE__), array('jquery'));
        }
    }

    public function clientScriptsIfNeed($content)
    {
        wp_enqueue_script('ogc-lazy-script');
        return $content;
    }

    public function clientScripts()
    {
        if ($this->scriptInFooter) {
            wp_register_script('ogc-lazy-script', plugins_url('/js/lazyLoadAvatar.js', __FILE__), array(), '1.1', true);
        } else {
            wp_enqueue_script('ogc-lazy-script', plugins_url('/js/lazyLoadAvatar.js', __FILE__), array());
        }
        wp_add_inline_script(
            'ogc-lazy-script',
            'var OGC={
                        offset:'.$this->offset.'
                      };',
            'before'
        );
    }

    protected function getUserAvatarsCached($hash)
    {
        global $wpdb;

        $sql = "SELECT `id`, `ext`, `size`, `lastModified` FROM `{$this->cacheTableName}` WHERE `hash` = '$hash' AND `def`='0'";
        $results = $wpdb->get_results($sql, OBJECT);
        if (count($results)) {
            return $results;
        } else {
            return false;
        }
    }

    public function deleteUserAvatarsCache($userId)
    {
        global $wpdb;
        $userData = get_userdata($userId);
        $mailHASH=md5($userData->user_email);

        if ($this->firstSave && is_dir(ABSPATH.$this->cacheDirectory)) {
            $users=$this->getUserAvatarsCached($mailHASH);
            if ($users) {
                foreach ($users as $user) {
                    $b35Id=base_convert($user -> id, 10, 35);
                    if (file_exists(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$user -> ext)) {
                        unlink(ABSPATH.$this->cacheDirectory.$b35Id.'.'.$user -> ext);
                    }
                }
            }
        }
        $wpdb->query("DELETE FROM `{$this->cacheTableName}` WHERE `hash`='{$mailHASH}'");
    }

    // protected function baseConvert($num, $a, $b)
    // {
    //     return gmp_strval(gmp_init($num, $a), $b);
    // }

    protected function validatePermissionsMode($mode)
    {
        if (preg_match('/^[0-7]{4}$/', $mode)) {
            return true;
        }
        return false;
    }

    protected function copyOldCacheDirectoryFiles($src, $dst)
    {
        $dir = opendir($src);
        if ($dir === false) {
            return false;
        }

        if (!is_dir($dst)) {
            $result = mkdir($dst);
        } else {
            $result = true;
        }

        if ($result === true) {
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..') && $result) {
                    if (is_dir($src . '/' . $file)) {
                        $result = $this->copyOldCacheDirectoryFiles($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        $result = copy($src . '/' . $file, $dst . '/' . $file);
                        touch($dst . '/' . $file, filemtime($src . '/' . $file));
                    }
                }
            }
        }
        closedir($dir);

        return $result;
    }

    protected function postsContainsGravatarsHash($hash)
    {
        global $wpdb;
        $postsIDs=array();

        $email=$this->getUserOrCommenterEmailByHash($hash);

        if (!$email) {
            return $postsIDs;
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            $user = new stdClass();
            $user->user_email=$email;
            $user->ID=0;
        }

        $args = array(
                'author_email' => $user->user_email
            );

        $comments = get_comments($args);

        foreach ($comments as $comment) {
            $postsIDs[]=$comment->comment_post_ID;
        }

        if ($user->ID > 0) {
            $where = "WHERE ( ( (`post_type` = 'post' OR `post_type` = 'page') AND ( post_status = 'publish' ) ) ) AND post_author = {$user->ID}";
            $query = "SELECT ID FROM $wpdb->posts $where";
            $post_ids = $wpdb->get_results($query, OBJECT);

            foreach ($post_ids as $post_id) {
                $postsIDs[]=$post_id->ID;
            }
        }
        return $postsIDs;
    }

    protected function clearPostsIDsOnPageCachePlugins()
    {
        global $wpdb;
        if (!$this->clearPageChachePluginsCache) {
            return;
        }

        if (count($this->hashsToCleanOnPageCachePlugins)) {
            $this->hashsToCleanOnPageCachePlugins=array_unique($this->hashsToCleanOnPageCachePlugins, SORT_REGULAR);
            $hashsToSelect=array();
            $postsIDsToClear=array();

            foreach ($this->hashsToCleanOnPageCachePlugins as $hashToClean) {
                $postsIDs=$this->postsContainsGravatarsHash($hashToClean);
                $postsIDsToClear = array_merge($postsIDsToClear, $postsIDs);
            }

            $this->hashsToCleanOnPageCachePlugins=array();

            if (count($postsIDsToClear)) {
                $postsIDsToClear=array_values(array_unique($postsIDsToClear));
            }

            switch ($this->cachePagePlugins) {
            case 'wp-super-cache':
                foreach ($postsIDsToClear as $postIDToClear) {
                    wp_cache_post_change((int)$postIDToClear);
                    wpsc_delete_post_cache((int)$postIDToClear);
                    wpsc_delete_post_archives((int)$postIDToClear);
                }
                break;
            case 'WPHB-cache':
                foreach ($postsIDsToClear as $postIDToClear) {
                    do_action('wphb_clear_page_cache', $postIDToClear);
                }
                break;
            }
        }
    }

    protected function clearAllOnPageCachePlugins()
    {
        if (!$this->clearPageChachePluginsCache) {
            return;
        }

        switch ($this->cachePagePlugins) {
        case 'wp-super-cache':
            global $file_prefix;
            wp_cache_clean_cache($file_prefix);
            break;
        case 'WPHB-cache':
            do_action('wphb_clear_page_cache');
            break;
        default:
            $this->errorMessages[]=array(
                "type" => "notice notice-success",
                "message" => __("If you are using some other page cache plugin that is not supported you have to clear cache of that same plugin.", "OGC"),
                "args"=>array()
              );
        }
    }

    protected function checkThePageCachePluginThatIsInUse()
    {
        if (!$this->clearPageChachePluginsCache) {
            return;
        }

        if (function_exists('wp_cache_post_change')) {
            $this->cachePagePlugins='wp-super-cache';
            $GLOBALS["super_cache_enabled"]=1;
            return;
        }
        if (defined('WPHB_VERSION')) {
            $this->cachePagePlugins='WPHB-cache';
            if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] == '') {
                $_SERVER['HTTP_HOST']=$this->host;
            }
            return;
        }
    }

    protected function getUserOrCommenterEmailByHash($hash)
    {
        global $wpdb;
        $query = "SELECT `user_email`, IF ('{$hash}' = MD5(`user_email`), 'true', 'false') as valid from `{$wpdb->users}` ORDER BY `valid` DESC limit 1";
        $usersIDs = $wpdb->get_results($query, OBJECT);
        if ($usersIDs[0]->valid == "true") {
            return $usersIDs[0]->user_email;
        } else {
            $query = "SELECT `comment_author_email`, IF ('{$hash}' = MD5(`comment_author_email`), 'true', 'false') as valid from `{$wpdb->comments}` ORDER BY `valid` DESC limit 1 ";
            $commentersIDs = $wpdb->get_results($query, OBJECT);
            if ($commentersIDs[0]->valid == "true") {
                return $commentersIDs[0]->comment_author_email;
            }
        }
        return false;
    }

    protected function resolveURLsFromHashs()
    {
        global $wpdb;
        if (count($this->hashsToCleanOnPageCachePlugins)) {
            $hashsToSelect=array();
            foreach ($this->hashsToCleanOnPageCachePlugins as $userHash) {
                $hashsToSelect[]=sprintf("'%s'", $userHash);
            }
            $hashsToSelectPart= implode(",", $hashsToSelect);

            $query="SELECT `id`, `ext` FROM `{$this->cacheTableName}` WHERE `hash` in({$hashsToSelectPart}) AND `def` = '0'";
            $results=$wpdb->get_results($query, OBJECT);

            foreach ($results as $gravatar) {
                $b35Id=base_convert($gravatar -> id, 10, 35);
                $this->urlsToClearOnCloudflare[]="{$this->cacheURL}{$b35Id}.{$gravatar->ext}";
            }
        }
    }

    protected function getDefaultGravatarHashs()
    {
        if (count($this->urlsToClearOnCloudflare)) {
            global $wpdb;

            $defaultGavatarHashsSQL = "SELECT DISTINCT(`hash`) FROM `wp_optimum_gravatar_cache` where `def` ='1'";
            $defaultGravatarHashs = $wpdb->get_results($defaultGavatarHashsSQL, ARRAY_N);

            foreach ($defaultGravatarHashs as $defaultGravatarHash) {
                $this->hashsToCleanOnPageCachePlugins[]=$defaultGravatarHash[0];
            }
        }
    }

    protected function clearCloudflareCacheGravatars()
    {
        if ($this->clearCloudflareCache) {
            $urlsToClear=array_unique($this->urlsToClearOnCloudflare, SORT_REGULAR);
            if (count($urlsToClear)) {

                $properties=$this->sendCloudflareRequest($urlsToClear);
                if (!$properties->error) {
                    //
                }
                $this->urlsToClearOnCloudflare=array();
            }
        }
    }
}

$OGC = new OGC();
