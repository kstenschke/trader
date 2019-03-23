Trader = {

    init: function() {
        Trader.Utils.Ajax.init();
        Trader.Clock.init();
    },

    Utils: {}
};

$(document).ready(function() {
    Trader.init();
});


