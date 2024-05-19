/* global gtplug */

gtplug.aff = function() {
  $.get(
    zbp.options.bloghost + "zb_users/plugin/xnxf_aff/ajaxAdd.php",
    function() {
      if ($("#aff-index").is(":checked")) {
        location.href = bloghost;
      } else {
        $("#aff-geetest,.modal-backdrop").removeClass("in").fadeOut(777);
      }
    },
  );
};
$(function() {
  const $modal = $("#aff-geetest");
  $modal.after("<div class=\"modal-backdrop fade\"></div>");
  $modal.append(
    "<label for=\"aff-index\"><input id=\"aff-index\" name=\"aff-index\" type=\"checkbox\" checked value=\"1\" /> 引导至首页</label>",
  );
  $modal
    .css({
      top: ($(".aff-form").height() - $modal.height()) / 2 - 37 + "px",
      left: ($(".aff-form").width() - $modal.width()) / 2 + "px",
    })
    .addClass("in");
  $(".modal-backdrop").addClass("in");
});
