<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * Estonian translation by Mikk - [et.translations@phpbbeesti.net] - August, 2016
 *
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'VIEW_EDIT_LOG'			=> ' <a href="%1s">(Muudatuste logi)</a>',
    'NO_EDIT_LOG'			=> 'Ära salvesta muudatuste ajalugu',

    'COMPARE_VERSIONS'			=> 'Võrdluse versioonid',

    'EDIT_LOG'			=> 'Muudatuste logi',
    'POST_TITLE'			=> 'Postitus',

    'USER'		=> 'Kasutaja',
    'EDIT_TIME'				=> 'Muudatuse aeg',
    'EDIT_REASON'	=> 'Muudatuse põhjus',

    'ORIGINAL_MESSAGE'			=> 'Algne sõnum',
    'TEXT_CHANGES'	=> 'Muudatused tekstis',

    'EDITLOG_BAD_OPTIONS_COUNT'			=> 'Sa pead valima võrdlemiseks kaks versiooni.',
    'NO_POST_LOG'			=> 'Sellel sõnumil ei ole salvestatud muudatusi ajalukku.<br /><br /> <a href="%1s">Tagasi sõnumi juurde</a>',
    'EDITLOG_NO_AUTH'			=> 'Sul ei ole õigusi vaadata muudatuse ajalugu antud postitusel.<br /><br /> <a href="%1s">Tagasi sõnumi juurde</a>',
    'EDITLOG_NO_DELETE_AUTH'	=> 'Sul ei ole õigusi kustutada muudatuste kirjeid ajaloos.<br /><br /> <a href="%1s">Tagasi</a>',
    'EDITLOG_DELETE_SUCCESS'	=> 'Kirjed on kustutatud muudatustest.<br /><br /> <a href="%1s">Tagasi</a>',
    'LOG_EDITLOG_DELETE_SUCCESS'	=> '<strong>Kustutatud muudatuste ajaloo kirje </strong><br />» <a href="%1s">%s2s</a>',
));
