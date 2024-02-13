(function(b) {
  b.widget("ui.nestedSortable", b.extend({}, b.ui.sortable.prototype, {options:{tabSize:20, disableNesting:"ui-nestedSortable-no-nesting", errorClass:"ui-nestedSortable-error", listType:"ol", maxLevels:0, noJumpFix:0}, _create:function() {
    0 == this.noJumpFix && this.element.height(this.element.height());
    this.element.data("sortable", this.element.data("nestedSortable"));
    return b.ui.sortable.prototype._create.apply(this, arguments);
  }, _mouseDrag:function(a) {
    this.position = this._generatePosition(a);
    this.positionAbs = this._convertPositionTo("absolute");
    this.lastPositionAbs || (this.lastPositionAbs = this.positionAbs);
    if (this.options.scroll) {
      var d = this.options, c = !1;
      this.scrollParent[0] != document && "HTML" != this.scrollParent[0].tagName ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - a.pageY < d.scrollSensitivity ? this.scrollParent[0].scrollTop = c = this.scrollParent[0].scrollTop + d.scrollSpeed : a.pageY - this.overflowOffset.top < d.scrollSensitivity && (this.scrollParent[0].scrollTop = c = this.scrollParent[0].scrollTop - d.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - a.pageX < d.scrollSensitivity ? 
      this.scrollParent[0].scrollLeft = c = this.scrollParent[0].scrollLeft + d.scrollSpeed : a.pageX - this.overflowOffset.left < d.scrollSensitivity && (this.scrollParent[0].scrollLeft = c = this.scrollParent[0].scrollLeft - d.scrollSpeed)) : (a.pageY - b(document).scrollTop() < d.scrollSensitivity ? c = b(document).scrollTop(b(document).scrollTop() - d.scrollSpeed) : b(window).height() - (a.pageY - b(document).scrollTop()) < d.scrollSensitivity && (c = b(document).scrollTop(b(document).scrollTop() + 
      d.scrollSpeed)), a.pageX - b(document).scrollLeft() < d.scrollSensitivity ? c = b(document).scrollLeft(b(document).scrollLeft() - d.scrollSpeed) : b(window).width() - (a.pageX - b(document).scrollLeft()) < d.scrollSensitivity && (c = b(document).scrollLeft(b(document).scrollLeft() + d.scrollSpeed)));
      !1 !== c && b.ui.ddmanager && !d.dropBehaviour && b.ui.ddmanager.prepareOffsets(this, a);
    }
    this.positionAbs = this._convertPositionTo("absolute");
    this.options.axis && "y" == this.options.axis || (this.helper[0].style.left = this.position.left + "px");
    this.options.axis && "x" == this.options.axis || (this.helper[0].style.top = this.position.top + "px");
    for (c = this.items.length - 1;0 <= c;c--) {
      var g = this.items[c], e = g.item[0], f = this._intersectsWithPointer(g);
      if (f && e != this.currentItem[0] && this.placeholder[1 == f ? "next" : "prev"]()[0] != e && !b.contains(this.placeholder[0], e) && ("semi-dynamic" == this.options.type ? !b.contains(this.element[0], e) : 1)) {
        this.direction = 1 == f ? "down" : "up";
        if ("pointer" == this.options.tolerance || this._intersectsWithSides(g)) {
          this._rearrange(a, g);
        } else {
          break;
        }
        this._clearEmpty(e);
        this._trigger("change", a, this._uiHash());
        break;
      }
    }
    c = this.placeholder[0].parentNode.parentNode && b(this.placeholder[0].parentNode.parentNode).closest(".ui-sortable").length ? b(this.placeholder[0].parentNode.parentNode) : null;
    g = this._getLevel(this.placeholder);
    e = this._getChildLevels(this.helper);
    f = this.placeholder[0].previousSibling ? b(this.placeholder[0].previousSibling) : null;
    if (null != f) {
      for (;"li" != f[0].nodeName.toLowerCase() || f[0] == this.currentItem[0];) {
        if (f[0].previousSibling) {
          f = b(f[0].previousSibling);
        } else {
          f = null;
          break;
        }
      }
    }
    newList = document.createElement(d.listType);
    this.beyondMaxLevels = 0;
    null != c && this.positionAbs.left < c.offset().left ? (c.after(this.placeholder[0]), this._clearEmpty(c[0]), this._trigger("change", a, this._uiHash())) : null != f && this.positionAbs.left > f.offset().left + d.tabSize ? (this._isAllowed(f, g + e + 1), null == f[0].children[1] && f[0].appendChild(newList), f[0].children[1].appendChild(this.placeholder[0]), this._trigger("change", a, this._uiHash())) : this._isAllowed(c, g + e);
    this._contactContainers(a);
    b.ui.ddmanager && b.ui.ddmanager.drag(this, a);
    this._trigger("sort", a, this._uiHash());
    this.lastPositionAbs = this.positionAbs;
    return !1;
  }, _mouseStop:function(a, d) {
    if (this.beyondMaxLevels) {
      for (var c = this.placeholder.parent().closest(this.options.items), g = this.beyondMaxLevels - 1;0 < g;g--) {
        c = c.parent().closest(this.options.items);
      }
      this.placeholder.removeClass(this.options.errorClass);
      c.after(this.placeholder);
      this._trigger("change", a, this._uiHash());
    }
    b.ui.sortable.prototype._mouseStop.apply(this, arguments);
  }, serialize:function(a) {
    var d = this._getItemsAsjQuery(a && a.connected), c = [];
    a = a || {};
    b(d).each(function() {
      var d = (b(a.item || this).attr(a.attribute || "id") || "").match(a.expression || /(.+)[-=_](.+)/), e = (b(a.item || this).parent(a.listType).parent("li").attr(a.attribute || "id") || "").match(a.expression || /(.+)[-=_](.+)/);
      d && c.push((a.key || d[1] + "[" + (a.key && a.expression ? d[1] : d[2]) + "]") + "=" + (e ? a.key && a.expression ? e[1] : e[2] : "root"));
    });
    !c.length && a.key && c.push(a.key + "=");
    return c.join("&");
  }, toHierarchy:function(a) {
    function d(c) {
      var e = (b(c).attr(a.attribute || "id") || "").match(a.expression || /(.+)[-=_](.+)/);
      if (null != e) {
        var f = {id:e[2]};
        0 < b(c).children(a.listType).children("li").length && (f.children = [], b(c).children(a.listType).children("li").each(function() {
          var a = d(b(this));
          f.children.push(a);
        }));
        return f;
      }
    }
    a = a || {};
    var c = [];
    b(this.element).children("li").each(function() {
      var a = d(b(this));
      c.push(a);
    });
    return c;
  }, toArray:function(a) {
    function d(f, e, h) {
      right = h + 1;
      0 < b(f).children(a.listType).children("li").length && (e++, b(f).children(a.listType).children("li").each(function() {
        right = d(b(this), e, right);
      }), e--);
      id = b(f).attr(a.attribute || "id").match(a.expression || /(.+)[-=_](.+)/);
      e === c + 1 ? pid = "root" : (parentItem = b(f).parent(a.listType).parent("li").attr("id").match(a.expression || /(.+)[-=_](.+)/), pid = parentItem[2]);
      null != id && g.push({item_id:id[2], parent_id:pid, depth:e, left:h, right:right});
      return h = right + 1;
    }
    a = a || {};
    var c = a.startDepthCount || 0, g = [], e = 2;
    g.push({item_id:"root", parent_id:"none", depth:c, left:"1", right:2 * (b("li", this.element).length + 1)});
    b(this.element).children("li").each(function() {
      e = d(this, c + 1, e);
    });
    return g = g.sort(function(a, c) {
      return a.left - c.left;
    });
  }, _clear:function(a, d) {
    b.ui.sortable.prototype._clear.apply(this, arguments);
    for (var c = this.items.length - 1;0 <= c;c--) {
      this._clearEmpty(this.items[c].item[0]);
    }
    return !0;
  }, _clearEmpty:function(a) {
    a.children[1] && 0 == a.children[1].children.length && a.removeChild(a.children[1]);
  }, _getLevel:function(a) {
    var b = 1;
    if (this.options.listType) {
      for (a = a.closest(this.options.listType);!a.is(".ui-sortable");) {
        b++, a = a.parent().closest(this.options.listType);
      }
    }
    return b;
  }, _getChildLevels:function(a, d) {
    var c = this, g = this.options, e = 0;
    d = d || 0;
    b(a).children(g.listType).children(g.items).each(function(a, b) {
      e = Math.max(c._getChildLevels(b, d + 1), e);
    });
    return d ? e + 1 : e;
  }, _isAllowed:function(a, b) {
    var c = this.options;
    null != a && a.hasClass(c.disableNesting) ? (this.placeholder.addClass(c.errorClass), this.beyondMaxLevels = 0 < b - c.maxLevels ? b - c.maxLevels : 1) : c.maxLevels < b && 0 != c.maxLevels ? (this.placeholder.addClass(c.errorClass), this.beyondMaxLevels = b - c.maxLevels) : (this.placeholder.removeClass(c.errorClass), this.beyondMaxLevels = 0);
  }}));
  b.ui.nestedSortable.prototype.options = b.extend({}, b.ui.sortable.prototype.options, b.ui.nestedSortable.prototype.options);
})(jQuery);
