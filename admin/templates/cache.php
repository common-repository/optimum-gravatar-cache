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
        <th scope="row">
          <?php _e("Caching", "OGC"); ?>
        </th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e("Caching", "OGC"); ?></span></legend>
            <label><input name='OGC_options[activated]' value="1" type="radio" <?php checked(1, $this->activated); ?>>  <?php _e("Caching Enabled", "OGC"); ?></label><br>
            <label><input name='OGC_options[activated]' value="0" type="radio" <?php checked(0, $this->activated); ?>> <?php _e("Cache Disabled", "OGC"); ?></label>
            <p class="description">
              <?php _e("This option allows you to enable or disable the Gravatar cache locally. You will still be able to configure all other aspects of the plugin.", "OGC"); ?>
            </p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="expireTime"><?php _e("Refresh Gravatar cache every", "OGC"); ?></label></th>
        <td>
          <input required name="OGC_options[expireTime]" step="1" min="1" id="expireTime" value="<?php echo $this->expireTime; ?>" class="small-text" type="number" oninvalid="this.setCustomValidity('<?php _e("Please enter a whole number greater than or equal to 1.", "OGC");?>')" oninput="this.setCustomValidity('')">
          <?php $this->expireTime <2 ? _e("day", "OGC") : _e("days", "OGC"); ?>
          <p class="description">
            <?php _e("This option allows you to specify how many days the Gravatars are cached for. The plugin will check gravatar.com for new/updated Gravatars after this many day and will update your cache as necessary.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="searchExpiredTime"><?php _e("Check for outdated Gravatars every", "OGC"); ?></label></th>
        <td>
          <input required name="OGC_options[searchExpiredTime]" step="1" min="1" id="searchExpiredTime" value="<?php echo $this->searchExpiredTime; ?>" class="small-text" type="number" oninvalid="this.setCustomValidity('<?php _e("Please enter a whole number greater than or equal to 1.", "OGC");?>')" oninput="this.setCustomValidity('')">
          <?php $this->searchExpiredTime <2 ? _e("minute", "OGC") : _e("minutes", "OGC"); ?>
          <p class="description">
            <?php _e("This option lets you specify a time interval in minutes for the background tasks to run. This time interval will be used by WP Cron to schedule the tasks in the background. Tasks such as updating, resizing and optimizing Gravatars are done in the background to prevent impacting the response time of your pages.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="maxUpdateEachTime"><?php _e("Number of users to check simultaneously", "OGC"); ?></label></th>
        <td>
          <input required name="OGC_options[maxUpdateEachTime]" step="1" min="1" id="maxUpdateEachTime" value="<?php echo $this->maxUpdateEachTime; ?>" class="small-text" type="number" oninvalid="this.setCustomValidity('<?php _e("Please enter a whole number greater than or equal to 1.", "OGC");?>')" oninput="this.setCustomValidity('')">
          <?php $this->maxUpdateEachTime <2 ? _e("user", "OGC") : _e("users", "OGC"); ?>
          <p class="description">
            <?php _e("This option allows you to specify how many users Gravatars will be checked on gravatar.com simultaneously. Checks are performed in batches, to prevent overloading your server. Start with a smaller number and increase as necessary.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="cacheDirectory"><?php _e("Gravatar cache directory", "OGC"); ?></label></th>
        <td>
          <?php echo ABSPATH; ?><input required pattern="^((?!\./).)*$" name="OGC_options[cacheDirectory]" id="cacheDirectory" value="<?php echo $this->cacheDirectory; ?>" class="regular-text ltr" type="text" oninvalid="this.setCustomValidity('<?php _e("Enter the directory where you want to save the cache files, eg: wp-content/uploads/optimum-gravatar-cache/", "OGC");?>')" oninput="this.setCustomValidity('')">
          <p class="description">
            <?php _e("This option allows you to specify the directory where cached files will be saved. Gravatar images will be stored in this directory, as well as all files that are created by the plugin.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="directoriesMode"><?php _e("Cache directory permissions", "OGC"); ?></label></th>
        <td><input required pattern="[0-7]{4}" maxlength="4" name="OGC_options[directoriesMode]" id="directoriesMode" value="<?php echo $this->directoriesMode; ?>" class="small-text" type="text" oninvalid="this.setCustomValidity('<?php _e("Please enter an octal value for the permissions, eg: 0777", "OGC");?>')" oninput="this.setCustomValidity('')">
          <p class="description">
            <?php _e("To allow better control over cache permissions, configure the mode that you want to use when creating any directories for the cache in the file system. You need to enter the OCTAL value.", "OGC"); ?>
          </p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="avatarsMode"><?php _e("Gravatar files permissions", "OGC"); ?></label></th>
        <td><input required pattern="[0-7]{4}" maxlength="4" name="OGC_options[avatarsMode]" id="avatarsMode" value="<?php echo $this->avatarsMode; ?>" class="small-text" type="text" oninvalid="this.setCustomValidity('<?php _e("Please enter an octal value for the permissions, eg: 0777", "OGC");?>')" oninput="this.setCustomValidity('')">
          <p class="description">
            <?php _e("To allow better control over cache permissions, configure the mode that you want to use when creating and updating Gravatars as well as the lock files for the cache in the file system. You need to enter the OCTAL value.", "OGC"); ?>
          </p>
        </td>
      </tr>


      <tr>
        <th scope="row"><label for="avatarUsedSizes"><?php _e("Gravatar sizes used", "OGC"); ?></label></th>
        <td><input required pattern="^(([0-9]+,? ?)+)$" name="OGC_options[avatarUsedSizes]" id="avatarUsedSizes" value="<?php echo implode(", ", $this->avatarUsedSizes); ?>" class="regular-text ltr" type="text" oninvalid="this.setCustomValidity('<?php _e("Please enter the used sizes separated by commas, eg: 20,40,64", "OGC");?>')" oninput="this.setCustomValidity('')">
          <p class="description">
            <?php _e("This field allows you to manually add Gravatar sizes that are used in your site by plugins and/or themes. It contains a list of sizes separated by commas. This field will always be updated if/when a new size is detected as being used.", "OGC"); ?>
          </p>
        </td>
      </tr>
    </table>
    <p class="submit">
      <button type="submit" name="updateCacheOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
      <button class="button button-primary" name="clearCache" <?php echo disabled($this->firstSave, 0, true) ?>><?php _e("Clear Cache", "OGC"); ?>
        <span class="clear-count"><?php echo '('.$fileCacheInfo['images'] .' '.($fileCacheInfo['images'] <2 ? __("image", "OGC") : __("images", "OGC")).' / '.$fileCacheInfo['usedSpace'].')' ?></span>
  </button>
  <button type="submit" name="syncUsersAndCommenters" class="button button-primary"><?php _e("Sync users and commenters now", "OGC"); ?></button>

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
