<?php
/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author Wuild
 * @package openTracker
 */
if (!defined("INCLUDED"))
    die("Access denied");

try {

    if (!isset($_GET['id']))
        throw new Exception("Missing id");

    if (isset($_GET['confirm'])) {
        ?>
        <div class="confirm">
            <center><?php echo _t("Are you sure you wish to delete this user?") ?><br /><br />
                <a href="<?php echo page("admin", "members", "delete", "", "", "id=" . $_GET['id']) ?>"><span class="btn red"><?php echo _t("Yes"); ?></span></a> 
                <a href="<?php echo page("admin", "members") ?>"><span class="btn"><?php echo _t("No"); ?></span></center></a>
        </div>
        <?
    } else {
        $db = new DB("users");
        $db->delete("user_id = '" . $db->escape($_GET['id']) . "'");
        header("location: " . page("admin", "members"));
    }
} catch (Exception $e) {
    echo error(_t($e->getMessage()));
}
?>
