<?php

namespace App\Support;

use DateTimeInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;

/**
 * Limit the number of words in a string.
 *
 * @link   https://github.com/illuminate/support/blob/v5.4.9/Str.php#L182
 * @param  string  $str    The string being truncated.
 * @param  integer $words  The number of words to truncate to.
 * @param  string  $suffix A value to append if $str is truncated.
 * @return string
 */
function str_words($str, $words = 100, $suffix = '…')
{
    if (is_object($str)) {
        $str = (string)$str;
    }

    preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $str, $matches);

    if (!isset($matches[0]) || mb_strlen($str) === mb_strlen($matches[0])) {
        return $str;
    }

    return rtrim($matches[0]).$suffix;
}

/**
 * Fill all empty translations with the first available translation.
 *
 * @param  Translation $input The translations to alter.
 * @param  string|null $lang  The fallback language to use.
 * @return integer The number of replacements performed.
 */
function fill_missing_translations(Translation $input, $lang = null)
{
    $messages = $input->data();
    $filled   = array_keys(array_filter($messages, 'strlen'));
    $missing  = array_values(array_diff(array_keys($messages), $filled));

    if (empty($filled) || empty($missing)) {
        return 0;
    }

    if ($lang === '*') {
        $lang = null;
    }

    if ($lang !== null && isset($messages[$lang]) && strlen($messages[$lang])) {
        $fallback = $messages[$lang];
    } else {
        $fallback = $messages[$filled[0]];
    }

    foreach ($missing as $lang) {
        $input[$lang] = $fallback;
    }

    return count($missing);
}

/**
 * Merge group permissions from config defined per object permissions.
 *
 * @param  array $group     Sidemenu group.
 * @param  array $resources Model permissions.
 * @return array Merged group.
 */
function merge_group_links(array $group, array $resources)
{
    if (!isset($group['links'])) {
        return $group;
    }

    foreach ($group['links'] as $key => $link) {
        if (!isset($resources[$key])) {
            continue;
        }

        if (isset($link['required_acl_permissions'])) {
            $permissions = $link['required_acl_permissions'];
        } else {
            $permissions = [];
        }

        $permissions[] = $key;

        $group['links'][$key]['required_acl_permissions'] = $permissions;
    }

    return $group;
}

/**
 * Start error log output
 */
function log_var_dump(...$values) : void
{
    ob_start();
    foreach ($values as $value) {
        var_dump($value);
    }
    $stuff = ob_get_contents();
    ob_end_clean();
    error_log($stuff);
}

/**
 * Start error log output
 */
function startLog() : void
{
    ob_start();
}

/**
 * End error log output
 */
function endLog() : void
{
    $stuff = ob_get_contents();
    ob_end_clean();
    error_log($stuff);
}

/**
 * @return array
 */
function secondsToTimeSections($inputSeconds)
{
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;
    $secondsInAWeek = 7 * $secondsInADay;

    // Extract weeks
    $weeks = floor($inputSeconds / $secondsInAWeek);

    // Extract days
    $days = floor($inputSeconds / $secondsInADay);

    // Extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // Extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // Extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    return [
        'week'   => [
            'label' => [
                'en' => 'week',
                'fr' => 'semaine'
            ],
            'value' => (int)$weeks,
        ],
        'day'    => [
            'label' => [
                'en' => 'day',
                'fr' => 'jour'
            ],
            'value' => (int)$days,
        ],
        'hour'   => [
            'label' => [
                'en' => 'hour',
                'fr' => 'heure'
            ],
            'value' => (int)$hours,
        ],
        'minute' => [
            'label' => [
                'en' => 'minute',
                'fr' => 'minute'
            ],
            'value' => (int)$minutes,
        ],
        'second' => [
            'label' => [
                'en' => 'second',
                'fr' => 'seconde'
            ],
            'value' => (int)$seconds,
        ],
    ];
}


/**
 * Convert number of seconds into hours, minutes and seconds
 * and return a string containing those values.
 *
 * @param integer $inputSeconds Number of seconds to parse
 * @return string
 */
function secondsToTime($inputSeconds, array $filteredSections = [], $locale = 'en')
{
    $timeParts = [];
    foreach (\App\Support\secondsToTimeSections($inputSeconds) as $ident => $section) {
        if (in_array($ident, $filteredSections)) {
            continue;
        }
        $section = \App\Support\formatTimeSection($section, $locale);
        if ($section) {
            $timeParts[] = $section;
        }
    }

    return implode(', ', $timeParts);
}

/**
 * @return string|null
 */
function formatTimeSection($section, $locale = 'en')
{
    $value = $section['value'];
    $label = $section['label'][$locale];
    return ($value > 0) ? sprintf('%s %s%s', $value, $label, $value === 1 ? '' : 's') : null;
}

/**
 * @return string
 */
function formatPhoneNumer($phoneNumber)
{
   return preg_replace('/\D+/', '', $phoneNumber);
}

/**
 * Format a local date/time according to locale settings.
 *
 * @param  string $format Format accepted by {@see strftime()}.
 * @param  mixed  $time   A date/time value. Valid formats are explained in
 *     {@link http://php.net/manual/en/datetime.formats.php Date and Time Formats}.
 * @return self
 */
function localizedTime($format, $time = null)
{
    if ($time === null) {
        $time = time();
    } elseif ($time instanceof DateTimeInterface) {
        $time = $time->getTimestamp();
    } elseif (is_string($time)) {
        $time = strtotime($time);
    } elseif (is_array($time)) {
        $time = mktime(...$time);
    }
    // Check for Windows to find and replace the %e
    // modifier correctly
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
    }
    $format = str_replace('%O', date('S', $time), $format);
    return strftime($format, $time);
}
