<?php

/**
 *
 * GK Aside Menu class
 *
 * based on T3 Framework menu class
 *
 * @version             3.0.0
 * @package             Gavern Framework
 * @copyright			Copyright (C) 2010 - 2012 GavickPro. All rights reserved.
 *               
 */
 
// No direct access.
defined('_JEXEC') or die;

if (!defined('_GK_ASIDE_MENU_CLASS')) {
    define('_GK_ASIDE_MENU_CLASS', 1);
    require_once (dirname(__file__) . DS . "GKBase.class.php");

    class GKAsideMenu extends GKMenuBase {
        function __construct($params) {
            $params->set('gkmenu', 1);
            parent::__construct($params);
        }

        function beginMenu($startlevel = 0, $endlevel = 10) {
            echo "<div id=\"gkAsideMenu\" class=\"gkMenu\">\n";
        }

        function endMenu($startlevel = 0, $endlevel = 10) {
            echo "\n</div>";
        }

        function beginMenuItems($pid = 0, $level = 0, $return = false) {
            if ($level) {
                if ($this->items[$pid]->gkparams->get('group')) {
                    $data = "";
                } else {
                    $style = $this->getParam('gk-style', 1);
                    if (!method_exists($this, "beginMenuItems$style")) $style = 1; //default
                    $data = call_user_func_array(array($this, "beginMenuItems$style"), array($pid, $level, true));
                }
                
                if ($return) return $data; else echo $data;
            }
        }

        function endMenuItems($pid = 0, $level = 0, $return = false) {
            if ($level) {
                if ($this->items[$pid]->gkparams->get('group')) {
                    $data = "";
                } else {
                    $style = $this->getParam('gk-style', 1);
                    if (!method_exists($this, "endMenuItems$style")) $style = 1; //default
                    $data = call_user_func_array(array($this, "endMenuItems$style"), array($pid, $level, true));
                }
                
                if ($return) return $data; else echo $data;
            }
        }

        function beginSubMenuItems($pid = 0, $level = 0, $pos, $i, $return = false) {
            $data = '';
            
            if (@$this->children[$pid]) $data .= "<ul class=\"gkmenu level$level\">";
            
            if ($return) return $data; else echo $data;
        }

        function endSubMenuItems($pid = 0, $level = 0, $return = false) {
            $data = '';
            if (@$this->children[$pid]) $data .= "</ul>";
            
            if ($return) return $data; else echo $data;
        }

        function beginSubMenuModules($item, $level = 0, $pos, $i, $return = false) {
			$data = '';
			if ($return) return $data; else echo $data;
		}


        function endSubMenuModules($item, $level = 0, $return = false) {
            $data = '';
            if ($return) return $data;
            else echo $data;
        }

        function genClass($mitem, $level, $pos) {
            $iParams = new JRegistry($mitem->params);
            $cls =  ($pos ? " $pos" : "");
            if (@$this->children[$mitem->id] || (isset($mitem->content) && $mitem->content)) {
                if ($mitem->gkparams->get('group'))
                    $cls .= " group";
                else
                    if ($level < $this->getParam('endlevel') && isset($this->children[$mitem->id][0]))
                    $cls .= " haschild";
            }
            $active = in_array($mitem->id, $this->open);
            if (!preg_match('/group/', $cls))
                $cls .= ($active ? " active" : "");
            if ($mitem->gkparams->get('class'))
                $cls .= " " . $mitem->gkparams->get('class');
            return $cls;
        }
		
		function genMenuItem($item, $level = 0, $pos = '', $ret = 0, $desc = true) {
            $data = '';
            $tmp = $item;
            $tmpname = ($this->getParam('gkmenu') && !$tmp->params->get('menu_text', 1 )) ? '' : $tmp->name;
            $active = $this->genClass($tmp, $level, $pos);
            
            if ($active) $active = " class=\"$active\"";

            $id = 'id="menu' . $tmp->id . '"';
            $tmpname = str_replace('"','&quot;', $tmpname);
			$txt = '';

			if ($tmp->params->get('menu_image', 0)) {
				$txt .= '<img src="'.$tmp->params->get('menu_image', 0).'" alt="'.$tmpname.'" />';
			}
			
			$txt .= $tmpname;
            
            $title = "title=\"$tmpname\"";

            if ($tmp->type == 'menulink') {
                $menu = JSite::getMenu();
                $alias_item = clone ($menu->getItem($tmp->query['Itemid']));
                if(!$alias_item) return false;
                else $tmp->url = $alias_item->link;
            }

            $rel = "";
            if ($tmp->gkparams->get('gk_rel')) {
                            $rel = " rel=\"nofollow\"";
            }
            if ($txt != '') {
                if ($tmp->type == 'separator') {
                    $data = '<a href="#" ' . $active . ' ' . $id . ' ' . $title . ' ' . $rel . '>' . $txt . '</a>';
                } else {
                    if ($tmp->url != null) {
                        switch ($tmp->browserNav) {
                            default:
                            case 0:
                                // _top
                                $data = '<a href="' . $tmp->url . '" ' . $active . ' ' . $id . ' ' . $title .
                                    ' ' . $rel . '>' . $txt . '</a>';
                                break;
                            case 1:
                                // _blank
                                $data = '<a href="' . $tmp->url . '" target="_blank" ' . $active . ' ' . $id .
                                    ' ' . $title . ' ' . $rel . '>' . $txt . '</a>';
                                break;
                            case 2:
                                $data = '<a href="' . $tmp->url . '" target="_blank" ' . $active . ' ' . $id .
                                    ' ' . $title . ' ' . $rel . '>' . $txt . '</a>';
                                break;
                        }
                    } else {
                        $data = '<a ' . $active . ' ' . $id . ' ' . $title . ' ' . $rel . '>' . $txt . '</a>';
                    }
                }
            }
            
            if ($this->getParam('gkmenu')) {
                if ($tmp->gkparams->get('group') && $data)
                    $data = "<header>$data</header>";
                if (isset($item->content) && $item->content) {
                    if ($item->gkparams->get('group')) {
                        $data .= "<div class=\"group-content\">".($item->content)."</div>";
                    } else {
                        $data .= $this->beginMenuItems($item->id, $level + 1, true);
                        $data .= $item->content;
                        $data .= $this->endMenuItems($item->id, $level + 1, true);
                    }
                }
            }
            if ($ret)
                return $data;
            else
                echo $data;

        }

        function beginMenuItem($mitem = null, $level = 0, $pos = '') {
            $active = trim($this->genClass($mitem, $level, $pos));
            if ($active) $active = " class=\"$active\"";
            echo "<li $active>";
        }
        function endMenuItem($mitem = null, $level = 0, $pos = '') {
            echo "</li>";
        }

        function beginMenuItems1($pid = 0, $level = 0, $return = false) {
            $cols = $pid && $this->getParam('gkmenu') && isset($this->items[$pid]->cols) && $this->items[$pid]->cols ? $this->items[$pid]->cols : 1;
            
            $data = "";
            if ($return) return $data; else echo $data;
        }

		function endMenuItems1($pid=0, $level=0, $return = false){
			$data = "";
			if($return) return $data; else echo $data;
		}
		
		function getParam($paramName, $default = null) {
            return $this->_params->get($paramName, $default);
        }
    }
}