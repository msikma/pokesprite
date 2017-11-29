/** @preserve
 * {{$title_str}} v{{$version}} ({{$revision}}) {{$website_txt}}
 * {{$copyright_str}}
 * {{$copyright_gf}}
 * {{$copyright_contrib_notice}}
 * {{$generated_on}}
 *
 */
(function (global, factory) {
  if (typeof module === "object" && typeof module.exports === "object") {
    if (global.document) {
      module.exports = factory(global, true);
    }
    else {
      module.exports = function (w) {
        if (!w.document) {
          throw new Error("PokéSprite requires a window with a document");
        }
        return factory(w);
      };
    }
  }
  else {
    factory(global);
  }
}(typeof window !== "undefined" ? window : this, function(window, noGlobal) {

  // Extends polyfill.
  var _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };

  // PokéSprite code starts here.
  var PkSpr = {
  };
  /**
   * Base CSS class that identifies an element as ours.
   *
   * @const
   * @type {!string}
   */
  PkSpr.PKSPR_BASE_CLASS = "{{$css_base_selector}}";

  /**
   * List of types and their sizes.
   *
   * @const
   * @type {!Object}
   */
  PkSpr.PKSPR_TYPES = {{$sizes_json}};

  /**
   * Coordinate and size data for every single icon. Size data is
   * not included if the type's size can already be found
   * in the PKSPR_TYPES constant.
   *
   * @const
   * @type {!Object}
   */
  PkSpr.PKSPR_DATA = {{$coords_json}};

  /**
   * Index linking Pokédex numbers to slugs. Generated on runtime.
   *
   * @type {?Object}
   */
  var pkmn_idx_to_slug;

  /**
   * Regular Expression used to check whether an identifier
   * is a valid dex number.
   *
   * @type {?RegExp}
   */
  var numeric_regexp;

  /**
   * Schedules the DOM to be processed completely as soon as it's ready.
   */
  PkSpr["process_dom"] = function()
  {
    PkSpr.content_loaded(window, PkSpr.process_container);
  }

  /**
   * Decorates a list of objects.
   *
   * This is to be called by the user in case they want to decorate
   * specific items without having the script scan the DOM.
   *
   * The argument must either be an ID string (in which case the node
   * is fetched using document.getElementById()), or a node, or
   * an array of ID strings, or an array of nodes.
   *
   * @param {(string|Element|Array.<string, Element>)} val Item or items to be decorated.
   * @return {?object} An object, if we received an object.
   */
  PkSpr["decorate"] = function(val)
  {
    // If we've received an object, return an object with the information
    // necessary to construct a DOM object of an icon manually.
    if (PkSpr.is_object(val)) {
      return PkSpr.decorate_object(val);
    }
    // Determine what the user passed.
    var is_arr = PkSpr.is_array(val);
    // If it's not an array, turn it into one so we can iterate over it.
    if (is_arr === false) {
      val = [val];
    }

    var a, z;
    var obj, node, is_str, is_node;
    for (a = 0, z = val.length; a < z; ++a) {
      obj = val[a];
      is_str = typeof obj === "string" || obj instanceof String;
      is_node = obj.nodeName !== null;

      // Fetch the object by its ID if necessary.
      if (is_str) {
        node = document.getElementById(obj);
      }
      else {
        node = obj;
      }

      // We either have a parent object that contains icons,
      // or an icon itself.
      if (PkSpr.has_class(node, PkSpr.PKSPR_BASE_CLASS)) {
        // It's an icon.
        PkSpr.decorate_node(node);
      }
      else {
        // It's a parent object.
        PkSpr.process_container(null, node);
      }
    }
  }

  /**
   * Puts a message in the console in case of decoration failure.
   *
   * @param {Object} attrs The icon attributes.
   */
  PkSpr.decoration_error = function(attrs)
  {
    window.console && console.warn("Couldn't decorate icon with the "+
      "following properties: %o", attrs);
  }
  
  /**
   * Checks whether the computed icon attributes match
   * the user's requested attributes.
   *
   * @param {Object} attrs User attributes
   * @param {Object} missing Computed values (missing in the data)
   * @return {Object} Whether we have an exact match and the changed values
   */
  PkSpr.check_exact_match = function(attrs, missing)
  {
    var exactMatch = true;
    var changedValues = {};
    for (var defaultVal in attrs) {
      if (!attrs.hasOwnProperty(defaultVal) || attrs[defaultVal] == null) {
        continue;
      }
      for (var finalVal in missing) {
        if (!missing.hasOwnProperty(finalVal) || defaultVal !== finalVal) {
          continue;
        }
        if (attrs[defaultVal] !== missing[finalVal]) {
          exactMatch = false;
          changedValues[finalVal] = missing[finalVal];
        }
      }
    }
    return {
      "exact": exactMatch,
      "changedValues": changedValues
    };
  }
  
  /**
   * Decorates an object. See decorate_node() for details.
   *
   * @param {Object} info Our settings, e.g. name, direction, gender...
   * @return {Object} Necessary information needed to display the icon.
   */
  PkSpr.decorate_object = function(info)
  {
    var defaults = {
      "type": "pkmn",
      "slug": null,
      "color": null,
      "form": null,
      "gender": null,
      "dir": null
    };
    var attrs = _extends(defaults, info);
    var size = PkSpr.get_type_size(attrs.type);
    var data = PkSpr.get_icon_data(attrs);
    
    // Check if we have an exact match or not.
    var match = PkSpr.check_exact_match(attrs, data["missing"]);
    attrs = _extends(attrs, match["changedValues"]);
    
    var custom_size = size == null || size["w"] == null;
    if (data["coords"] == null) {
      return {
        "request": info,
        "attributes": attrs,
        "exactMatch": null,
        "found": false,
        "data": null,
        "size": null
      };
    }
    if (custom_size) {
      size = {"w": data["coords"]["w"], "h": data["coords"]["h"]};
    }
    delete data["missing"];
    return {
      "request": info,
      "attributes": attrs,
      "exactMatch": match["exact"],
      "found": true,
      "data": data,
      "size": size
    };
  }

  /**
   * Decorates a single node
   *
   * @param {Element} node The node to be decorated.
   * @return {boolean} Whether decoration was successful.
   */
  PkSpr.decorate_node = function(node)
  {
    // Check to make sure it hasn't been decorated before.
    if (PkSpr.is_decorated(node)) {
      return false;
    }

    // Get the node's base attributes.
    var attrs = PkSpr.get_node_attrs(node);
    var size = PkSpr.get_type_size(attrs.type);
    var data = PkSpr.get_icon_data(attrs);
    var coords = data["coords"];
    var props = data["props"];

    // If we were not able to gauge its size from the type,
    // that means this icon's size is stored alongside
    // the coordinate data.
    var custom_size = size == null || size["x"] == null;

    // Check whether this node's icon really exists.
    if (coords == null) {
      // If not, error out.
      PkSpr.decoration_error(attrs);
      return false;
    }
    if (custom_size) {
      size = {"w": coords["w"], "h": coords["h"]};
    }

    // Create the inner element that is the icon itself.
    var inner = PkSpr.create_inner_node(node);
    // Set background coordinates.
    PkSpr.set_icon_coords(inner, coords);
    // Set the size, if we're dealing with a custom sized icon.
    if (custom_size) {
      PkSpr.set_icon_size(node, inner, size);
    }
    // Flip the icon if we're showing a faux right-facing icon.
    if (props["flipped"]) {
      PkSpr.set_icon_direction(node, "right");
    }

    // Indicate that this node has been decorated so we don't
    // accidentally decorate it twice.
    PkSpr.set_decorated(node);

    return true;
  }

  /**
   * Adds a class to the icon signifying it is to be mirrored in CSS.
   *
   * @param {Element} node The icon node.
   * @param {string} dir Direction the icon should face.
   */
  PkSpr.set_icon_direction = function(node, dir)
  {
    PkSpr.add_class(node, "{{$var_base_name}}-faux-"+dir);
  }

  /**
   * Creates the inner node, which is an extra child element inside the
   * icon node that contains the actual icon itself.
   *
   * @param {Element} node The icon node.
   * @return {Element} The newly created inner node.
   */
  PkSpr.create_inner_node = function(node)
  {
    var inner = document.createElement("i");
    node.appendChild(inner);
    return inner;
  }

  /**
   * Sets the background-position value of an icon.
   *
   * @param {Element} inner The inner node (<i> element of the icon object).
   * @param {!Object} coords The coordinates.
   */
  PkSpr.set_icon_coords = function(inner, coords)
  {
    inner.style.backgroundPosition = (-coords["x"])+"px "+(-coords["y"])+"px";
  }

  /**
   * Sets the size value of an icon.
   *
   * @param {Element} node The outer node.
   * @param {Element} inner The inner node (<i> element of the icon object).
   * @param {Object} size The size.
   */
  PkSpr.set_icon_size = function(node, inner, size)
  {
    node.style.width = (size.w)+"px";
    node.style.height = (size.h)+"px";
    inner.style.width = (size.w)+"px";
    inner.style.height = (size.h)+"px";
  }

  /**
   * Returns the coordinates and other properties for the icon.
   *
   * @param {Object} attrs The icon's list of attributes.
   * @return {?Object} The icon's coordinates and properties.
   */
  PkSpr.get_icon_data = function(attrs)
  {
    var tree = PkSpr.PKSPR_DATA;
    var branch;

    // The following list contains fallbacks. If a certain form
    // or variation is not found in the coordinates list, it will
    // either fall back to something from this list, or return an error.
    var attr, val, fbval;
    var fallbacks = {
      "type": null,
      "slug": null,
      "form": ".",
      "dir": null,
      "gender": ".",
      "color": "{{$fallback_color}}"
    };
    var props = {
      "flipped": false
    };
    // Attributes that the user requested but couldn't be found in the data.
    var missing = {
    };

    for (attr in fallbacks) {
      if (!fallbacks.hasOwnProperty(attr)) {
        continue;
      }
      // Check if we've reached an end node and quit iterating if so.
      if (tree.x >= 0) {
        break;
      }

      val = attrs[attr];
      fbval = fallbacks[attr];

      // If the value exists in the tree, continue via that branch.
      if (branch = tree[val]) {
        tree = branch;
        continue;
      }
      // If not, continue via the fallback value.
      else
      if (branch = tree[fbval]) {
        missing[attr] = fbval;
        tree = branch;
        // If we're reverting from a non-existent right-facing icon,
        // keep note that this icon should be flipped later.
        if (attr === "dir" && val === "right") {
          props["flipped"] = true;
        }
        continue;
      }
      // If the fallback value doesn't exist, and we're in "dir",
      // continue with "gender" instead. These two share the same node.
      else if (attr === "dir") {
        continue;
      }
      // If the fallback value doesn't exist, and we're in "gender",
      // just skip to the next one. It means we are in a right-facing icon.
      else if (attr === "gender") {
        continue;
      }
      // In all other cases, error out. We don't have the icon.
      else {
        tree = null;
        break;
      }
    }

    // If all went well, we'll have the coordinates and other properties.
    return {
      "coords": tree,
      "props": props,
      "missing": missing
    };
  }

  /**
   * Returns information about the icon type.
   *
   * @param {string} type The type to retrieve information from.
   * @return {?Object} The type's information.
   */
  PkSpr.get_type_size = function(type)
  {
    var spr_type;
    for (spr_type in PkSpr.PKSPR_TYPES) {
      if (!PkSpr.PKSPR_TYPES.hasOwnProperty(spr_type)) {
        continue;
      }
      if (spr_type === type) {
        return PkSpr.PKSPR_TYPES[spr_type];
      }
    }
    return null;
  }

  /**
   * Retrieves icon type information from a node's class.
   *
   * @param {Element} node The node to be scanned.
   * @return {?Object} The node's information.
   */
  PkSpr.get_node_attrs = function(node)
  {
    // The node's class.
    var node_class = node.className;
    if (node_class == null) {
      return null;
    }

    var node_attrs = {
      "type": null,   // e.g. pkmn
      "slug": null,   // e.g. unown
      "color": null,  // regular or shiny
      "form": null,   // e.g. defense, a, exclamation, orange
      "gender": null, // male, female or genderless
      "dir": null     // left or right
    };

    // Aside from these basic variables, we'll also scan for
    // every known icon type. We'll register the type and
    // redirect the values to the appropriate keys.
    var spr_type;
    for (spr_type in PkSpr.PKSPR_TYPES) {
      if (!PkSpr.PKSPR_TYPES.hasOwnProperty(spr_type)) {
        continue;
      }
      // The key goes to "type", the value to "slug".
      // e.g. pkmn-caterpie yields type: pkmn, slug: caterpie.
      node_attrs[spr_type] = {"k": "type", "v": "slug"};
    }

    var a, z;
    var var_idx, var_mapping, var_key, var_val;
    var bit, bits = node_class.split(" ");
    for (a = 0, z = bits.length; a < z; ++a) {
      bit = bits[a];
      // Iterate over all recognized variable types.
      for (var_key in node_attrs) {
        if (!node_attrs.hasOwnProperty(var_key)) {
          continue;
        }
        var_mapping = node_attrs[var_key];
        var_idx = bit.indexOf(var_key+"-");
        if (var_idx === 0) {
          var_val = bit.substring(var_key.length + 1);

          if (var_mapping === null) {
            // Color, form, gender and dir are saved to
            // the node_attrs variable directly.
            node_attrs[var_key] = var_val;
          }
          else {
            node_attrs[var_mapping["k"]] = var_key;
            node_attrs[var_mapping["v"]] = var_val;
          }
        }
      }
    }

    // Check to see if this is a Pokémon icon that uses the number
    // as the identifier rather than the slug.
    if (node_attrs.type === "pkmn"
    &&  PkSpr.is_numeric_pkmn(node_attrs["slug"])) {
      // Replace the index number with the slug.
      node_attrs["slug"] = pkmn_idx_to_slug[node_attrs["slug"]];
    }

    // Clean the output up a bit.
    for (spr_type in PkSpr.PKSPR_TYPES) {
      if (!PkSpr.PKSPR_TYPES.hasOwnProperty(spr_type)) {
        continue;
      }
      delete node_attrs[spr_type];
    }

    return node_attrs;
  }

  /**
   * Compiles a regular expression for use by PkSpr.is_numeric_pkmn().
   */
  PkSpr.prepare_numeric_check = function()
  {
    if (numeric_regexp != undefined) {
      return;
    }
    // 000 is always false.
    numeric_regexp = new RegExp(/(?!000)^[0-9]{3}$/);
  }

  /**
   * Generates a list of Pokédex numbers linked to their respective slugs.
   */
  PkSpr.generate_idx_list = function()
  {
    var a, z, pkmn;

    if (pkmn_idx_to_slug != undefined) {
      return;
    }
    pkmn_idx_to_slug = {};

    // In case we don't have any Pokémon icons in this compile.
    if (PkSpr.PKSPR_DATA == null
    ||  PkSpr.PKSPR_DATA["pkmn"] == null) {
      return;
    }

    pkmn = Object.keys(PkSpr.PKSPR_DATA["pkmn"]);
    for (a = 1, z = pkmn.length; a <= z; ++a) {
      // Fast zero-padding hardcoded to work for 3 digits.
      pkmn_idx_to_slug[("000"+a).slice(-3)] = pkmn[a - 1];
    }
  }

  /**
   * Determines whether a Pokémon identifier is a dex number or not.
   *
   * @param {?string} pkmn The Pokémon identifier (slug or ID).
   * @return {boolean} Whether it is or isn't a numeric identifier.
   */
  PkSpr.is_numeric_pkmn = function(pkmn)
  {
    return numeric_regexp.test(pkmn);
  }

  /**
   * Determines whether something is an array.
   *
   * @param {?} something The object.
   * @return {boolean} Whether the object is an array.
   */
  PkSpr.is_array = function(something)
  {
    return toString.call(something) === "[object Array]";
  }
  
  /**
   * Determines whether something is an associative array.
   *
   * @param {?} something The object.
   * @return {boolean} Whether the object is an associative array, e.g. {}.
   */
  PkSpr.is_object = function(something)
  {
    return Object.prototype.toString.call(something) === "[object Object]";
  }

  /**
   * Decorates all icons found in the parent object.
   *
   * If decorating the entire DOM (document as parent object),
   * this function should be run as a callback from PkSpr.content_loaded().
   *
   * @param {*} caller Calling object (if callback).
   * @param {HTMLDocument|Element} parent Parent object.
   */
  PkSpr.process_container = function(caller, parent)
  {
    if (parent == null) {
      parent = document;
    }

    var a;
    var elements = PkSpr.get_icon_elements(parent);
    for (a = 0; a < elements.length; ++a) {
      PkSpr.decorate_node(elements[a]);
    }
  }

  /**
   * Retrieves all elements in the DOM that can be decorated.
   *
   * @param {HTMLDocument|Element} parent The parent element to search in.
   */
  PkSpr.get_icon_elements = function(parent)
  {
    if (parent == null) {
      parent = document;
    }

    // We'll attempt to use document.querySelectorAll() first.
    // If it's not available, we'll do our own check.
    try {
      return parent.querySelectorAll(
        "span."+PkSpr.PKSPR_BASE_CLASS+","+
        "div."+PkSpr.PKSPR_BASE_CLASS
      );
    }
    catch(e) {}

    // Can't use document.querySelectorAll(), so we'll do this
    // the hard way. Grab all elements of those types and check for the
    // base identifier class.
    var a, b;
    var result, results, elements = [];
    var types = ["span", "div"];
    for (a = 0; a < types.length; ++a) {
      results = parent.getElementsByTagName(types[a]);
      for (b = 0; b < results.length; ++b) {
        result = results[b];
        if (PkSpr.has_class(result, PkSpr.PKSPR_BASE_CLASS)) {
          elements.push(result);
        }
      }
    }
    return elements;
  }

  /**
   * Checks if an DOM element has already been decorated before.
   *
   * @param {Element} element The element to check.
   * @return {boolean} Whether the element has been decorated.
   */
  PkSpr.is_decorated = function(element)
  {
    return PkSpr.has_class(element, PkSpr.PKSPR_BASE_CLASS+"-decorated");
  }

  /**
   * Adds a class to an item that indicates it has been decorated already.
   *
   * @param {Element} element The element to set.
   */
  PkSpr.set_decorated = function(element)
  {
    PkSpr.add_class(element, " "+PkSpr.PKSPR_BASE_CLASS+"-decorated");
  }

  /**
   * Adds a class to a DOM element.
   *
   * @param {Element} element The element to add a class to.
   * @param {string} cls The class name to add.
   */
  PkSpr.add_class = function(element, cls)
  {
    element.className += " "+cls;
  }

  /**
   * Checks if an DOM element has a specific class.
   *
   * @param {Element} element The element to check.
   * @param {string} cls The class name to check for.
   * @return {boolean} Whether the element has the class.
   */
  PkSpr.has_class = function(element, cls)
  {
    return (" "+element.className+" ").indexOf(" "+cls+" ") > -1;
  }

  /**
   * Cross-browser DOMContentLoaded wrapper (version 1.2)
   *
   * Takes a window object and function; the function is executed after
   * DOM is loaded and ready, regardless of the browser used.
   *
   * Written by Diego Perini <diego.perini@gmail.com> and released under
   * the MIT license. Slightly modified for this project. For more
   * information, see <https://github.com/dperini/ContentLoaded>.
   *
   * @param {Window} win Window object.
   * @param {function(...)} fn Function to execute.
   */
  PkSpr.content_loaded = function(win, fn)
  {
    var done = false, top = true,

    doc = win.document, root = doc.documentElement,

    add = doc.addEventListener ? "addEventListener" : "attachEvent",
    rem = doc.addEventListener ? "removeEventListener" : "detachEvent",
    pre = doc.addEventListener ? "" : "on",

    init = function(e)
    {
      if (e.type === "readystatechange" && doc.readyState !== "complete") {
        return;
      }
      (e.type === "load" ? win : doc)[rem](pre + e.type, init, false);
      if (!done && (done = true)) {
        fn.call(win, e.type || e);
      }
    },

    poll = function()
    {
      try {
        root.doScroll("left");
      }
      catch(e) {
        setTimeout(poll, 50); return;
      }
      init("poll");
    };

    if (doc.readyState == "complete") {
      fn.call(win, "lazy");
    }
    else {
      if (doc.createEventObject && root.doScroll) {
        try {
          top = !win.frameElement;
        }
        catch(e) {
        }
        if (top) {
          poll();
        }
      }
      doc[add](pre+"DOMContentLoaded", init, false);
      doc[add](pre+"readystatechange", init, false);
      win[add](pre+"load", init, false);
    }
  }

  /**
   * Runs a couple of initialization functions.
   */
  PkSpr.initialize = function()
  {
    // Compile our numeric check regular expression.
    PkSpr.prepare_numeric_check();
    // Generate a list of slugs by Pokédex number.
    PkSpr.generate_idx_list();
  }();

  if (typeof define === "function" && define.amd) {
    define("pokesprite", [], function() {
      return PkSpr;
    });
  }

  if (!noGlobal) {
    window["PkSpr"] = PkSpr;
  }

  return PkSpr;
}));
