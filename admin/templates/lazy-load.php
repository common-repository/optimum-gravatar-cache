<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
?>

  <form method="post" class="gravatar-cache-form" enctype="multipart/form-data">
    <input type="hidden" name="OGC_options[nonce]" value="<?php echo wp_create_nonce("OGC"); ?>"/>
    <table class="form-table">
      <tr>
      <th scope="row"><?php _e("Use Lazy Load", "OGC"); ?></th>
      <td><fieldset><legend class="screen-reader-text"><span><?php _e("Use Lazy Load", "OGC"); ?></span></legend>
        <label for="lazyLoad"><input name="OGC_options[lazyLoad]" id="lazyLoad" value="1" type="checkbox" <?php checked(1, $this->lazyLoad); ?>><?php _e("Use Lazy Load with Gravatars", "OGC"); ?></label>
        <p class="description"><?php _e("Using Lazy Load allows only visible Gravatars to be downloaded, allowing higher priority content to download and be displayed faster. Lazy Loading also saves data on mobile/slow networks, among a list of other benefits! Search 'Lazy Load' on Google for more information.", "OGC"); ?></p>
      </fieldset></td>
      </tr>

      <tr>
      <th scope="row"><?php _e("Only shown on the frontend", "OGC"); ?></th>
      <td><fieldset><legend class="screen-reader-text"><span><?php _e("Only shown on the frontend", "OGC"); ?></span></legend>
        <label for="onlyOnTheFrontend"><input name="OGC_options[onlyOnTheFrontend]" id="onlyOnTheFrontend" value="1" type="checkbox" <?php checked(1, $this->onlyOnTheFrontend); ?>><?php _e("Use Lazy Load only on site frontend", "OGC"); ?></label>
        <p class="description"><?php _e("This option prevents Lazy Load from being run on admin pages. Lazy Load will only be applied to non-admin/non-logged-in users.", "OGC"); ?></p>
      </fieldset></td>
      </tr>

      <tr>
        <th scope="row">
          <?php _e("Where to insert the script", "OGC"); ?>
        </th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e("Where to insert the script", "OGC"); ?></span></legend>
            <label><input name='OGC_options[scriptInFooter]' value="0" type="radio" <?php checked(0, $this->scriptInFooter); ?>>  <?php _e("In the Page Header", "OGC"); ?></label><br>
            <label><input name='OGC_options[scriptInFooter]' value="1" type="radio" <?php checked(1, $this->scriptInFooter); ?>> <?php _e("In the page footer", "OGC"); ?></label>
            <p class="description">
              <?php _e("Depending on your project/use-case, it may be more appropriate to manually insert the script into either the header or footer of your site. Placing the script in the footer of your site will mean the script is run after the rest of the page has been loaded. Placing the script in the header of your site could result in render-blocking. Please choose the location that best suits your project.", "OGC"); ?>
            </p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="offset"><?php _e("Download Gravatars that are this distance from being scrolled into view", "OGC"); ?></label></th>
        <td>
          <input required name="OGC_options[offset]" step="1" min="1" id="offset" value="<?php echo $this->offset; ?>" class="small-text" type="number"  oninvalid="this.setCustomValidity('<?php _e("Please enter a whole number greater than or equal to 1.", "OGC");?>')" oninput="this.setCustomValidity('')">
          <?php $this->offset <2 ? _e("pixel", "OGC") : _e("pixels", "OGC"); ?>
          <p class="description">
            <?php _e("This option allows you to specify the distance in pixels in which Gravatars should be loaded before scrolling into view. Gravatars will be loaded before they are visible on screen.", "OGC"); ?>
          </p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="notInThisClass"><?php _e("Ignore Gravatars with this class", "OGC"); ?></label></th>
        <td><input name="OGC_options[notInThisClass]" id="notInThisClass" value="<?php echo $this->notInThisClass; ?>" class="regular-text ltr" type="text">
          <p class="description">
            <?php _e("This option prevents Lazy Load being used on the Gravatars that have this class applied. You can pass classes through to Gravatars via the Wordpress get_avatar() function or the BuddyPress bp_core_fetch_avatar() function, in your themes and/or plugins. If this class is detected on a Gravatar, Lazy Load will not be applied to it.", "OGC"); ?>
          </p>
        </td>
      </tr>

      <tr>
      <th scope="row"><?php _e("Use placeholder image", "OGC"); ?></th>
      <td><fieldset><legend class="screen-reader-text"><span><?php _e("Use placeholder image", "OGC"); ?></span></legend>
        <label for="usePlaceholder"><input name="OGC_options[usePlaceholder]" id="usePlaceholder" value="1" type="checkbox" <?php checked(1, $this->usePlaceholder); ?>><?php _e("Use the placeholder image to animate the Gravatar download", "OGC"); ?></label>
        <p class="description"><?php _e("Use a placeholder image that is displayed to users while Gravatars are being downloaded. This is important when on a slow connection, as it can take some time to download and cache Gravatars.", "OGC"); ?></p>
      </fieldset></td>
      </tr>

      <tr>
      <th scope="row"><?php _e("Placeholder image", "OGC"); ?></th>
      <td>
          <img id="costomPlaceholder" alt="" src="<?php echo $this->getCurrentPlaceholder(); ?>" class="avatar avatar-96 photo" width="96" height="96" />
          <p class="description"><?php _e("This image will always be used as a placeholder for Gravatars.", "OGC"); ?></p>
      </td>
      </tr>

      <tr>
      <th scope="row"><label for="placeholderFile"><?php _e("Upload placeholder image", "OGC"); ?></label></th>
      <td><input type='file' id="placeholderFile" name='file' accept='image/jpeg, image/png, image/gif, image/svg+xml'>
      <p class="description"><?php _e("This image will be used as a placeholder image. The following file types are accepted: .svg, .png, .jpg, .gif. It is advised that this file is small, so that it loads very quickly.", "OGC"); ?></p>
      </td>
      </tr>

    </table>
    <p class="submit">
      <button type="submit" name="updateLazyLoadOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
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
