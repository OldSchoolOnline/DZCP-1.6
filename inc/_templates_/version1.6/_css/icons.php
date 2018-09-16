<?php
ob_start();
header("Content-type: text/css");
define('basePath', realpath('../../../..'));
$thumbgen = true;

include(basePath . '/vendor/autoload.php');
include(basePath . "/inc/debugger.php");
include(basePath . "/inc/config.php");

use Phpfastcache\CacheManager;

// Cache
$cache = CacheManager::getInstance($config_cache['storage'], $config_cache['config'],'default');

$CachedString = $cache->getItem('css_icons');
if (is_null($CachedString->get())) {
    function getIcons($dir)
    {
        $dp = @opendir($dir);
        $allicons = array();
        while ($icons = @readdir($dp)) {
            if ($icons != '.' && $icons != '..')
                array_push($allicons, $icons);
        }
        @closedir($dp);
        sort($allicons);
        return ($allicons);
    }

    $flags = getIcons('../../../images/flaggen/');
    $icons = '';
    for ($i = 0; $i < count($flags); $i++) {
        $icons .= " option[value=" . preg_replace("#\.gif|.jpg#Uis", "", $flags[$i]) . "]:before {";
        $icons .= " content: url(\"../../../images/flaggen/" . $flags[$i] . "\");";
        $icons .= "}";
    }

    $games = getIcons('../../../images/gameicons/');
    for ($i = 0; $i < count($games); $i++) {
        if (preg_match("=\.gif|.jpg=Uis", $games[$i])) {
            $icons .= "option[value=" . preg_replace("#\.#", "\.", $games[$i]) . "]:before {";
            $icons .= "  content: url(\"../../../images/gameicons/" . $games[$i] . "\");";
            $icons .= "}";
        }
    }

    $CachedString->set($icons)->expiresAfter(30);
    $cache->save($CachedString);
    echo $CachedString->get();
} else {
    echo $CachedString->get();
}
ob_end_flush();