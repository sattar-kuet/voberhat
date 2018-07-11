(function () {
    
    'use strict';
    /** 
     * Class for managing events.
     * Can be extended to provide event functionality in other classes.
     *
     * @class EventEmitter Manages event registering and emitting.
     */
    function EventEmitter() {}

    // Shortcuts to improve speed and size
    var proto = EventEmitter.prototype;
    var exports = this;
    var originalGlobalValue = exports.EventEmitter;

    /**
     * Finds the index of the listener for the event in it's storage array.
     *
     * @param {Function[]} listeners Array of listeners to search through.
     * @param {Function} listener Method to look for.
     * @return {Number} Index of the specified listener, -1 if not found
     * @api private
     */
    function indexOfListener(listeners, listener) {
        var i = listeners.length;
        while (i--) {
            if (listeners[i].listener === listener) {
                return i;
            }
        }

        return -1;
    }

    /**
     * Alias a method while keeping the context correct, to allow for overwriting of target method.
     *
     * @param {String} name The name of the target method.
     * @return {Function} The aliased method
     * @api private
     */
    function alias(name) {
        return function aliasClosure() {
            return this[name].apply(this, arguments);
        };
    }

    /**
     * Returns the listener array for the specified event.
     * Will initialise the event object and listener arrays if required.
     * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
     * Each property in the object response is an array of listener functions.
     *
     * @param {String|RegExp} evt Name of the event to return the listeners from.
     * @return {Function[]|Object} All listener functions for the event.
     */
    proto.getListeners = function getListeners(evt) {
        var events = this._getEvents();
        var response;
        var key;

        // Return a concatenated array of all matching events if
        // the selector is a regular expression.
        if (typeof evt === 'object') {
            response = {};
            for (key in events) {
                if (events.hasOwnProperty(key) && evt.test(key)) {
                    response[key] = events[key];
                }
            }
        }
        else {
            response = events[evt] || (events[evt] = []);
        }

        return response;
    };

    /**
     * Takes a list of listener objects and flattens it into a list of listener functions.
     *
     * @param {Object[]} listeners Raw listener objects.
     * @return {Function[]} Just the listener functions.
     */
    proto.flattenListeners = function flattenListeners(listeners) {
        var flatListeners = [];
        var i;

        for (i = 0; i < listeners.length; i += 1) {
            flatListeners.push(listeners[i].listener);
        }

        return flatListeners;
    };

    /**
     * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
     *
     * @param {String|RegExp} evt Name of the event to return the listeners from.
     * @return {Object} All listener functions for an event in an object.
     */
    proto.getListenersAsObject = function getListenersAsObject(evt) {
        var listeners = this.getListeners(evt);
        var response;

        if (listeners instanceof Array) {
            response = {};
            response[evt] = listeners;
        }

        return response || listeners;
    };

    /**
     * Adds a listener function to the specified event.
     * The listener will not be added if it is a duplicate.
     * If the listener returns true then it will be removed after it is called.
     * If you pass a regular expression as the event name then the listener will be added to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to attach the listener to.
     * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addListener = function addListener(evt, listener) {
        var listeners = this.getListenersAsObject(evt);
        var listenerIsWrapped = typeof listener === 'object';
        var key;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
                listeners[key].push(listenerIsWrapped ? listener : {
                    listener: listener,
                    once: false
                });
            }
        }

        return this;
    };

    /**
     * Alias of addListener
     */
    proto.on = alias('addListener');

    /**
     * Semi-alias of addListener. It will add a listener that will be
     * automatically removed after it's first execution.
     *
     * @param {String|RegExp} evt Name of the event to attach the listener to.
     * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addOnceListener = function addOnceListener(evt, listener) {
        return this.addListener(evt, {
            listener: listener,
            once: true
        });
    };

    /**
     * Alias of addOnceListener.
     */
    proto.once = alias('addOnceListener');

    /**
     * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
     * You need to tell it what event names should be matched by a regex.
     *
     * @param {String} evt Name of the event to create.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.defineEvent = function defineEvent(evt) {
        this.getListeners(evt);
        return this;
    };

    /**
     * Uses defineEvent to define multiple events.
     *
     * @param {String[]} evts An array of event names to define.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.defineEvents = function defineEvents(evts) {
        for (var i = 0; i < evts.length; i += 1) {
            this.defineEvent(evts[i]);
        }
        return this;
    };

    /**
     * Removes a listener function from the specified event.
     * When passed a regular expression as the event name, it will remove the listener from all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to remove the listener from.
     * @param {Function} listener Method to remove from the event.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeListener = function removeListener(evt, listener) {
        var listeners = this.getListenersAsObject(evt);
        var index;
        var key;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key)) {
                index = indexOfListener(listeners[key], listener);

                if (index !== -1) {
                    listeners[key].splice(index, 1);
                }
            }
        }

        return this;
    };

    /**
     * Alias of removeListener
     */
    proto.off = alias('removeListener');

    /**
     * Adds listeners in bulk using the manipulateListeners method.
     * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
     * You can also pass it a regular expression to add the array of listeners to all events that match it.
     * Yeah, this function does quite a bit. That's probably a bad thing.
     *
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to add.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.addListeners = function addListeners(evt, listeners) {
        // Pass through to manipulateListeners
        return this.manipulateListeners(false, evt, listeners);
    };

    /**
     * Removes listeners in bulk using the manipulateListeners method.
     * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
     * You can also pass it an event name and an array of listeners to be removed.
     * You can also pass it a regular expression to remove the listeners from all events that match it.
     *
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to remove.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeListeners = function removeListeners(evt, listeners) {
        // Pass through to manipulateListeners
        return this.manipulateListeners(true, evt, listeners);
    };

    /**
     * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
     * The first argument will determine if the listeners are removed (true) or added (false).
     * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
     * You can also pass it an event name and an array of listeners to be added/removed.
     * You can also pass it a regular expression to manipulate the listeners of all events that match it.
     *
     * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
     * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
     * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
        var i;
        var value;
        var single = remove ? this.removeListener : this.addListener;
        var multiple = remove ? this.removeListeners : this.addListeners;

        // If evt is an object then pass each of it's properties to this method
        if (typeof evt === 'object' && !(evt instanceof RegExp)) {
            for (i in evt) {
                if (evt.hasOwnProperty(i) && (value = evt[i])) {
                    // Pass the single listener straight through to the singular method
                    if (typeof value === 'function') {
                        single.call(this, i, value);
                    }
                    else {
                        // Otherwise pass back to the multiple function
                        multiple.call(this, i, value);
                    }
                }
            }
        }
        else {
            // So evt must be a string
            // And listeners must be an array of listeners
            // Loop over it and pass each one to the multiple method
            i = listeners.length;
            while (i--) {
                single.call(this, evt, listeners[i]);
            }
        }

        return this;
    };

    /**
     * Removes all listeners from a specified event.
     * If you do not specify an event then all listeners will be removed.
     * That means every event will be emptied.
     * You can also pass a regex to remove all events that match it.
     *
     * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.removeEvent = function removeEvent(evt) {
        var type = typeof evt;
        var events = this._getEvents();
        var key;

        // Remove different things depending on the state of evt
        if (type === 'string') {
            // Remove all listeners for the specified event
            delete events[evt];
        }
        else if (type === 'object') {
            // Remove all events matching the regex.
            for (key in events) {
                if (events.hasOwnProperty(key) && evt.test(key)) {
                    delete events[key];
                }
            }
        }
        else {
            // Remove all listeners in all events
            delete this._events;
        }

        return this;
    };

    /**
     * Alias of removeEvent.
     *
     * Added to mirror the node API.
     */
    proto.removeAllListeners = alias('removeEvent');

    /**
     * Emits an event of your choice.
     * When emitted, every listener attached to that event will be executed.
     * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
     * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
     * So they will not arrive within the array on the other side, they will be separate.
     * You can also pass a regular expression to emit to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
     * @param {Array} [args] Optional array of arguments to be passed to each listener.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.emitEvent = function emitEvent(evt, args) {
        var listeners = this.getListenersAsObject(evt);
        var listener;
        var i;
        var key;
        var response;

        for (key in listeners) {
            if (listeners.hasOwnProperty(key)) {
                i = listeners[key].length;

                while (i--) {
                    // If the listener returns true then it shall be removed from the event
                    // The function is executed either with a basic call or an apply if there is an args array
                    listener = listeners[key][i];

                    if (listener.once === true) {
                        this.removeListener(evt, listener.listener);
                    }

                    response = listener.listener.apply(this, args || []);

                    if (response === this._getOnceReturnValue()) {
                        this.removeListener(evt, listener.listener);
                    }
                }
            }
        }

        return this;
    };

    /**
     * Alias of emitEvent
     */
    proto.trigger = alias('emitEvent');

    /**
     * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
     * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
     *
     * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
     * @param {...*} Optional additional arguments to be passed to each listener.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.emit = function emit(evt) {
        var args = Array.prototype.slice.call(arguments, 1);
        return this.emitEvent(evt, args);
    };

    /**
     * Sets the current value to check against when executing listeners. If a
     * listeners return value matches the one set here then it will be removed
     * after execution. This value defaults to true.
     *
     * @param {*} value The new value to check for when executing listeners.
     * @return {Object} Current instance of EventEmitter for chaining.
     */
    proto.setOnceReturnValue = function setOnceReturnValue(value) {
        this._onceReturnValue = value;
        return this;
    };

    /**
     * Fetches the current value to check against when executing listeners. If
     * the listeners return value matches this one then it should be removed
     * automatically. It will return true by default.
     *
     * @return {*|Boolean} The current value to check for or the default, true.
     * @api private
     */
    proto._getOnceReturnValue = function _getOnceReturnValue() {
        if (this.hasOwnProperty('_onceReturnValue')) {
            return this._onceReturnValue;
        }
        else {
            return true;
        }
    };

    /**
     * Fetches the events object and creates one if required.
     *
     * @return {Object} The events storage object.
     * @api private
     */
    proto._getEvents = function _getEvents() {
        return this._events || (this._events = {});
    };

    /**
     * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
     *
     * @return {Function} Non conflicting EventEmitter class.
     */
    EventEmitter.noConflict = function noConflict() {
        exports.EventEmitter = originalGlobalValue;
        return EventEmitter;
    };

    // Expose the class either via AMD, CommonJS or the global object
    if (typeof define === 'function' && define.amd) {
        define('eventEmitter/EventEmitter',[],function () {
            return EventEmitter;
        });
    }
    else if (typeof module === 'object' && module.exports){
        module.exports = EventEmitter;
    }
    else {
        this.EventEmitter = EventEmitter;
    }
}.call(this));

/*!
 * eventie v1.0.4
 * event binding helper
 *   eventie.bind( elem, 'click', myFn )
 *   eventie.unbind( elem, 'click', myFn )
 */

/*jshint browser: true, undef: true, unused: true */
/*global define: false */

( function( window ) {



var docElem = document.documentElement;

var bind = function() {};

function getIEEvent( obj ) {
  var event = window.event;
  // add event.target
  event.target = event.target || event.srcElement || obj;
  return event;
}

if ( docElem.addEventListener ) {
  bind = function( obj, type, fn ) {
    obj.addEventListener( type, fn, false );
  };
} else if ( docElem.attachEvent ) {
  bind = function( obj, type, fn ) {
    obj[ type + fn ] = fn.handleEvent ?
      function() {
        var event = getIEEvent( obj );
        fn.handleEvent.call( fn, event );
      } :
      function() {
        var event = getIEEvent( obj );
        fn.call( obj, event );
      };
    obj.attachEvent( "on" + type, obj[ type + fn ] );
  };
}

var unbind = function() {};

if ( docElem.removeEventListener ) {
  unbind = function( obj, type, fn ) {
    obj.removeEventListener( type, fn, false );
  };
} else if ( docElem.detachEvent ) {
  unbind = function( obj, type, fn ) {
    obj.detachEvent( "on" + type, obj[ type + fn ] );
    try {
      delete obj[ type + fn ];
    } catch ( err ) {
      // can't delete window object properties
      obj[ type + fn ] = undefined;
    }
  };
}

var eventie = {
  bind: bind,
  unbind: unbind
};

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( 'eventie/eventie',eventie );
} else {
  // browser global
  window.eventie = eventie;
}

})( this );

/*!
 * imagesLoaded v3.1.8
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

( function( window, factory ) { 
  // universal module definition

  /*global define: false, module: false, require: false */

  if ( typeof define === 'function' && define.amd ) {
    // AMD
    define( [
      'eventEmitter/EventEmitter',
      'eventie/eventie'
    ], function( EventEmitter, eventie ) {
      return factory( window, EventEmitter, eventie );
    });
  } else if ( typeof exports === 'object' ) {
    // CommonJS
    module.exports = factory(
      window,
      require('wolfy87-eventemitter'),
      require('eventie')
    );
  } else {
    // browser global
    window.imagesLoaded = factory(
      window,
      window.EventEmitter,
      window.eventie
    );
  }

})( window,

// --------------------------  factory -------------------------- //

function factory( window, EventEmitter, eventie ) {



var $ = window.jQuery;
var console = window.console;
var hasConsole = typeof console !== 'undefined';

// -------------------------- helpers -------------------------- //

// extend objects
function extend( a, b ) {
  for ( var prop in b ) {
    a[ prop ] = b[ prop ];
  }
  return a;
}

var objToString = Object.prototype.toString;
function isArray( obj ) {
  return objToString.call( obj ) === '[object Array]';
}

// turn element or nodeList into an array
function makeArray( obj ) {
  var ary = [];
  if ( isArray( obj ) ) {
    // use object if already an array
    ary = obj;
  } else if ( typeof obj.length === 'number' ) {
    // convert nodeList to array
    for ( var i=0, len = obj.length; i < len; i++ ) {
      ary.push( obj[i] );
    }
  } else {
    // array of single index
    ary.push( obj );
  }
  return ary;
}

  // -------------------------- imagesLoaded -------------------------- //

  /**
   * @param {Array, Element, NodeList, String} elem
   * @param {Object or Function} options - if function, use as callback
   * @param {Function} onAlways - callback function
   */
  function ImagesLoaded( elem, options, onAlways ) {
    // coerce ImagesLoaded() without new, to be new ImagesLoaded()
    if ( !( this instanceof ImagesLoaded ) ) {
      return new ImagesLoaded( elem, options );
    }
    // use elem as selector string
    if ( typeof elem === 'string' ) {
      elem = document.querySelectorAll( elem );
    }

    this.elements = makeArray( elem );
    this.options = extend( {}, this.options );

    if ( typeof options === 'function' ) {
      onAlways = options;
    } else {
      extend( this.options, options );
    }

    if ( onAlways ) {
      this.on( 'always', onAlways );
    }

    this.getImages();

    if ( $ ) {
      // add jQuery Deferred object
      this.jqDeferred = new $.Deferred();
    }

    // HACK check async to allow time to bind listeners
    var _this = this;
    setTimeout( function() {
      _this.check();
    });
  }

  ImagesLoaded.prototype = new EventEmitter();

  ImagesLoaded.prototype.options = {};

  ImagesLoaded.prototype.getImages = function() {
    this.images = [];

    // filter & find items if we have an item selector
    for ( var i=0, len = this.elements.length; i < len; i++ ) {
      var elem = this.elements[i];
      // filter siblings
      if ( elem.nodeName === 'IMG' ) {
        this.addImage( elem );
      }
      // find children
      // no non-element nodes, #143
      var nodeType = elem.nodeType;
      if ( !nodeType || !( nodeType === 1 || nodeType === 9 || nodeType === 11 ) ) {
        continue;
      }
      var childElems = elem.querySelectorAll('img');
      // concat childElems to filterFound array
      for ( var j=0, jLen = childElems.length; j < jLen; j++ ) {
        var img = childElems[j];
        this.addImage( img );
      }
    }
  };

  /**
   * @param {Image} img
   */
  ImagesLoaded.prototype.addImage = function( img ) {
    var loadingImage = new LoadingImage( img );
    this.images.push( loadingImage );
  };

  ImagesLoaded.prototype.check = function() {
    var _this = this;
    var checkedCount = 0;
    var length = this.images.length;
    this.hasAnyBroken = false;
    // complete if no images
    if ( !length ) {
      this.complete();
      return;
    }

    function onConfirm( image, message ) {
      if ( _this.options.debug && hasConsole ) {
        console.log( 'confirm', image, message );
      }

      _this.progress( image );
      checkedCount++;
      if ( checkedCount === length ) {
        _this.complete();
      }
      return true; // bind once
    }

    for ( var i=0; i < length; i++ ) {
      var loadingImage = this.images[i];
      loadingImage.on( 'confirm', onConfirm );
      loadingImage.check();
    }
  };

  ImagesLoaded.prototype.progress = function( image ) {
    this.hasAnyBroken = this.hasAnyBroken || !image.isLoaded;
    // HACK - Chrome triggers event before object properties have changed. #83
    var _this = this;
    setTimeout( function() {
      _this.emit( 'progress', _this, image );
      if ( _this.jqDeferred && _this.jqDeferred.notify ) {
        _this.jqDeferred.notify( _this, image );
      }
    });
  };

  ImagesLoaded.prototype.complete = function() {
    var eventName = this.hasAnyBroken ? 'fail' : 'done';
    this.isComplete = true;
    var _this = this;
    // HACK - another setTimeout so that confirm happens after progress
    setTimeout( function() {
      _this.emit( eventName, _this );
      _this.emit( 'always', _this );
      if ( _this.jqDeferred ) {
        var jqMethod = _this.hasAnyBroken ? 'reject' : 'resolve';
        _this.jqDeferred[ jqMethod ]( _this );
      }
    });
  };

  // -------------------------- jquery -------------------------- //

  if ( $ ) {
    $.fn.imagesLoaded = function( options, callback ) {
      var instance = new ImagesLoaded( this, options, callback );
      return instance.jqDeferred.promise( $(this) );
    };
  }


  // --------------------------  -------------------------- //

  function LoadingImage( img ) {
    this.img = img;
  }

  LoadingImage.prototype = new EventEmitter();

  LoadingImage.prototype.check = function() {
    // first check cached any previous images that have same src
    var resource = cache[ this.img.src ] || new Resource( this.img.src );
    if ( resource.isConfirmed ) {
      this.confirm( resource.isLoaded, 'cached was confirmed' );
      return;
    }

    // If complete is true and browser supports natural sizes,
    // try to check for image status manually.
    if ( this.img.complete && this.img.naturalWidth !== undefined ) {
      // report based on naturalWidth
      this.confirm( this.img.naturalWidth !== 0, 'naturalWidth' );
      return;
    }

    // If none of the checks above matched, simulate loading on detached element.
    var _this = this;
    resource.on( 'confirm', function( resrc, message ) {
      _this.confirm( resrc.isLoaded, message );
      return true;
    });

    resource.check();
  };

  LoadingImage.prototype.confirm = function( isLoaded, message ) {
    this.isLoaded = isLoaded;
    this.emit( 'confirm', this, message );
  };

  // -------------------------- Resource -------------------------- //

  // Resource checks each src, only once
  // separate class from LoadingImage to prevent memory leaks. See #115

  var cache = {};

  function Resource( src ) {
    this.src = src;
    // add to cache
    cache[ src ] = this;
  }

  Resource.prototype = new EventEmitter();

  Resource.prototype.check = function() {
    // only trigger checking once
    if ( this.isChecked ) {
      return;
    }
    // simulate loading on detached element
    var proxyImage = new Image();
    eventie.bind( proxyImage, 'load', this );
    eventie.bind( proxyImage, 'error', this );
    proxyImage.src = this.src;
    // set flag
    this.isChecked = true;
  };

  // ----- events ----- //

  // trigger specified handler for event type
  Resource.prototype.handleEvent = function( event ) {
    var method = 'on' + event.type;
    if ( this[ method ] ) {
      this[ method ]( event );
    }
  };

  Resource.prototype.onload = function( event ) {
    this.confirm( true, 'onload' );
    this.unbindProxyEvents( event );
  };

  Resource.prototype.onerror = function( event ) {
    this.confirm( false, 'onerror' );
    this.unbindProxyEvents( event );
  };

  // ----- confirm ----- //

  Resource.prototype.confirm = function( isLoaded, message ) {
    this.isConfirmed = true;
    this.isLoaded = isLoaded;
    this.emit( 'confirm', this, message );
  };

  Resource.prototype.unbindProxyEvents = function( event ) {
    eventie.unbind( event.target, 'load', this );
    eventie.unbind( event.target, 'error', this );
  };

  // -----  ----- //

  return ImagesLoaded;

});

/**
 * @preserve
 * Project: Bootstrap Hover Dropdown
 * Author: Cameron Spear
 * Version: v2.1.3
 * Contributors: Mattia Larentis
 * Dependencies: Bootstrap's Dropdown plugin, jQuery
 * Description: A simple plugin to enable Bootstrap dropdowns to active on hover and provide a nice user experience.
 * License: MIT
 * Homepage: http://cameronspear.com/blog/bootstrap-dropdown-on-hover-plugin/
 */
;(function ($, window, undefined) {
    // outside the scope of the jQuery plugin to
    // keep track of all dropdowns
    var $allDropdowns = $();

    // if instantlyCloseOthers is true, then it will instantly
    // shut other nav items when a new one is hovered over
    $.fn.dropdownHover = function (options) {
        // don't do anything if touch is supported
        // (plugin causes some issues on mobile)
        if('ontouchstart' in document) return this; // don't want to affect chaining

        // the element we really care about
        // is the dropdown-toggle's parent
        $allDropdowns = $allDropdowns.add(this.parent());

        return this.each(function () {
            var $this = $(this),
                $parent = $this.parent(),
                defaults = {
                    delay: 500,
                    hoverDelay: 0,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    hoverDelay: $(this).data('hover-delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                showEvent   = 'show.bs.dropdown',
                hideEvent   = 'hide.bs.dropdown',
                // shownEvent  = 'shown.bs.dropdown',
                // hiddenEvent = 'hidden.bs.dropdown',
                settings = $.extend(true, {}, defaults, options, data),
                timeout, timeoutHover;

            $parent.hover(function (event) {
                // so a neighbor can't open the dropdown
                if(!$parent.hasClass('open') && !$this.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    return true;
                }

                openDropdown(event);
            }, function () {
                // clear timer for hover event
                window.clearTimeout(timeoutHover)
                timeout = window.setTimeout(function () {
                    $this.attr('aria-expanded', 'false');
                    $parent.removeClass('open');
                    $this.trigger(hideEvent);
                }, settings.delay);
            });

            // this helps with button groups!
            $this.hover(function (event) {
                // this helps prevent a double event from firing.
                // see https://github.com/CWSpear/bootstrap-hover-dropdown/issues/55
                if(!$parent.hasClass('open') && !$parent.is(event.target)) {
                    // stop this event, stop executing any code
                    // in this callback but continue to propagate
                    return true;
                }

                openDropdown(event);
            });

            // handle submenus
            $parent.find('.dropdown-submenu').each(function (){
                var $this = $(this);
                var subTimeout;
                $this.hover(function () {
                    window.clearTimeout(subTimeout);
                    $this.children('.dropdown-menu').show();
                    // always close submenu siblings instantly
                    $this.siblings().children('.dropdown-menu').hide();
                }, function () {
                    var $submenu = $this.children('.dropdown-menu');
                    subTimeout = window.setTimeout(function () {
                        $submenu.hide();
                    }, settings.delay);
                });
            });

            function openDropdown(event) {
                // clear dropdown timeout here so it doesnt close before it should
                window.clearTimeout(timeout);
                // restart hover timer
                window.clearTimeout(timeoutHover);
                
                // delay for hover event.  
                timeoutHover = window.setTimeout(function () {
                    $allDropdowns.find(':focus').blur();

                    if(settings.instantlyCloseOthers === true)
                        $allDropdowns.removeClass('open');
                    
                    // clear timer for hover event
                    window.clearTimeout(timeoutHover);
                    $this.attr('aria-expanded', 'true');
                    $this.trigger(showEvent);
                }, settings.hoverDelay);
            }
        });
    };

    $(document).ready(function () {
        // apply dropdownHover to all elements with the data-hover="dropdown" attribute
        $('[data-hover="dropdown"]').dropdownHover();
          //  Fix First Click Menu /

    });
    $(document.body).on('click', '.nav [data-toggle="dropdown"]' ,function(){
        if(  this.href && this.href != '#'){
            window.location.href = this.href;
        }
    });

    $(document.body).on('click', '.treeview [data-toggle="dropdown"]' ,function(){
        if(  this.href && this.href != '#'){
            window.location.href = this.href;
        }
    });
    
})(jQuery, window);




(function ($) {
     
    $("[data-progress-animation]").each(function() {
        var $this = $(this);
        $this.appear(function() {
            var delay = ($this.attr("data-appear-animation-delay") ? $this.attr("data-appear-animation-delay") : 1);
            if(delay > 1) $this.css("animation-delay", delay + "ms");
            setTimeout(function() { $this.animate({width: $this.attr("data-progress-animation")}, 800);}, delay);
        }, {accX: 0, accY: -50});
      });

    $.fn.wrapStart = function(numWords){
        return this.each(function(){
            var $this = $(this);
            var node = $this.contents().filter(function(){
                return this.nodeType == 3;
            }).first(),
            text = node.text().trim(),
            first = text.split(' ', 1).join(" ");
            if (!node.length) return;
            node[0].nodeValue = text.slice(first.length);
            node.before('<b>' + first + '</b>');
        });
    };
    
    jQuery(document).ready(function() {

        $('.mod-heading .widget-title > span').wrapStart(1);
        
        $with = $(window).width();

        $(".owl-carousel[data-carousel=owl]").each( function(){
            var config = {
                loop: false,
                nav: $(this).data( 'nav' ),
                dots: $(this).data( 'pagination' ),
                items: 4,
                navText: ['<span class="icofont icofont-simple-left"></span>', '<span class="icofont icofont-simple-right"></span>']
            };
        
            var owl = $(this);
            if( $(this).data('items') ){
                config.items = $(this).data( 'items' );
                var desktop_full = $(this).data( 'items' );
            }

            if ($(this).data('large')) {
                var desktop = $(this).data('large');
            } else {
                var desktop = config.items;
            }
            if ($(this).data('medium')) {
                var medium = $(this).data('medium');
            } else {
                var medium = config.items;
            }
            if ($(this).data('smallmedium')) {
                var smallmedium = $(this).data('smallmedium');
            } else {
                var smallmedium = config.items;
            }
            if ($(this).data('extrasmall')) {
                var extrasmall = $(this).data('extrasmall');
            } else {
                var extrasmall = 2;
            }
            if ($(this).data('verysmall')) {
                var verysmall = $(this).data('verysmall');
            } else {
                var verysmall = 1;
            }
            config.responsive = {
                0:{
                    items:verysmall
                },
                320:{
                    items:extrasmall
                },
                768:{
                    items:smallmedium
                },
                980:{
                    items:medium
                },
                1280:{
                    items:desktop
                },
                1600:{
                    items:desktop_full
                }
            }
            if ( $('html').attr('dir') == 'rtl' ) {
                config.rtl = true;
            }
            $(this).owlCarousel( config );
            // owl enable next, preview
            var viewport = jQuery(window).width();
            var itemCount = jQuery(".owl-item", $(this)).length;

            if(
                (viewport >= 1600 && itemCount <= desktop_full) //desktop_full
                || ((viewport >= 1280 && viewport < 1600) && itemCount <= desktop) //desktop
                || ((viewport >= 980 && viewport < 1280) && itemCount <= medium) //desktop
                || ((viewport >= 768 && viewport < 980) && itemCount <= smallmedium) //tablet
                || ((viewport >= 320 && viewport < 768) && itemCount <= extrasmall) //mobile
                || (viewport < 320 && itemCount <= verysmall) //mobile
            )
            {
                $(this).find('.owl-prev, .owl-next').hide();
            }
        } );

       // Fix owl in bootstrap tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            e.target // newly activated tab
            e.relatedTarget // previous active tab
            $(".owl-carousel").trigger('refresh.owl.carousel');
            
        });
    })    
    $('.dropdown_product_cat').SumoSelect({ csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything." });

    /*Remove active when click out of area menu mobile*/
    var $win = $(window);
    var $box = $('#tbay-mobile-menu,.topbar-device-mobile .active-mobile,#tbay-header.header-v4 .header-main .tbay-mainmenu .btn-offcanvas,#tbay-header.header-v5 .header-main .tbay-mainmenu .btn-offcanvas,.topbar-mobile .btn.btn-offcanvas,.wrapper-container .tbay-offcanvas');

    $win.on("click.Bst,click touchstart tap", function(event){       
    if ( $box.has(event.target).length == 0 && !$box.is(event.target) ){
            $('.wrapper-container').removeClass('active');
            $('#tbay-mobile-menu').removeClass('active');            
        }
    });

    function topbardevice() {
        var scroll = $(this).scrollTop();
        var objectSelect = $(".topbar-device-mobile").height();
        var mobileoffset = $("#tbay-mobile-menu").height();
        var scrollmobile = $(this).scrollTop();
        if (scroll <= objectSelect) {
            $(".topbar-device-mobile").addClass("active");
        } else {
            $(".topbar-device-mobile").removeClass("active");
        }        

        if (scrollmobile == 0) {
            $("#tbay-mobile-menu").addClass("offsetop");
        } else {
            $("#tbay-mobile-menu").removeClass("offsetop");
        }

    }
    topbardevice();
    $(window).scroll(function() {    
        topbardevice();

        $(".wpb_animate_when_almost_visible:not(.wpb_start_animation)").each(function() {
            var $this = $(this);
            var animate_height      = $(window).height();
            var NextScroll                    = $this.offset().top - $(window).scrollTop();
            if (NextScroll < (animate_height - 50) ) {
             $this.addClass("wpb_start_animation animated");
            }
         });
    });

})(jQuery)

/** 
 * 
 * ISO PROTYPO AUTOMATIC PLAY
 */
jQuery( document).ready( function($){
     //Offcanvas Menu
    $('[data-toggle="offcanvas"], .btn-offcanvas').on('click', function () {
        $('.row-offcanvas').toggleClass('active')           
    });    
   
 
    //counter up
    if($('.counterUp').length > 0){
        $('.counterUp').counterUp({
            delay: 10,
            time: 800
        });
    }

    if( $('.tbay-home-banner').length > 0 ) {
        $('.tbay-home-banner').parents('.vc_row-fluid').addClass('position-img');
    }

    //Search Mobile
    $(".topbar-device-mobile .search-device .show-search").on('click', function(e){
        e.preventDefault();
        $( ".topbar-device-mobile .search-device .tbay-search-form" ).slideToggle( 500, function() {});
    });

     //Search Tablet
    $(".topbar-mobile-right .search-device .show-search").on('click', function(e){
        e.preventDefault();
        $( ".topbar-mobile-right .search-device .tbay-search-form" ).slideToggle( 500, function() {});
    });

    //Search latop
    $("#tbay-header.header-v2 .header-search-v2 .btn-search-totop").click(function() {
        $( "#tbay-header.header-v2 .header-search-v2 .tbay-search-form" ).slideToggle( 500, function() {});
        $(this).toggleClass('active');
    });
    
    //Sticky Header
    var tbay_header = jQuery('#tbay-header');
    if( tbay_header.hasClass('main-sticky-header') ) {
        var CurrentScroll = 0;
        var tbay_width = jQuery(window).width();
        var header_height = tbay_header.height();
        var header_height_fixed = jQuery('#tbay-header.sticky-header1').height();
        $(window).scroll(function() {
            if(tbay_width >= 1024) {
                var NextScroll = jQuery(this).scrollTop();
                if (NextScroll > header_height) {
                    tbay_header.addClass('sticky-header1');
                    tbay_header.parent().css('margin-top', header_height);
                    tbay_header.addClass('sticky-header1').css("top", jQuery('#wpadminbar').outerHeight());
                } else {
                    tbay_header.removeClass('sticky-header1');
                    tbay_header.parent().css('margin-top', 0);
                }
                currentP = jQuery(window).scrollTop();
            }
        });
    }
    
    
    //Tooltip
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

    $('.topbar-mobile .dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });

    /** PRELOADER **/
    if ( $.fn.jpreLoader ) {
        var $preloader = $( '.js-preloader' );

        $preloader.jpreLoader({
            // autoClose: false,
        }, function() {
            $preloader.addClass( 'preloader-done' );
            $( 'body' ).trigger( 'preloader-done' );
            $( window ).trigger( 'resize' );
        });
    };

    $('[data-countdown="countdown"]').each(function(index, el) {
        var $this = $(this);
        var $date = $this.data('date').split("-");
        $this.tbayCountDown({
            TargetDate:$date[0]+"/"+$date[1]+"/"+$date[2]+" "+$date[3]+":"+$date[4]+":"+$date[5],
            regexpMatchFormat: "([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",
            regexpReplaceWith:"<div class=\"countdown-wrapper\"><div class=\"day\"><span>$1</span> DAYS </div><div class=\"hours\"><span>$2</span> HRS </div><div class=\"minutes\"><span>$3</span> MINS </div><div class=\"seconds\"><span>$4</span> SECS </div></div>"
        });
    });

    /* ---------------------------------------------
        Resize tbay menu
    --------------------------------------------- */
    function tbay_vertical_resize_Megamenu() {
        var window_size = $('body').innerWidth();

        if (window_size > 767) {
            if ($('.tbay_custom_menu').length > 0) {

                if($('.tbay_custom_menu').hasClass('tbay-vertical-menu')){ 
                    var full_width = parseInt($('#main-container.container').innerWidth());
                    var menu_width = parseInt($('.tbay-vertical-menu').innerWidth());
                    var w = (full_width - menu_width);
                    $('.tbay-vertical-menu').find('.active-mega-menu').each(function () {
                        $(this).children('.dropdown-menu').css('max-width', w + 'px');
                        $(this).children('.dropdown-menu').css('width', (full_width - 30) + 'px');
                    });
                }
            }
        } else {
            if ($('.tbay_custom_menu').length > 0) {

                if($('.tbay_custom_menu').hasClass('tbay-vertical-menu')){ 
                    // Treeview for Mobile Menu
                    $(".tbay-vertical-menu").treeview({
                        animated: 300, 
                        collapsed: true,
                        unique: true,
                        hover: false
                    });    
                }
            }
        }
    }

    var back_to_top = function () {
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 400) {
                jQuery('.tbay-to-top').addClass('active');
                jQuery('.tbay-category-fixed').addClass('active');
            } else {
                jQuery('.tbay-to-top').removeClass('active');
                jQuery('.tbay-category-fixed').removeClass('active');
            }
        });
        jQuery('#back-to-top').on('click', function () {
            jQuery('html, body').animate({scrollTop: '0px'}, 800);
            return false;
        });
    };
    back_to_top();

    $(window).load(function () {
        $('#loader').delay(100).fadeOut(400, function () {
            $('body').removeClass('tbay-body-loading');
            $(this).remove();
        });

    });

    /* ---------------------------------------------
     Scripts resize
     --------------------------------------------- */

    $(window).on("resize", function () {
        tbay_vertical_resize_Megamenu(); 
    });

    // fancybox video
    $(document).ready(function() {
        $(".fancybox-video").fancybox({
            maxWidth    : 800,
            maxHeight   : 600,
            fitToView   : false,
            width       : '70%',
            height      : '70%',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none'
        });
        $(".fancybox").fancybox();
        $(".treeview-menu .menu").treeview({
            animated: 300,
            collapsed: true,
            unique: true,
            persist: "location"
        });

        tbay_vertical_resize_Megamenu(); 
        
        // Treeview for Mobile Menu
        $(".navbar-offcanvas #main-mobile-menu").treeview({
            animated: 300,
            collapsed: true,
            unique: true,
            hover: false
        });
        
        $(".category-inside-content #category-menu").addClass('treeview');
        $(".category-inside-content #category-menu").treeview({
            animated: 300,
            collapsed: true,
            unique: true,
            persist: "location"
        });
        
        // Category Menu - header-v6
        $(".category-inside .category-inside-title").click(function() {
            $(this).parents('.category-inside').find(".category-inside-content").slideToggle("fast");
            $(this).parents('.category-inside').toggleClass("open");
        });
    });

        // mobile menu
    $('[data-toggle="offcanvas"], .btn-offcanvas').on('click', function () { 
        $('#wrapper-container').toggleClass('active');
        $('#tbay-mobile-menu').toggleClass('active');           
    });
 
    // preload page
    var $body = $('body');
    if ( $body.hasClass('tbay-body-loader') ) {
        setTimeout(function() {
            $body.removeClass('tbay-body-loader');   
            $('.tbay-page-loader').fadeOut(250);
        }, 300);
    }
    
    // Category Menu - Huy Pham
    $(".category-v6 .category-inside-title").click(function() {
        $(this).parents('.category-v6').find(".menu-category-menu-container").slideToggle("fast");
        $(this).parents('.category-v6').toggleClass("open");
    });
    
    // preload page
    var $body = $('body');
    if ( $body.hasClass('tbay-body-loading') ) {

        setTimeout(function() {
            $body.removeClass('tbay-body-loading');
            $('.tbay-page-loading').fadeOut(250);
        }, 300);
    }

    $('.button-show-search').click(function(){
        $('.tbay-search-form').addClass('active');
        return false;
    });
    $('.button-hidden-search').click(function(){
        $('.tbay-search-form').removeClass('active');
        return false;
    });
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires+";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
} 

jQuery(window).load(function(){
    setTimeout(function(){
        var hiddenmodal = getCookie('hiddenmodal');
        if (hiddenmodal == "") {
            jQuery('#popupNewsletterModal').modal('show');
        }
    }, 3000);
    

    $with = jQuery(window).width();

    if( $with < 1024 ) {

        jQuery(".wpb_animate_when_almost_visible:not(.wpb_start_animation)").each(function() {
            jQuery(this).removeClass("wpb_animate_when_almost_visible");
        });
    }
});
jQuery(document).ready(function($){
    $('#popupNewsletterModal').on('hidden.bs.modal', function () {
        setCookie('hiddenmodal', 1, 0.1);
    });

    if( jQuery('html').attr('dir') == 'rtl' ){
        jQuery('[data-vc-full-width="true"]').each( function(i,v){
            jQuery(this).css('right' , jQuery(this).css('left') ).css( 'left' , 'auto');
        });
    }
    
    function fixsliderhome3() {
        $with = $(window).width();
        $main_container  = $(".container").width();
        $main_container_full  = $(".container-full").width();
        $width_sum_full       = ($with - $main_container)/2 - ($with - $main_container_full)/2 - 30;

        if( $with > 1520 ) {
            $width_sum2 = - $width_sum_full;
            $('.rev_slider .fix-laptop').css('margin-left', $width_sum2);

        } else {
            $('.rev_slider .fix-laptop').removeAttr('style');

        }

    }    

    fixsliderhome3();
    
    function tocategoryfixed() {
        $with            = $(window).width();
        $main_container  = $(".container").width();
        $width_sum       = ($with - $main_container)/2;

        if( $width_sum >= 80 ) {
            $width_sum2     =    $width_sum  - 80;
            if($width_sum < 110) {
                if (jQuery('body').hasClass("rtl")) { 
                    $('.tbay-to-top').css({"left": $width_sum2, "right": "auto"}); 
                    $('.tbay-category-fixed').css({"right": $width_sum2, "left": "auto"});
                } else { 
                    $('.tbay-to-top').css({"right": $width_sum2, "left": "auto"});   
                    $('.tbay-category-fixed').css({"left": $width_sum2, "right": "auto"});
                }
            } else {
                $('.tbay-to-top').removeAttr("style");
                $('.tbay-category-fixed').removeAttr("style");
            }

            $('.tbay-category-fixed').css('display', 'block');
            $('.tbay-to-top').css('display', 'block');

        } else {

            $('.tbay-category-fixed').css('display', 'none');
            $('.tbay-to-top').css('display', 'none');

        }
    }

    tocategoryfixed();
    
    $(window).resize(function() {
        tocategoryfixed();
        fixsliderhome3();
    });
    
});
