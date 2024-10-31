<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
?>

  <form method="post" class="gravatar-cache-form">
    <input type="hidden" name="OGC_options[nonce]" value="<?php echo wp_create_nonce("OGC"); ?>"/>
    <table class="form-table">
      <tr>
      <th scope="row"><?php _e("Use Cloudflare", "OGC"); ?></th>
      <td><fieldset><legend class="screen-reader-text"><span><?php _e("Use Cloudflare", "OGC"); ?></span></legend>
        <label for="clearCloudflareCache"><input name="OGC_options[clearCloudflareCache]" id="clearCloudflareCache" value="1" type="checkbox" <?php checked(1, $this->clearCloudflareCache); ?>><?php _e("Clean Gravatars in Cloudflare when they are updated", "OGC"); ?></label>
        <p class="description"><?php _e("This option allows outdated gravatars to be removed from the Cloudflare cache whenever it is updated locally, forcing the Cloudflare to serve the updated gravatars.", "OGC"); ?></p>
      </fieldset></td>
      </tr>

      <tr>
        <th scope="row"><label for="clearCloudflareCacheAuthEmail"><?php _e("Your account email address", "OGC"); ?></label></th>
        <td><input required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="OGC_options[clearCloudflareCacheAuthEmail]" id="clearCloudflareCacheAuthEmail" value="<?php echo $this->clearCloudflareCacheAuthEmail; ?>" class="regular-text ltr" type="email">
          <p class="description">
            <?php _e("The email address used to communicate with Cloudflare.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="clearCloudflareCacheAuthKey"><?php _e("Your global API Key", "OGC"); ?></label></th>
        <td><input name="OGC_options[clearCloudflareCacheAuthKey]" id="clearCloudflareCacheAuthKey" value="<?php echo $this->clearCloudflareCacheAuthKey; ?>" class="regular-text ltr" type="text">
          <p class="description">
            <?php _e("The Global API KEY that is used to communicate with your Cloudflare account through the API.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="clearCloudflareCacheZoneID"><?php _e("Your zone ID", "OGC"); ?></label></th>
        <td><input disabled name="OGC_options[clearCloudflareCacheZoneID]" id="clearCloudflareCacheZoneID" value="<?php echo $this->clearCloudflareCacheZoneID; ?>" class="regular-text ltr" type="text">
          <p class="description">
            <?php _e("The Zone ID that is used to communicate with your Cloudflare account through the API.", "OGC"); ?>
          </p>
        </td>
      </tr>

    </table>
    <p class="submit">
      <button type="submit" name="updateCloudflareOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
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
