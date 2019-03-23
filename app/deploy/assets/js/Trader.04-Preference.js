Trader.Preference = {

    /**
     * Save given preference for current user
     *
     * @param {string} preferenceIdentifier
     * @param {string} areaIdentifier
     * @param {string} preferenceValue
     */
    save: function(preferenceIdentifier, areaIdentifier, preferenceValue) {
        $.ajax({
           type   : 'POST',
           url    : 'ajax-preference-save.php',
           data   : 'preference=' + preferenceIdentifier +
                    '&area=' + areaIdentifier +
                    '&value=' + preferenceValue,
           /**
            * @param {object} results
            */
           success: function(results) {

           }
       });
    }
    
};