<?php

namespace helpers;

class Dates {

    const SECONDS_MIN  = 60;
    const SECONDS_HOUR = 3600;
    const SECONDS_DAY  = 86400;
    const SECONDS_WEEK = 604800;

    /**
     * Interval formatting, using the two biggest interval parts.
     * On small intervals: minutes and seconds.
     * On big intervals: months and days.
     * Only the two biggest parts are used.
     *
     * @param  \DateTime|int  $start
     * @param  \DateTime|null $end
     * @param  bool          $short
     * @return string
     */
    public static function formatDateDiff($start, $end = null, $short = false): string
    {
        if (!($start instanceof \DateTime)) {
            if (is_numeric($start)) {
                /** @noinspection CallableParameterUseCaseInTypeContextInspection */
                $start = date('d-m-Y H:i', $start);
            }
            $start = new \DateTime($start);
        }

        if ($end === null) {
            $end = new \DateTime();
        }
        $end = $end instanceof \DateTime ? $end : new \DateTime($start);

        $interval = $end->diff($start);
        $doPlural = function($number, $str) {
            return $number !== 1 ? $str . 's' : $str;
        };

        $format = [];
        if ($interval->y !== 0) {
            $format[] = '%y ' . $doPlural($interval->y, $short ? 'y' : 'year');
        }
        if ($interval->m !== 0) {
            $format[] = '%m ' . $doPlural($interval->m, $short ? 'm' : 'month');
        }
        if ($interval->d !== 0) {
            $format[] = '%d ' . $doPlural($interval->d, 'day');
        }
        if ($interval->h !== 0) {
            $format[] = '%h ' . $doPlural($interval->h, $short ? 'hr' : 'hour');
        }
        if ($interval->i !== 0) {
            $format[] = '%i ' . $doPlural($interval->i, $short ? 'min' : 'minute');
        }
        if ($interval->s !== 0) {
            if (!count($format)) {
                return 'less than a minute';
            }
            $format[] = '%s ' . $doPlural($interval->s, $short ? 'sec' : 'second');
        }

        // We use the two biggest parts
        $format = count($format) > 1
            ? array_shift($format) . ' / ' . array_shift($format)
            : array_pop($format);

        return $interval->format($format);
    }
}
