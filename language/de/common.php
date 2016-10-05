<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * German translation / Deutsche Übersetzung: Frank Ingermann [info@frankingermann.de]
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
    'VIEW_EDIT_LOG'			=> ' <a href="%1s">(Versions-Historie)</a>',
    'NO_EDIT_LOG'			=> 'Versionshitorie nicht speichern',

    'COMPARE_VERSIONS'			=> 'Vergleiche Versionen',

    'EDIT_LOG'			=> 'Versions-Historie',
    'POST_TITLE'			=> 'Beitrag',

    'USER'		=> 'Benutzer',
    'EDIT_TIME'				=> 'Bearbeitungszeitpunkt',
    'EDIT_REASON'	=> 'Bearbeitungsgrund',

    'ORIGINAL_MESSAGE'			=> 'Original-Beitrag',
    'TEXT_CHANGES'	=> 'Änderungen im Text',

    'EDITLOG_BAD_OPTIONS_COUNT'			=> 'Bitte wähle zwei Versionen zum Vergleichen aus.',
    'NO_POST_LOG'			=> 'Zu diesem Beitrag gibt es keine Versions-Historie.<br /><br /> <a href="%1s">Zurück zum Beitrag</a>',
    'EDITLOG_NO_AUTH'			=> 'Du darfst die Versions-Historie dieses Beitrags nicht einsehen.<br /><br /> <a href="%1s">Zurück zum Beitrag</a>',
    'EDITLOG_NO_DELETE_AUTH'	=> 'Du darfst keine Einträge aus der Versions-Historie löschen.<br /><br /> <a href="%1s">Zurück</a>',
    'EDITLOG_DELETE_SUCCESS'	=> 'Einträge wurden gelöscht.<br /><br /> <a href="%1s">Zurück</a>',
    'LOG_EDITLOG_DELETE_SUCCESS'	=> '<strong>Löschte Einträge aus der Versions-Historie </strong><br />» <a href="%1s">%s2s</a>',
));
