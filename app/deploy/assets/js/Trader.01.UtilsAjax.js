Trader.Utils.Ajax = {

    init: function() {
        Trader.Utils.Ajax.insertAjaxSpinnerLayer();
        Trader.Utils.Ajax.initAjaxObserver();
    },

    insertAjaxSpinnerLayer: function() {
        var $spinnerLayer =
                $('<div id="ajaxSpinner" style="text-align:center;">' +
                  '<img src="var/assets/images/spinner.gif"/>' +
                  '</div>').appendTo('body');
        $spinnerLayer.css({
                              backgroundColor: '#000',
                              height         : '100%',
                              width          : '100%',
                              left           : 0,
                              top            : 0,
                              position       : 'fixed',
                              opacity        : 0.7,
                              display        : 'none'
                          });
    },

    /**
     * Show/hide spinner layer when AJAX request starts/ends
     */
    initAjaxObserver: function() {
        $(document)
            .ajaxStart(function(){
                $('#ajaxSpinner').show();
            })
            .ajaxStop(function(){
                $('#ajaxSpinner').hide();
            });
    }
};
