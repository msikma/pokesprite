/** @preserve
 * {{$title_str_site}}
 * {{$copyright_str}}
 * {{$copyright_gf}}
 * {{$copyright_contrib_notice}}
 * {{$generated_on}}
 *
// ==ClosureCompiler==
// @output_file_name {{$js_output_min}}
// @compilation_level ADVANCED_OPTIMIZATIONS
// ==/ClosureCompiler==
 */
;(function(){

/**
 * PokéSprite main code and icon processor.
 *
 * This class has been generated, so editing it directly is not recommended.
 *
 * @static
 */
window["PkSpr"] = (function()
{
    var self = this;
    
    /**
     * Base CSS class that identifies an element as ours.
     *
     * @const
     * @type {!string}
     */
    self.PKSPR_BASE_CLASS = "{{$css_base_selector}}";
    
    /**
     * List of types and their sizes.
     *
     * @const
     * @type {!Object}
     */
    self.PKSPR_TYPES = {{$sizes_json}};
    
    /**
     * Coordinate and size data for every single icon. Size data is
     * not included if the type's size can already be found
     * in the PKSPR_TYPES constant.
     *
     * @const
     * @type {!Object}
     */
    self.PKSPR_DATA = {{$coords_json}};
    
    /**
     * Schedules the DOM to be processed completely as soon as it's ready.
     */ 
    self["process_dom"] = function()
    {
        self.content_loaded(window, self.process_container);
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
     */
    self["decorate"] = function(val)
    {
        // Determine what the user passed.
        var is_arr = self.is_array(val);
        // If it's not an array, turn it into one so we can iterate over it.
        if (is_arr == false) {
            val = [val];
        }
        
        var a, z;
        var obj, node, is_str, is_node;
        for (a = 0, z = val.length; a < z; ++a) {
            obj = val[a];
            is_str = typeof obj == 'string' || obj instanceof String;
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
            if (self.has_class(node, self.PKSPR_BASE_CLASS)) {
                // It's an icon.
                self.decorate_node(node);
            }
            else {
                // It's a parent object.
                self.process_container(null, node);
            }
        }
    }
    
    /**
     * Puts a message in the console in case of decoration failure.
     *
     * @param {Object} attrs The icon attributes.
     */
    self.decoration_error = function(attrs)
    {
        window.console && console.warn("Couldn't decorate icon with the "+
            "following properties: %o", attrs);
    }
    
    /**
     * Decorates a single node
     *
     * @param {Element} node The node to be decorated.
     * @return {boolean} Whether decoration was successful.
     */
    self.decorate_node = function(node)
    {
        // Check to make sure it hasn't been decorated before.
        if (self.is_decorated(node)) {
            return false;
        }
        
        // Get the node's base attributes.
        var attrs = self.get_node_attrs(node);
        var size = self.get_type_size(attrs.type);
        var data = self.get_icon_data(attrs);
        var coords = data.coords;
        var props = data.props;
        
        // If we were not able to gauge its size from the type,
        // that means this icon's size is stored alongside
        // the coordinate data.
        var custom_size = size == null || size.x == null;
        
        // Check whether this node's icon really exists.
        if (coords == null) {
            // If not, error out.
            self.decoration_error(attrs);
            return false;
        }
        if (custom_size) {
            size = {"w": coords.w, "h": coords.h};
        }
        
        // Create the inner element that is the icon itself.
        var inner = self.create_inner_node(node);
        // Set background coordinates.
        self.set_icon_coords(inner, coords);
        // Set the size, if we're dealing with a custom sized icon.
        if (custom_size) {
            self.set_icon_size(node, inner, size);
        }
        // Flip the icon if we're showing a faux right-facing icon.
        if (props.flipped) {
            self.set_icon_direction(node, "right");
        }
        
        // Indicate that this node has been decorated so we don't
        // accidentally decorate it twice.
        self.set_decorated(node);
        
        return true;
    }
    
    self.set_icon_direction = function(node, dir)
    {
        self.add_class(node, '{{$var_base_name}}-faux-right');
    }
    
    /**
     * Creates the inner node, which is an extra child element inside the
     * icon node that contains the actual icon itself.
     *
     * @param {Element} node The icon node.
     * @return {Element} The newly created inner node.
     */
    self.create_inner_node = function(node)
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
    self.set_icon_coords = function(inner, coords)
    {
        inner.style.backgroundPosition = (-coords.x)+"px "+(-coords.y)+"px";
    }
    
    /**
     * Sets the size value of an icon.
     *
     * @param {Element} node The outer node.
     * @param {Element} inner The inner node (<i> element of the icon object).
     * @param {Object} size The size.
     */
    self.set_icon_size = function(node, inner, size)
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
    self.get_icon_data = function(attrs)
    {
        var tree = self.PKSPR_DATA;
        var branch;
        
        // The following list contains fallbacks. If a certain form
        // or variation is not found in the coordinates list, it will
        // either fall back to something from this list, or return an error.
        var attr, val, fbval;
        var fallbacks = {
            "type": null,
            "slug": null,
            "form": ".",
            "dir": ".",
            "color": "{{$fallback_color}}"
        };
        var props = {
            "flipped": false
        };
        
        for (attr in fallbacks) {
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
                tree = branch;
                // If we're reverting from a non-existent right-facing icon,
                // keep note that this icon should be flipped later.
                if (val == "right") {
                    props.flipped = true;
                }
                continue;
            }
            // If the fallback value doesn't exist, error out.
            else {
                tree = null;
                break;
            }
        }
        
        // If all went well, we'll have the coordinates and other properties.
        return {
            coords: tree,
            props: props
        };
    }
    
    /**
     * Returns information about the icon type.
     *
     * @param {string} type The type to retrieve information from.
     * @return {?Object} The type's information.
     */
    self.get_type_size = function(type)
    {
        var spr_type;
        for (spr_type in self.PKSPR_TYPES) {
            if (spr_type == type) {
                return self.PKSPR_TYPES[spr_type];
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
    self.get_node_attrs = function(node)
    {
        // The node's class.
        var node_class = node.className;
        if (node_class == null) {
            return null;
        }
        
        var node_attrs = {
            "type": null,     // e.g. pkmn
            "slug": null,     // e.g. unown
            "color": null,    // regular or shiny
            "form": null,     // e.g. defense, a, exclamation, orange
            "gender": null,   // male, female or genderless
            "dir": null       // left or right
        };
        
        // Aside from these basic variables, we'll also scan for
        // every known icon type. We'll register the type and
        // redirect the values to the appropriate keys.
        var spr_type;
        for (spr_type in self.PKSPR_TYPES) {
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
                var_mapping = node_attrs[var_key];
                var_idx = bit.indexOf(var_key+"-");
                if (var_idx == 0) {
                    var_val = bit.substring(var_key.length + 1);
                    
                    if (var_mapping === null) {
                        // Color, form, gender and dir are saved to
                        // the node_attrs variable directly.
                        node_attrs[var_key] = var_val;
                    }
                    else {
                        node_attrs[var_mapping.k] = var_key;
                        node_attrs[var_mapping.v] = var_val;
                    }
                }
            }
        }
        
        // Clean the output up a bit.
        for (spr_type in self.PKSPR_TYPES) {
            delete node_attrs[spr_type];
        }
        
        return node_attrs;
    }
    
    /**
     * Determines whether something is an array.
     *
     * @param {?} something The object.
     * @return {boolean} Whether the object is an array.
     */
    self.is_array = function(something)
    {
        return toString.call(something) === "[object Array]";
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
    self.process_container = function(caller, parent)
    {
        if (parent == null) {
            parent = document;
        }
        
        var a;
        var elements = self.get_icon_elements(parent);
        for (a = 0; a < elements.length; ++a) {
            self.decorate_node(elements[a]);
        }
    }
    
    /**
     * Retrieves all elements in the DOM that can be decorated.
     *
     * @param {HTMLDocument|Element} parent The parent element to search in.
     */
    self.get_icon_elements = function(parent)
    {
        if (parent == null) {
            parent = document;
        }
        
        // We'll attempt to use document.querySelectorAll() first.
        // If it's not available, we'll do our own check.
        try {
            return parent.querySelectorAll(
                "span."+self.PKSPR_BASE_CLASS+","+
                "div."+self.PKSPR_BASE_CLASS
            );
        }
        catch(e) {}
        
        // Can't use querySelectorAll(), so we'll do this the hard way.
        // Grab all elements of those types and check for the
        // base identifier class.
        var a, b;
        var result, results, elements = [];
        var types = ["span", "div"];
        for (a = 0; a < types.length; ++a) {
            results = parent.getElementsByTagName(types[a]);
            for (b = 0; b < results.length; ++b) {
                result = results[b];
                if (self.has_class(result, self.PKSPR_BASE_CLASS)) {
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
    self.is_decorated = function(element)
    {
        return self.has_class(element, self.PKSPR_BASE_CLASS+"-decorated");
    }
    
    /**
     * Adds a class to an item that indicates it has been decorated already.
     *
     * @param {Element} element The element to set.
     */
    self.set_decorated = function(element)
    {
        self.add_class(element, " "+self.PKSPR_BASE_CLASS+"-decorated");
    }
    
    /**
     * Adds a class to a DOM element.
     *
     * @param {Element} element The element to add a class to.
     * @param {string} cls The class name to add.
     */
    self.add_class = function(element, cls)
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
    self.has_class = function(element, cls)
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
    self.content_loaded = function(win, fn)
    {
        var done = false, top = true,
    
        doc = win.document, root = doc.documentElement,
    
        add = doc.addEventListener ? "addEventListener" : "attachEvent",
        rem = doc.addEventListener ? "removeEventListener" : "detachEvent",
        pre = doc.addEventListener ? "" : "on",
    
        init = function(e)
        {
            if (e.type == "readystatechange" && doc.readyState != "complete") {
                return;
            }
            (e.type == "load" ? win : doc)[rem](pre + e.type, init, false);
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
    
    return self;
})();

/* All done. */
})();