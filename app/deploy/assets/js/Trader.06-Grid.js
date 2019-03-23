Trader.Grid = {
    initQuoteLinksObserverInGrid: function() {
        var $showQuoteLink = $('.showQuoteLink');

        $showQuoteLink.click(function(event) {
            var symbol = $(event.target).parent().find('strong').html();
            Trader.Symbol.requestQuote(symbol, $('#dataTable'));
        });
    }
};
