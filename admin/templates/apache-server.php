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
        <th>
          <?php _e("Example .htaccess file for Apache servers.", "OGC"); ?>
        </th>
        <td>
          <textarea rows="20" class="large-text code" name="OGC_options[apacheHtaccess]"><?php echo $apacheConfig; ?></textarea>
          <p class="description">
          <?php _e("The text box contains an example configuration for Apache servers. These settings may not be appropriate for your use case.<br><br>This example configuration has several functions:<br>1. Deny access to list the cache directories;<br>2. Allow pre-compressed .SVG files to be used;<br>3. Force Gravatars to be revalidated by browsers.<br><br>This configuration may not be appropriate for your site/project/server, so please revise and adapt it as required.", "OGC"); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th>
          <?php _e("Save or update the .htaccess file", "OGC"); ?>
        </th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e("Save or update the .htaccess file", "OGC"); ?></span></legend>
            <label for="updateHtaccess"><input name="OGC_options[updateHtaccess]" id="updateHtaccess" value="1" type="checkbox"><?php _e("Save or update the .htaccess file in the cache directory", "OGC"); ?></label>
            <p class="description">
              <?php _e("Allows you to save or refresh the content entered above, in the .htaccess file that is stored in the cache directory.", "OGC"); ?>
            </p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <th><?php _e("Delete the .htaccess file", "OGC"); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><span><?php _e("Delete the .htaccess file", "OGC"); ?></span></legend>
            <label for="deleteHtaccess"><input name="OGC_options[deleteHtaccess]" id="deleteHtaccess" value="1" type="checkbox"><?php _e("Delete the .htaccess file that is stored in the cache directory", "OGC"); ?></label>
            <p class="description">
              <?php _e("Allows you to delete the .htaccess file that is stored in the cache directory.", "OGC"); ?>
            </p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="htaccessMode"><?php _e(".htaccess file permissions", "OGC"); ?></label></th>
        <td><input required pattern="[0-7]{4}" maxlength="4" name="OGC_options[htaccessMode]" id="htaccessMode" value="<?php echo $this->htaccessMode; ?>" class="small-text" type="text" oninvalid="this.setCustomValidity('<?php _e("Please enter an octal value for the permissions, eg: 0777", "OGC");?>')" oninput="this.setCustomValidity('')">
          <p class="description">
            <?php _e("To allow better control over cache permissions, configure the mode that you want to use when the .htaccess file is created and updated in the file system. You need to enter the OCTAL value.", "OGC"); ?>
          </p>
        </td>
      </tr>
    </table>
    <p class="submit">
      <button type="submit" name="updateApacheConfiguration" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
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
