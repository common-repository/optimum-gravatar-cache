<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
?>


<table class="form-table">
  <tr>
    <th>
<?php _e("Example configuration for NGinx servers.", "OGC"); ?>    </th>
    <td>
      <textarea rows="20" class="large-text code" readonly>location ~* ^/cache/avatar/.*\.(jpg|png|gif|svg)$ {
allow all;
gzip_static  on;
etag off;
expires off;
add_header Cache-Control "max-age=0";
}
location ~* ^/cache/avatar/.*$ {
deny all;
}</textarea>
      <p class="description">
<?php _e("The text box contains an example configuration for NGinx servers. These settings may not be appropriate for your use case.<br><br>This example configuration has several functions:<br>1. Deny access to list the cache directories;<br>2. Allow pre-compressed .SVG files to be used;<br>3. Force Gravatars to be revalidated by browsers.<br><br>This configuration may not be appropriate for your site/project/server, so please revise and adapt it as required.<br><br>For NGinx servers, an administrator must manually configure the server.", "OGC"); ?>
      </p>
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
