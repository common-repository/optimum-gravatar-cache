<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
?>

<table class="form-table">
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Sizes used", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $dbCacheInfo['sizes']?>
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("MimeTypes Used", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $fileCacheInfo['typesUsed']?>
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Cached Gravatars", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $dbCacheInfo['total']?>
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Number of users and commenters have a custom Gravatar", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $dbCacheInfo['custom'] ?> &#8773; <?php echo $dbCacheInfo['customPercent'] ?>%
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Number of users and commenters do not have a custom Gravatar", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $dbCacheInfo['default']?> &#8773; <?php echo $dbCacheInfo['defaultPercent'] ?>%
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Number of Gravatars that have been resolved", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $this->resolved; ?>
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Gravatars on disk", "OGC"); ?></th>
    <td class="column-columnname">
      <?php echo $fileCacheInfo['images']?>
    </td>
  </tr>
  <tr valign="top">
    <th class="column-columnname column-primary" scope="row">
      <?php _e("Disk space used by cached Gravatars", "OGC"); ?>
    </th>
    <td class="column-columnname">
      <?php echo $fileCacheInfo['usedSpace']?>
    </td>
  </tr>
</table>
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
