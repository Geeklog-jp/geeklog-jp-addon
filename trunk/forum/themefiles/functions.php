<?php

// this file can't be used on its own
if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.php') !== false) {
    die ('This file can not be used on its own!');
}

$_IMAGE_TYPE = 'png';

if (!defined ('XHTML')) {
    define('XHTML',''); // change this to ' /' for XHTML
}

$result = DB_query ("SELECT onleft,name FROM {$_TABLES['blocks']} WHERE is_enabled = 1");
$nrows = DB_numRows ($result);
for ($i = 0; $i < $nrows; $i++) {
    $A = DB_fetchArray ($result);
        if ($A['onleft'] == 1) {
            $_BLOCK_TEMPLATE[$A['name']] = 'blockheader-left.thtml,blockfooter-left.thtml';
        } else {
            $_BLOCK_TEMPLATE[$A['name']] = 'blockheader-right.thtml,blockfooter-right.thtml';
    }
}

$_BLOCK_TEMPLATE['_msg_block'] = 'blockheader-message.thtml,blockfooter-message.thtml';

$_BLOCK_TEMPLATE['whats_related_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';
$_BLOCK_TEMPLATE['story_options_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';


/********************* FORUM PLUGIN v2.7+ Setup for block layout to use ********************
* Fourm Plugin for Geeklog v1.4.1 available at http://www.portalparts.com
* Set the following for which Geeklog block columns you want to show along with the forum.
* Options are: 'leftblocks', 'rightblocks', 'allblocks', 'noblocks'
* For example, set to noblocks to not show any blocks (and have the forum span the entire page.)
***************************************************************************************/
$CONF_FORUM['showblocks'] = 'leftblocks';

/********************* FORUM PLUGIN v2.7+  Setup for user menu style to use ****************
* Show the usermenu as a block menu or as a top navbar
* Note: Need to show leftblocks or rightblocks if usermenu option set to blockmenu
* Options are 'blockmenu' or 'navbar' or 'none'
***************************************************************************************/
$CONF_FORUM['usermenu'] = 'navbar';

?>
