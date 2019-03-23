Trader.Symbol = {

    initQuoteRefreshShown: function(symbolIDs) {
        $('#quote_refresh_shown').click(function() {
            document.location.href='ajax-refresh-symbols.php?ids=' + symbolIDs;
            return false;
        });    
    },
    
    /**
     * Install key-up observer: find symbols by freetext search over symbol table
     */
    initFindSymbolObserver: function() {
        var $sword = $('#sword');
        $sword.bind('keyup', function() {
            Trader.Symbol.requestSymbolSearch();
        });
    },

    requestSymbolSearch: function() {
        var $sword = $('#sword'),
            value  = $sword.val();

        $.ajax({
           type   : 'POST',
           url    : 'ajax-symbol-search.php',
           data   : 'sword=' + value,

           /**
            * @param {object} results
            */
           success: function(results) {
               var amountOptions = results.split("\n").length,
                   size = amountOptions < 15 ? amountOptions : 15,
                   $searchResults = $('#searchResults');

               $searchResults.html(results).attr('size', size);
               $(document).on('change', $($searchResults.find('option')), function(){
                   $('#symbol').val($('#searchResults option')[document.getElementById('searchResults').selectedIndex].value);
                   Trader.Symbol.requestQuote();
               });

               var amountSymbols = (amountOptions - 1);
               $('#results-summary').html(amountSymbols + ' ' + (amountSymbols === 1 ? '###I::Symbol###' : '###I::Symbols###'));
           }
       });
    },

    /**
     * @param {string} [symbol]
     * @param {*}      [$insertPopupBefore]
     */
    requestQuote: function(symbol, $insertPopupBefore) {
        symbol             = typeof symbol === 'undefined' ? $('#symbol').val() : symbol;
        $insertPopupBefore = typeof $insertPopupBefore === 'undefined'
            ? $('#mainForm')
            : $insertPopupBefore;

        if (symbol.length > 2) {
            $.ajax({
               type   : 'POST',
               url    : 'ajax-symbol-quote.php',
               data   : 'symbol=' + symbol,

               /**
                * @param {object} result
                */
               success: function(result) {
                   $('#popup').remove();

                   $('<div id="popup">' + result + '</div>').insertBefore($insertPopupBefore);
               }
           });
        }
    },

    requestQuoteNextListed: function() {
        $('#searchResults option:selected').next().attr('selected', 'selected');
        $('#symbol').val($('#searchResults option:selected').val());
        Trader.Symbol.requestQuote();
    },

    requestQuotePreviousListed: function() {
        $('#searchResults option:selected').prev().attr('selected', 'selected');
        $('#symbol').val($('#searchResults option:selected').val());
        Trader.Symbol.requestQuote();
    },

    toggleSearchInPortfolio: function() {
        var value = $('#sword').val();

        value = value.indexOf('<portfolio>')
            ? value.replace('<portfolio>', '')
            : value + '<portfolio>';

        $('#sword').val(value);
    },

    toggleSearchInWatchlist: function() {
        
    }
};