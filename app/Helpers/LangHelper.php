<?php

/**
 * Translates the string and converts it to title case
 * @method utrans
 * @param  string  $value string to translate
 * @param  integer $count singular or plural
 * @return string
 */
function utrans($value, $count = 1, $params = [], $lang = null)
{
    return title_case(trans_choice($value, $count, $params, $lang));
}

/**
 * Translates the string and converts it to upper case
 * @method utrans
 * @param  string  $value string to translate
 * @param  integer $count singular or plural
 * @return string
 */
function uptrans($value, $count = 1, $params = [], $lang = null)
{
    return mb_strtoupper(trans_choice($value, $count, $params, $lang), 'UTF-8');
}

/**
 * Translates the string and converts its first letter to capital
 * @method utrans
 * @param  string  $value string to translate
 * @param  integer $count singular or plural
 * @return string
 */
function uctrans($value, $count = 1, $params = [], $lang = null)
{
    return mb_ucfirst(trans_choice($value, $count, $params, $lang), 'UTF-8');
}

/**
 * Translates the string and converts it to lower case.
 * @method utrans
 * @param  string  $value string to translate
 * @param  integer $count singular or plural
 * @return string
 */
function ultrans($value, $count = 1, $params = [], $lang = null)
{
    return mb_strtolower(trans_choice($value, $count, $params, $lang), 'UTF-8');
}

/**
 * Returns an URL adapted to $locale
 *
 * @param string|false   $url        URL to adapt in the current language. If not passed, the current url would be taken.
 * @param string|boolean $locale     Locale to adapt, false to remove locale
 * @param array          $attributes attributes to add to the route, if empty, the system would try to extract them from the url
 *
 * @throws UnsupportedLocaleException
 *
 * @return string|false URL translated, False if url does not exist
 */
function turl($url = null, $locale = null, $attributes = null)
{
    return LaravelLocalization::getLocalizedUrl($locale, $url, $attributes);
}

function translate_current($url = null, $locale = null, $attributes = null)
{
    if (count(request()->segments()) != 2) {
        return LaravelLocalization::getLocalizedUrl($locale, $url, $attributes);
    }
    $url_name = request()->segment(2);
    $pages = \App\Content::page()->likePath('/pages')->get()->loadTranslations();
    $page = $pages->first(function ($item) use ($locale, $url_name) {
        foreach ($item->toTranslatedArray(true)['url_name'] as $url) {
            if ($url == $url_name) {
                return true;
            }
        }
    });
    if ($page) {
        $url_name = $page->translate($locale, null)->url_name;
        return LaravelLocalization::getLocalizedUrl($locale, $url_name, $attributes);
    }

    return LaravelLocalization::getLocalizedUrl($locale, $url, $attributes);
}

/**
 * Translates the string without case convertion
 * @method untrans
 * @param  string  $value string to translate
 * @param  integer $count singular or plural
 * @return string
 */
function untrans($value, $count = 1, $params = [], $lang = null)
{
    return trans_choice($value, $count, $params, $lang);
}

/* html escaped plural version */
function _hn($singular, $plural, $number)
{
    return htmlspecialchars(ngettext($singular, $plural, $number));
}

function translate_date($select)
{
    $change = str_replace(
            ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            [uctrans('dates.Jan'), uctrans('dates.Feb'), uctrans('dates.Mar'), uctrans('dates.Apr'), uctrans('dates.May'), uctrans('dates.Jun'), uctrans('dates.Jul'), uctrans('dates.Aug'), uctrans('dates.Sep'), uctrans('dates.Oct'), uctrans('dates.Nov'), uctrans('dates.Dec')]
            , $select);

    return $change;
}

function mb_ucfirst($string, $encoding)
{
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}