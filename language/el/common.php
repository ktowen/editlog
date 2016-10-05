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
    'VIEW_EDIT_LOG'			=> ' <a href="%1s">(Ιστορικό αλλαγών)</a>',
    'NO_EDIT_LOG'			=> 'Μη αποθήκευση του ιστορικού αλλαγών',

    'COMPARE_VERSIONS'			=> 'Σύγκριση εκδόσεων',

    'EDIT_LOG'			=> 'Ιστορικό αλλαγών',
    'POST_TITLE'			=> 'Δημοσίευση',

    'USER'		=> 'Χρήστης',
    'EDIT_TIME'				=> 'Χρόνος επεξεργασίας',
    'EDIT_REASON'	=> 'Λόγος επεξεργασίας',

    'ORIGINAL_MESSAGE'			=> 'Αρχικό μήνυμα',
    'TEXT_CHANGES'	=> 'Αλλαγές στο κείμενο',

    'EDITLOG_BAD_OPTIONS_COUNT'			=> 'Πρέπει να επιλέξετε δύο εκδόσεις για να κάνετε μια σύγκριση.',
    'NO_POST_LOG'			=> 'Αυτό το μήνυμα δεν έχει αποθηκευμένες αλλαγές στο ιστορικό.<br /><br /> <a href="%1s">Επιστροφή στο μήνυμα</a>',
    'EDITLOG_NO_AUTH'			=> 'Δεν έχετε πρόσβαση στο ιστορικό αλλαγών αυτού του μηνύματος.<br /><br /> <a href="%1s">Επιστροφή στο μήνυμα</a>',
    'EDITLOG_NO_DELETE_AUTH'	=> 'Δεν μπορείτε να διαγράψετε εγγραφές του ιστορικού αλλαγών.<br /><br /> <a href="%1s">Πίσω</a>',
    'EDITLOG_DELETE_SUCCESS'	=> 'Οι εγγραφές διεγράφησαν.<br /><br /> <a href="%1s">Πίσω</a>',
    'LOG_EDITLOG_DELETE_SUCCESS'	=> '<strong>Διέγραψε εγγραφές του ιστορικού αλλαγών </strong><br />» <a href="%1s">%s2s</a>',
));
