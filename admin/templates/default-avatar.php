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
      <th scope="row"><?php _e("Default/Placeholder Gravatar", "OGC"); ?></th>
      <td>
          <img id="default-avatar" alt="" src="<?php echo $this->getCurrentDefaultAvatar() ?>" class="avatar avatar-96 photo" width="96" height="96" />
          <p class="description"><?php _e("The default Gravatar is used whenever the user does not have a custom Gravatar. It should be configured with an image that fits your site.", "OGC"); ?></p>
      </td>
      </tr>

      <tr>
      <th scope="row"><label for="upload"><?php _e("Upload custom Gravatar", "OGC"); ?></label></th>
      <td><input type='file' id="upload" name='file' accept='image/jpeg, image/png, image/gif, image/svg+xml'>
      <p class="description"><?php _e("This image will be used as the default Gravatar. The following file types are accepted: .svg, .png, .jpg, .gif. This image should have the minimum dimensions of the largest Gravatar used on your site (except for .SVG files), so that the Gravatar does not lose quality when it is resized.", "OGC"); ?></p>
      </td>
      </tr>


      <tr>
      <th scope="row"><?php _e("Reset default/placeholder Gravatar", "OGC"); ?></th>
      <td><fieldset><legend class="screen-reader-text"><span><?php _e("Reset default/placeholder Gravatar", "OGC"); ?></span></legend>
      	<label for="resetDefaultAvatar"><input name="OGC_options[resetDefaultAvatar]" id="resetDefaultAvatar" value="1" type="checkbox"><?php _e("Reset default/placeholder Gravatar", "OGC"); ?></label>
      	<p class="description"><?php _e("This option allows you to reset the default Gravatar to use one originally provided by the plugin. This will remove any custom placeholder Gravatar you have set.", "OGC"); ?></p>
      </fieldset></td>
      </tr>

  </table>
  <p class="submit">
    <button type="submit" name="updateDefaultAvatarOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
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
