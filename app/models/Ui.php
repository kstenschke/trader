<?php

namespace models;

class Ui {

    /**
     * @param  int       $amountOwned
     * @param  int|float $priceOld
     * @param  int|float $priceLatest
     * @return string
     */
    public static function renderTendencyValueTd($amountOwned, $priceOld, $priceLatest) : string
    {
        if ((int)$amountOwned === 0) {
            return '<td class="tendency watching"><img title="On Watchlist" class="icon black no-invert" style="width:16px;" src="var/assets/images/eye-black.png"/></td>';
        }
        if ($priceOld > $priceLatest) {
            return '<td title="Lower than price paid" class="tendency down">&#10136;</td>';
        }
        if($priceOld === $priceLatest) {
            return '<td title="Higher than price paid" class="tendency"></td>';
        }
        return '<td class="tendency up">&#10138;</td>';
    }
}
