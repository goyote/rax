(function($) {
    'use strict';

    /**
     * Tooltip class.
     *
     * @class
     * @constructor
     *
     * @param {jQuery} $element
     * @param {object} options
     */
    var Tooltip = function($element, options) {
        this.init('tooltip', $element, options);
    };

    /**
     * Tooltip methods.
     *
     * @lends {Tooltip.prototype}
     */
    Tooltip.prototype = {
        /**
         * @type {Tooltip}
         */
        constructor: Tooltip,

        /**
         * Constructor.
         *
         * @method init
         *
         * @param {string} type
         * @param {jQuery} $element
         * @param {object} options
         */
        init: function(type, $element, options) {
            this.type     = type;
            this.$element = $element;
            this.enabled  = true;

            this.setOptions(options);
            this.addListeners();
        },

        /**
         * Adds the events listeners.
         *
         * @param {string|Array.<string>=} events
         */
        addListeners: function(events) {
            if (!events) {
                events = this.options.events;
            }

            if (typeof events === 'string') {
                events = [events];
            }

            $.each(events, function(index, event) {
                this['add' + event + 'Listener']();
            });
        },



        /**
         * Merges & stores the plugin options.
         *
         * @method setOptions
         * @chainable
         *
         * @param {object} options
         *
         * @return {Tooltip}
         */
        setOptions: function(options) {
            options = $.extend({}, $.fn[this.type].defaults, options, this.$element.data());

            if (options.delay && typeof options.delay === 'number') {
                options.delay = {
                    show: options.delay,
                    hide: options.delay
                };
            }

            this.options = options;

            return this;
        },

        show: function() {

        },

        enable: function() {
            this.enabled = true;
        },

        disable: function() {
            this.enabled = false;
        },

        toggleEnabled: function() {
            this.enabled = !this.enabled;
        }
    };

    /**
     * Tooltip jQuery plugin.
     *
     * @method tooltip
     *
     * @param {object|string} method
     *
     * @return {jQuery}
     */
    $.fn.tooltip = function(method) {
        var options = $.isPlainObject(method) ? method : {};

        return this.each(function() {
            var $this   = $(this),
                tooltip = $this.data('tooltip');

            if ((tooltip instanceof Tooltip) === false) {
                tooltip = new Tooltip($this, options);
                $this.data('tooltip', tooltip);
            }

            if (typeof method === 'string') {
                tooltip[method]();
            }
        });
    };

    /**
     * Default plugin options.
     *
     * @namespace
     *
     * @property {boolean}               animation
     * @property {number}                delay
     * @property {string|Array.<string>} events
     * @property {string}                placement
     * @property {boolean}               selector
     * @property {string}                template
     * @property {string}                title
     */
    $.fn.tooltip.defaults = {
        animation: true,
        delay    : 0,
        events   : 'hover',
        placement: 'top',
        selector : false,
        template : '<div class="tooltip"><div class="tooltip-content"/><div class="tooltip-arrow"/></div>',
        title    : ''
    };

})(window.jQuery);
