+function(d) {
  function f(a) {
    var b = a.attr("data-target");
    b || (b = (b = a.attr("href")) && /#[A-Za-z]/.test(b) && b.replace(/.*(?=#[^\s]*$)/, ""));
    return (b = b && d(b)) && b.length ? b : a.parent();
  }
  function g(a) {
    a && 3 === a.which || (d(".dropdown-backdrop").remove(), d('[data-toggle="dropdown"]').each(function() {
      var b = d(this), c = f(b), e = {relatedTarget:this};
      !c.hasClass("open") || a && "click" == a.type && /input|textarea/i.test(a.target.tagName) && d.contains(c[0], a.target) || (c.trigger(a = d.Event("hide.bs.dropdown", e)), a.isDefaultPrevented() || (b.attr("aria-expanded", "false"), c.removeClass("open").trigger(d.Event("hidden.bs.dropdown", e))));
    }));
  }
  var e = function(a) {
    d(a).on("click.bs.dropdown", this.toggle);
  };
  e.VERSION = "3.3.6";
  e.prototype.toggle = function(a) {
    var b = d(this);
    if (!b.is(".disabled, :disabled")) {
      var c = f(b);
      a = c.hasClass("open");
      g();
      if (!a) {
        if ("ontouchstart" in document.documentElement && !c.closest(".navbar-nav").length) {
          d(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(d(this)).on("click", g);
        }
        var e = {relatedTarget:this};
        c.trigger(a = d.Event("show.bs.dropdown", e));
        if (a.isDefaultPrevented()) {
          return;
        }
        b.trigger("focus").attr("aria-expanded", "true");
        c.toggleClass("open").trigger(d.Event("shown.bs.dropdown", e));
      }
      return !1;
    }
  };
  e.prototype.keydown = function(a) {
    if (/(38|40|27|32)/.test(a.which) && !/input|textarea/i.test(a.target.tagName)) {
      var b = d(this);
      a.preventDefault();
      a.stopPropagation();
      if (!b.is(".disabled, :disabled")) {
        var c = f(b), e = c.hasClass("open");
        if (!e && 27 != a.which || e && 27 == a.which) {
          return 27 == a.which && c.find('[data-toggle="dropdown"]').trigger("focus"), b.trigger("click");
        }
        b = c.find(".dropdown-menu li:not(.disabled):visible a");
        b.length && (c = b.index(a.target), 38 == a.which && 0 < c && c--, 40 == a.which && c < b.length - 1 && c++, ~c || (c = 0), b.eq(c).trigger("focus"));
      }
    }
  };
  var h = d.fn.dropdown;
  d.fn.dropdown = function(a) {
    return this.each(function() {
      var b = d(this), c = b.data("bs.dropdown");
      c || b.data("bs.dropdown", c = new e(this));
      "string" == typeof a && c[a].call(b);
    });
  };
  d.fn.dropdown.Constructor = e;
  d.fn.dropdown.noConflict = function() {
    d.fn.dropdown = h;
    return this;
  };
  d(document).on("click.bs.dropdown.data-api", g).on("click.bs.dropdown.data-api", ".dropdown form", function(a) {
    a.stopPropagation();
  }).on("click.bs.dropdown.data-api", '[data-toggle="dropdown"]', e.prototype.toggle).on("keydown.bs.dropdown.data-api", '[data-toggle="dropdown"]', e.prototype.keydown).on("keydown.bs.dropdown.data-api", ".dropdown-menu", e.prototype.keydown);
}(jQuery);
