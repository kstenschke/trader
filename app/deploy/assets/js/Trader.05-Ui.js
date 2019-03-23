Trader.Ui = {
    toggleBuyFilters: function() {
        var $buyFilters       = $('#buyFilters'),
	        $toggleBuyFilters = $('#toggleBuyFilters');

        $buyFilters.toggle();

        if ($buyFilters.is(':visible')) {
            $toggleBuyFilters.css({
                'margin'    : '10px 12px 0 0',
                'background-image': 'url("var/assets/images/caret-down.png")'
            });
        } else {
            $toggleBuyFilters.css({
                'background-image': 'url("var/assets/images/search-white.png")',
                'margin'  : '17px 12px -60px',
                'position': 'relative',
                'z-index' : '10'
            });
        }
    }
};
