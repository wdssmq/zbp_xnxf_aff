gtplug.aff = function () {
  $.get(zbp.options.bloghost + "zb_users/plugin/xnxf_aff/ajaxAdd.php", function () {
    $("#xnxf-geetest,.modal-backdrop").removeClass("in").fadeOut(777);
  });
}
$(function () {
  $("#xnxf-geetest").after('<div class="modal-backdrop fade"></div>');
  var $modal = $("#xnxf-geetest");
  $modal.css({
    top: ($(window).height() - $modal.height()) / 2 - 57 + "px",
    left: ($(window).width() - $modal.width()) / 2 + "px"
  }).addClass("in");
  $(".modal-backdrop").addClass("in");
});
