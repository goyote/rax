var Php = (function() {
    'use strict';

    return {


        /**
         * Checks if the class method exists.
         *
         * @param {Object|String|Function} object
         * @param {String} method
         *
         * @return {Boolean}
         */
        methodExists: function(object, method) {
            if (typeof object === 'string') {
                return Boolean(window[object]) && this.methodExists(window[object], method);
            }

            if (typeof object === 'function') {
                object = new object();
            }

            return (typeof object[method] === 'function');
        }
    };

})();
