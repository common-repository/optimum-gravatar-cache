<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
?>

<form method="post" class="gravatar-cache-form">
  <input type="hidden" name="OGC_options[nonce]" value="<?php echo wp_create_nonce("OGC");?>"/>
    <table class="form-table">

        <tr>
        <th scope="row"><?php _e("Query Strings", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("Query Strings", "OGC"); ?></span></legend>
          <label for="useQueryStrings"><input name="OGC_options[useQueryStrings]" id="useQueryStrings" value="1" type="checkbox" <?php checked(1, $this->useQueryStrings); ?>><?php _e("Use a query string with Gravatar modification date for cache busting/invalidation", "OGC"); ?></label>
          <p class="description"><?php _e("Using a query string (such as ?d=[modification date]) allows browsers to invalidate a particular Gravatar’s cache whenever the query string is changed (such as when a Gravatar is updated).", "OGC"); ?></p>
        </fieldset></td>
        </tr>

        <tr>
        <th scope="row"><?php _e("Clear cache of Page Cache plugins", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("Clear cache of Page Cache plugins", "OGC"); ?></span></legend>
          <label for="clearPageChachePluginsCache"><input name="OGC_options[clearPageChachePluginsCache]" id="clearPageChachePluginsCache" value="1" type="checkbox" <?php checked(1, $this->clearPageChachePluginsCache); ?>><?php _e("Clear cache of Page Cache plugins", "OGC"); ?></label>
          <p class="description"><?php _e("This option, when enabled, allows you to clear the cache of posts and pages in the supported Cache Plugins, such as WP Super Cache. Whenever there are changes in Gravatars, the posts and pages containing the updated Gravatars will be cleaned.", "OGC"); ?></p>
        </fieldset></td>
        </tr>

        <tr>
        <th scope="row"><?php _e("Use gravatar.com", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("Use gravatar.com", "OGC"); ?></span></legend>
          <label for="useSourceAsLongNotParsed"><input name="OGC_options[useSourceAsLongNotParsed]" id="useSourceAsLongNotParsed" value="1" type="checkbox" <?php checked(1, $this->useSourceAsLongNotParsed); ?>><?php _e("Use gravatar.com until there is a Gravatar in the cache", "OGC"); ?></label>
          <p class="description"><?php _e("This option allows Gravatars from *.gravatar.com to be displayed, until a version has been cached in the background. This prevents an extended period of time where users would see the default Gravatar for all users and commenters.<br><br>Attention, with this option enabled the custom Gravatar should not be an SVG file type, since gravatar.com does not support this file type. If you use an SVG image as your custom/placeholder Gravatar, gravatar.com will instead show the 'mysterious person' for any users that do not have a custom Gravatar.", "OGC"); ?></p>
        </fieldset></td>
        </tr>

        <tr>
        <th scope="row"><?php _e("Remove Gravatars from your cache when removed from gravatar.com", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("Remove Gravatars from your cache when removed from gravatar.com", "OGC"); ?></span></legend>
          <label for="removeFromCacheWhenRemovedFromGravatar"><input name="OGC_options[removeFromCacheWhenRemovedFromGravatar]" id="removeFromCacheWhenRemovedFromGravatar" value="1" type="checkbox" <?php checked(1, $this->removeFromCacheWhenRemovedFromGravatar); ?>><?php _e("Remove Gravatars from your cache when they are removed from gravatar.com", "OGC"); ?></label>
          <p class="description"><?php _e("If you use caching plugins, such as 'WP Super Cache', you should not select this option. It is possible that the cached pages in other plugins could contain references to cached Gravatars that no longer exist.", "OGC"); ?></p>
        </fieldset></td>
        </tr>

        <tr>
        <th scope="row"><?php _e("UserAgent", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("UserAgent", "OGC"); ?></span></legend>
          <label for="sendUserAgent"><input name="OGC_options[sendUserAgent]" id="sendUserAgent" value="1" type="checkbox" <?php checked(1, $this->sendUserAgent); ?>><?php _e("Submit the site URL with the UserAgent to gravatar.com and resmush.it", "OGC"); ?></label><br>
          <?php _e("UserAgent", "OGC"); ?>: <input name="OGC_options[userAgent]" value="<?php echo $this->userAgent; ?>" class="regular-text ltr" type="text">
          <p class="description"><?php _e("It is usually a good idea to submit the site URL with the UserAgent, as under certain circumstances other sites could block your requests as without this information you could violate their security policies.", "OGC"); ?></p>
        </fieldset></td>
        </tr>

        <tr>
        <th scope="row"><?php _e("Resolve domain through content URL", "OGC"); ?></th>
        <td><fieldset><legend class="screen-reader-text"><span><?php _e("Resolve domain through content URL", "OGC"); ?></span></legend>
          <label for="resolvHostFromContentURL"><input name="OGC_options[resolvHostFromContentURL]" id="resolvHostFromContentURL" value="1" type="checkbox" <?php checked(1, $this->resolvHostFromContentURL); ?>><?php _e("Resolve the domain from the content URL", "OGC"); ?></label>
          <p class="description"><?php _e("Resolving the domain from the content URL is important when you have a domain or subdomain configured to serve static content. Serving assets 'Cookie Free' is one of the main reasons some people configure a domain that will only host static content.", "OGC"); ?></p>
        </fieldset></td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e("In learning mode", "OGC"); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text"><span><?php _e("In learning mode", "OGC"); ?></span></legend>
              <label for="learningMode"><input name="OGC_options[learningMode]" id="learningMode" value="1" type="checkbox" <?php checked(1, $this->learningMode); ?>><?php _e("Enable learning mode", "OGC"); ?></label>
              <p class="description">
                <?php _e("When the 'Learning Mode' option is enabled, the plugin captures the sizes of the Gravatars used on your site. This will reduce the bandwidth used to download assets from gravatar.com. Not only that, but the processing of Gravatars will be faster as the plugin will have captured the sizes relevant to your site. Leave this option active and navigate through pages on your site that contain Gravatars, to speed up the size collection process.", "OGC"); ?>
              </p>
            </fieldset>
          </td>
        </tr>

        <tr>
          <th scope="row"><label for="additionalClasses"><?php _e("Additional Classes", "OGC"); ?></label></th>
          <td><input required name="OGC_options[additionalClasses]" id="additionalClasses" value="<?php echo implode(" ", $this->additionalClasses); ?>" class="regular-text ltr" type="text">
            <p class="description">
              <?php _e("This option allows you to add additional classes to displayed Gravatars. These classes will be used on all Gravatars that have been processed by the plugin.", "OGC"); ?>
            </p>
          </td>
        </tr>
  </table>
  <p class="submit">
    <button type="submit" name="updateOtherOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
  </p>
</form>
<table class="form-table">
  <tr valign="top">
    <th><img alt="" src="<?php echo $this->getLogo() ?>" class="avatar avatar-150 photo" width="150" height="150" /></th>
    <th>
      <label><?php _e("Contact Us", "OGC"); ?></label><br>
      <label><?php _e("WebSite", "OGC") ?>: <a href="https://www.ncdc.pt">https://www.ncdc.pt</a></label><br>
      <label><?php _e("E-mail", "OGC") ?>: <a title="Mail To miguel@ncdc.pt" href="mailto:miguel@ncdc.pt">miguel@ncdc.pt</a</label>
    </th>
    <th>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="ETTJQCUA5Q6CE">
        <input type="image" src="https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - A forma mais fácil e segura de efetuar pagamentos online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/pt_PT/i/scr/pixel.gif" width="1" height="1">
      </form>
    </th>
  </tr>
</table>
