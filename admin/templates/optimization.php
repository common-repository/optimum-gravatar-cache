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
    <th scope="row"><?php _e("Optimize Gravatars", "OGC"); ?></th>
    <td><fieldset><legend class="screen-reader-text"><span><?php _e("Optimize Gravatars", "OGC"); ?></span></legend>
      <label for="optimizeAvatars"><input name="OGC_options[optimizeAvatars]" id="optimizeAvatars" value="1" type="checkbox" <?php checked(1, $this->optimizeAvatars); ?>><?php _e("Optimize Gravatars", "OGC"); ?></label>
      <p class="description"><?php _e("Cached Gravatars are optimised via the popular online service 'resmush.it'. This plugin needs to communicate with this service in order to optimize cached Gravatars. It is not mandatory to use optimized avatars. However, by optimizing any cached Gravatars, they will be loaded faster by site visitors, resulting in a more reponsive site and a better user experience.", "OGC"); ?></p>
    </fieldset></td>
    </tr>

    <tr>
    <th scope="row"><label for="maxOptimizeEachTime"><?php _e("Number of cached Gravatars to optimise simultaneously", "OGC"); ?></label></th>
    <td>
    <input name="OGC_options[maxOptimizeEachTime]" step="1" min="1" id="maxOptimizeEachTime" value="<?php echo $this->maxOptimizeEachTime; ?>" class="small-text" type="number" oninvalid="this.setCustomValidity('<?php _e("Please enter a whole number greater than or equal to 1.", "OGC");?>')" oninput="this.setCustomValidity('')"> <?php $this->maxOptimizeEachTime <2 ? _e("Gravatar", "OGC") : _e("Gravatars", "OGC"); ?>
    <p class="description"><?php _e("The number of Gravatars you set in this option will be optimized whenever background tasks run and will only run when there are Gravatars that require optimization.", "OGC"); ?></p>
    </td>
    </tr>

    <tr>
    <th scope="row"><?php _e("Pre-compress .SVG files", "OGC"); ?></th>
    <td><fieldset><legend class="screen-reader-text"><span><?php _e("Pre-compress .SVG files", "OGC"); ?></span></legend>
      <label for="precompress"><input name="OGC_options[precompress]" id="precompress" value="1" type="checkbox" <?php checked(1, $this->precompress); ?>><?php _e("Pre-compress Gravatars that are .SVG format", "OGC"); ?></label>
      <p class="description"><?php _e("Using pre-compressed .SVG files allows you to save resources on your server. This is because the files are compressed and ready to be served with no need for the server to perform any optimization/compression to serve them every time they are requested. However, the server must be configured to use pre-compressed .svg files.", "OGC"); ?></p>
    </fieldset></td>
    </tr>
  </table>
  <p class="submit">
    <button type="submit" name="updateOptimizationOptions" id="submit" class="button button-primary"><?php _e("Save Changes"); ?></button>
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
