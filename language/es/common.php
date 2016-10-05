<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
    'VIEW_EDIT_LOG'			=> ' <a href="%1s">(Historial de ediciones)</a>',
    'NO_EDIT_LOG'			=> 'No guardar edición en el historial',

    'COMPARE_VERSIONS'			=> 'Comparar versiones',

    'EDIT_LOG'			=> 'Historial de ediciones',
    'POST_TITLE'			=> 'Título del mensaje',

    'USER'		=> 'Usuario',
    'EDIT_TIME'				=> 'Fecha de edición',
    'EDIT_REASON'	=> 'Razón de la edición',

    'ORIGINAL_MESSAGE'			=> 'Mensaje original',
    'TEXT_CHANGES'	=> 'Cambios en el texto',

    'EDITLOG_BAD_OPTIONS_COUNT'			=> 'Debes seleccionar dos versiones para poder hacer una comparación.',
    'NO_POST_LOG'			=> 'Este mensaje no tiene ediciones guardadas en el historial.<br /><br /> <a href="%1s">Volver al mensaje</a>',
    'EDITLOG_NO_AUTH'			=> 'No estás autorizado a ver el historial de ediciones de este mensaje.<br /><br /> <a href="%1s">Volver al mensaje</a>',
    'EDITLOG_NO_DELETE_AUTH'	=> 'No estás autorizado a borrar entradas del historial de ediciones.<br /><br /> <a href="%1s">Volver</a>',
    'EDITLOG_DELETE_SUCCESS'	=> 'Se borraron las entradas del historial de ediciones.<br /><br /> <a href="%1s">Volver</a>',
    'LOG_EDITLOG_DELETE_SUCCESS'	=> '<strong>Eliminada entrada del registro de ediciones</strong><br />» <a href="%1s">%s2s</a>',
));
