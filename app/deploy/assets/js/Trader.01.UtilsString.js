Trader.Utils.String = {

    /**
     * Wrapper for deprecated window.unescape()
     *
     * @param   {string} str
     * @returns {string}
     */
    unescape: function(str) {
        if (window.decodeURI) {
            return window.decodeURI(str);
        }
        if (window.unescape) {
            return window.unescape(str);
        }
    },

    /**
     * Wrapper for deprecated window.escape()
     *
     * @param   {string} str
     * @returns {string}
     */
    escape: function(str) {
        if (window.encodeURI) {
            return window.encodeURI(str);
        }
        if (window.escape) {
            return window.escape(str);
        }
    }
};
