+function(b) {
  function k(e) {
    return this.each(function() {
      var a = b(this), d = a.data("bs.affix"), g = "object" == typeof e && e;
      d || a.data("bs.affix", d = new c(this, g));
      if ("string" == typeof e) {
        d[e]();
      }
    });
  }
  var c = function(e, a) {
    this.options = b.extend({}, c.DEFAULTS, a);
    this.$target = b(this.options.target).on("scroll.bs.affix.data-api", b.proxy(this.checkPosition, this)).on("click.bs.affix.data-api", b.proxy(this.checkPositionWithEventLoop, this));
    this.$element = b(e);
    this.pinnedOffset = this.unpin = this.affixed = null;
    this.checkPosition();
  };
  c.VERSION = "3.3.6";
  c.RESET = "affix affix-top affix-bottom";
  c.DEFAULTS = {offset:0, target:window};
  c.prototype.getState = function(b, a, d, c) {
    var f = this.$target.scrollTop(), h = this.$element.offset(), k = this.$target.height();
    if (null != d && "top" == this.affixed) {
      return f < d ? "top" : !1;
    }
    if ("bottom" == this.affixed) {
      return null != d ? f + this.unpin <= h.top ? !1 : "bottom" : f + k <= b - c ? !1 : "bottom";
    }
    var l = null == this.affixed, h = l ? f : h.top;
    return null != d && f <= d ? "top" : null != c && h + (l ? k : a) >= b - c ? "bottom" : !1;
  };
  c.prototype.getPinnedOffset = function() {
    if (this.pinnedOffset) {
      return this.pinnedOffset;
    }
    this.$element.removeClass(c.RESET).addClass("affix");
    var b = this.$target.scrollTop();
    return this.pinnedOffset = this.$element.offset().top - b;
  };
  c.prototype.checkPositionWithEventLoop = function() {
    setTimeout(b.proxy(this.checkPosition, this), 1);
  };
  c.prototype.checkPosition = function() {
    if (this.$element.is(":visible")) {
      var e = this.$element.height(), a = this.options.offset, d = a.top, g = a.bottom, f = Math.max(b(document).height(), b(document.body).height());
      "object" != typeof a && (g = d = a);
      "function" == typeof d && (d = a.top(this.$element));
      "function" == typeof g && (g = a.bottom(this.$element));
      a = this.getState(f, e, d, g);
      if (this.affixed != a) {
        null != this.unpin && this.$element.css("top", "");
        var d = "affix" + (a ? "-" + a : ""), h = b.Event(d + ".bs.affix");
        this.$element.trigger(h);
        if (h.isDefaultPrevented()) {
          return;
        }
        this.affixed = a;
        this.unpin = "bottom" == a ? this.getPinnedOffset() : null;
        this.$element.removeClass(c.RESET).addClass(d).trigger(d.replace("affix", "affixed") + ".bs.affix");
      }
      "bottom" == a && this.$element.offset({top:f - e - g});
    }
  };
  var m = b.fn.affix;
  b.fn.affix = k;
  b.fn.affix.Constructor = c;
  b.fn.affix.noConflict = function() {
    b.fn.affix = m;
    return this;
  };
  b(window).on("load", function() {
    b('[data-spy="affix"]').each(function() {
      var c = b(this), a = c.data();
      a.offset = a.offset || {};
      null != a.offsetBottom && (a.offset.bottom = a.offsetBottom);
      null != a.offsetTop && (a.offset.top = a.offsetTop);
      k.call(c, a);
    });
  });
}(jQuery);
