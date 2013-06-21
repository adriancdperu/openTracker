<?php
/**
 * Copyright 2012, openTracker. (http://opentracker.nu)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @link          http://opentracker.nu openTracker Project
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * filename library/Addon.php
 * 
 * @author Wuild
 * @package openTracker.Addon
 */
class Addon {

    /**
     * Selected addon path
     * @var string
     */
    private $_addon;

    /**
     * Selected addon name
     * @var string 
     */
    private $_name;

    /**
     * Sets the addon path and the name.
     * @param string $name 
     */
    function __construct($name) {
        $this->_addon = PATH_APPLICATIONS . $name . "/";
        $this->_name = $name;
    }

    /**
     *  Check if the selected addon is installed.
     * @return boolean 
     */
    function isInstalled() {
        $db = new DB("addons");
        $db->select("addon_name = '" . $db->escape($this->_name) . "' and addon_installed = '1'");
        if ($db->numRows())
            return true;
    }

    /**
     * Check if the current user has access to view the addon
     * @return boolean 
     */
    function Access() {
        $acl = new Acl(USER_ID);

        $db = new DB("addons");
        $db->select("addon_name = '" . $db->escape($this->_name) . "' AND addon_installed = '1'");
        $db->nextRecord();
        if ((int) $acl->group < (int) $db->addon_group)
            return false;
        else
            return true;
    }

    /**
     *  Check if the addon has an admin page.
     * @return boolean 
     */
    function hasAdmin() {
        if (file_exists($this->_addon . "admin/main.php"))
            return true;
    }

    /**
     * Check if the addon has an install script
     * @return boolean 
     */
    function checkInstall() {
        if (file_exists($this->_addon . "install.php"))
            return true;
    }

    /**
     * Run the addon install script.
     * @return boolean 
     */
    function Install() {
        if ($this->checkInstall()) {
            include($this->_addon . "install.php");
            return true;
        }
        
    function NotInstall(){
        if ($this->checkInstall()){
            include($this->_addon . "install.php")}
            return false;
        }    
        
    }
    

}

?>
