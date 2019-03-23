Trader.DataTable = {

    init: function(areaIdentifier) {
        var $dataTableWrap = $('#dataTableWrap'),
            $siteHeader    = $('#siteHeaderContainer'),
            $footer        = $('footer'),
            pageHeight     = document.body.clientHeight,
            wrapHeight     = pageHeight - $siteHeader.height() - $footer.height() - 100;

        // Init data-table height/overflow
        $dataTableWrap.css({
            height   : wrapHeight + 'px',
            overflowY: 'scroll'
       });

        Trader.DataTable.initSortingColumn(areaIdentifier);

        Trader.DataTable.initColumnSortObserver(areaIdentifier);
    },

    /**
     * Init click-observer on table column: store preference which column to sort by
     *
     * @param {string} areaIdentifier
     */
    initColumnSortObserver: function(areaIdentifier) {
        $('#dataTable').find('th').click(function(e) {
            var $target        = $(e.target),
                columnName     = $target.html().split("<")[0],
                isDescending   = $target.hasClass('sorttable_sorted_reverse'),
                // value: column | amount of clicks to restore sorting
                preferenceValue = columnName + '|' + (isDescending ? 1 : 2);

            Trader.Preference.save('sorting-column', areaIdentifier, preferenceValue);
        });
    },

    /**
     * Restore previous (if any) sort-by column
     *
     * @param {string} areaIdentifier
     */
    initSortingColumn: function(areaIdentifier) {
        $.ajax({
                   type   : 'POST',
                   url    : 'ajax-preference-load.php',
                   data   : 'preference=sorting-column' +
                            '&area=' + areaIdentifier,

                   /**
                    * @param {object} results
                    */
                   success: function(results) {
                       if (results) {
                           results = results.split('|');
                           $("#dataTable th:contains('" + results[0] + "')").trigger('click');
                           if (results[1] === '2') {
                               $("#dataTable th:contains('" + results[0] + "')").trigger('click');
                           }
                       }
                   }
               });
    }
};