$(function() {
  $(".checkbox_table tr").click(function(a) {
    var c = $(this), b = c.find("input");
    "INPUT" != a.target.nodeName && (b.prop("checked") ? b.prop("checked", !1) : b.prop("checked", !0));
    b.prop("checked") ? c.addClass("checked") : c.removeClass("checked");
  });
  $(document).on("mousedown", "span.combobox", function(a) {
    "INPUT" != a.target.nodeName && (a.preventDefault(), $(this).find("input").focus());
  });
  $(document).on("focus", "input.combobox", function() {
    var a = $(this).removeClass("combobox"), c = a.parent(), b = $(c.data("source")).data("json"), b = a.not(":ui-autocomplete").autocomplete({source:b, delay:100, minLength:1, appendTo:"#gp_admin_boxc", select:function(b, d) {
      if (d.item) {
        return a.val(d.item[0]), c.css({"border-color":""}), !1;
      }
    }}), e = "autocomplete";
    b.data("ui-autocomplete") && (e = "ui-autocomplete");
    b.data(e)._renderItem = function(b, a) {
      return $("<li></li>").data("item.autocomplete", a).append("<a>" + a[0] + "<span>" + a[1] + "</span></a>").appendTo(b);
    };
  });
});
