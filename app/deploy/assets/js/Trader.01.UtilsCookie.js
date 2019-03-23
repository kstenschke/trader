Trader.Utils.Cookie = {

    /**
     * @param   {string} cookieName
     * @returns {string}
     */
    getCookie: function(cookieName) {
        var c_value = document.cookie,
	        c_start = c_value.indexOf(' ' + cookieName + '=');

        if (c_start === -1) {
            c_start = c_value.indexOf(cookieName + '=');
        }
        if (c_start === -1) {
            return null;
        }

        c_start = c_value.indexOf('=', c_start) + 1;
        var c_end = c_value.indexOf(';', c_start);
        if(c_end === -1) {
            c_end = c_value.length;
        }

        return Trader.Utils.String.unescape(c_value.substring(c_start, c_end));
    },

    /**
     * @param {string}        cookieName
     * @param {number|string} value
     * @param {number}        expirationDays
     */
    setCookie: function(cookieName, value, expirationDays) {
        var expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + expirationDays);

        document.cookie = cookieName + '=' + (
            Trader.Utils.String.escape(value) + ((expirationDays === null)
                ? ''
                : '; expires=' + expiryDate.toUTCString()));
    }
};
