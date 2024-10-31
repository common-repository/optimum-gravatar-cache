jQuery(document).ready(function($) {
  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#default-avatar, #costomPlaceholder').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#upload").change(function() {
    readURL(this);
  });
  $("#placeholderFile").change(function() {
    readURL(this);
  });
  $('#updateHtaccess').change(function() {
    $("#deleteHtaccess").prop( "checked", false );
  });
  $('#deleteHtaccess').change(function() {
    $("#updateHtaccess").prop( "checked", false );
  });
});
