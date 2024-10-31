(function() {
  var rAF = (function() {
    return window.requestAnimationFrame ||
      function(callback) {
        window.setTimeout(callback, 0);
      };
  })();
  var eV = (function() {
    function C(e, p) {
      p = p || {
        bubbles: false,
        cancelable: false,
        detail: undefined
      };
      var eV = document.createEvent('CustomEvent');
      eV.initCustomEvent(e, p.bubbles, p.cancelable, p.detail);
      return eV;
    }
    if (typeof window.CustomEvent === "function") {
      return window.CustomEvent;
    } else {
      return C;
    }
  })();
  var now = (function() {
    return Date.now ||
      function now() {
        return new Date().getTime();
      };
  })();
  var z = 0;
  var imgs = [];
  var offset = OGC.offset;
  var aL = [];
  var body = document.body || document.documentElement;
  var event = new eV('Lazy');
  event.initEvent('update', true, true);
  var inited = false;
  var delay = 250;
  var sameAvatar = [];

  function init() {
    hNA(body);
    rAF(function() {
      lL();
      inited = true;
    });
    window.addEventListener('resize', tH(function() {
      rAF(function() {
        lL();
      });
    }), false);
    window.addEventListener('scroll', tH(function() {
      rAF(function() {
        lL();
      });
    }), false);
    window.addEventListener('update', tH(function() {
      rAF(function() {
        lL();
      });
    }), false);
    if (("MutationObserver" in window)) {
      var oB = new MutationObserver(function(mL) {
        var add = false;
        for (var i = 0; i < mL.length; i++) {
          if (mL[i].type == 'childList') {
            add = hNA(mL[i].target);
            break;
          }
        }
        if (add == true) {
          window.dispatchEvent(event);
        }
      });
      oB.observe(body, {
        attributes: true,
        childList: true,
        subtree: true
      });
    } else {
      body.addEventListener('DOMNodeInserted', tH(function(e) {
        if (hNA()) {
          window.dispatchEvent(event);
        }
      }), true);
    }
  };

  document.addEventListener('readystatechange', function(e) {
    if (document.readyState === "interactive" || document.readyState === "complete") {
      e.target.removeEventListener(e.type, arguments.callee);
      init();
    }
  }, false);

  function hNA
() {
    var has = false;
    var n = body.querySelectorAll('img.ogc-lazy');
    if (n.length) {
      aL = Array.prototype.slice.call(n);
      has = true;
    }
    return has;
  }

  function tH(f) {
    var sTO = null;
    var lT = null;
    return function() {
      var cT = this;
      var a = arguments;
      if (!lT) {
        f.apply(cT, a);
        lT = now();
      } else {
        clearTimeout(sTO);
        sTO = setTimeout(function() {
          if (now() - lT >= delay) {
            f.apply(cT, a);
            lT = null;
          }
        }, delay - (now() - lT));
      }
    };
  };

  function iIVP(el) {
    if (!el) return;
    try {
      var r = el.getBoundingClientRect();
    } catch (e) {
      return false;
    }
    var wH = (window.innerHeight || document.documentElement.clientHeight);
    var wW = (window.innerWidth || document.documentElement.clientWidth);
    var v = (r.top - offset <= wH) && ((r.top + r.height + offset) >= 0);
    var h = (r.left - offset <= wW) && ((r.left + r.width + offset) >= 0);
    return (v && h);
  }
  function rC(c, e) {
    e.className = e.className.replace(
      new RegExp('( |^)' + c + '( |$)', 'g'), ' ').trim();
  }
  function lL() {
    if (aL.length) {
      for (var a = 0; a < aL.length; a++) {
        if (aL[a]) {
          if (iIVP(aL[a])) {
            var ds = aL[a].getAttribute('data-src');
            var d = aL[a];
            var s = new Image();
            s.onload = (function(s, d) {
              return function() {
                d.src = s.src;
              }
            })(s, d);
            s.src = ds;
            rC('ogc-lazy', aL[a]);
            if (sameAvatar.indexOf(ds) === -1) {
              sameAvatar.push(ds);
            }
            aL[a].removeAttribute('data-src');
            aL[a] = null;
          } else {
            var u = aL[a].getAttribute('data-src');
            if (sameAvatar.indexOf(u) !== -1) {
              aL[a].src = u;
              rC('ogc-lazy', aL[a]);
              aL[a].removeAttribute('data-src');
              aL[a] = null;
            }
          }
        }
      }
      aL = aL.filter(function(o) {
        return o
      });
    }
  };
})();
