<?php
ini_set('display_errors', 'On');

if (isset($_GET['timezone']))
    date_default_timezone_set($_GET['timezone']);

$datetimeformat = $_GET['datetimeformat'] ?? 'Y/m/d H:i:s';

$sqlite = new SQLite3('/etc/radacct/sqlite.db', SQLITE3_OPEN_READONLY);

$useiByte = isset($_GET['use1024']) && $_GET['use1024'] == 'true';

$hideuser = isset($_GET['hideuser']) && $_GET['hideuser'] == 'true';
$hidesensitive = isset($_GET['hidesensitive']) && $_GET['hidesensitive'] == 'true';

$hide_td = '<td>*****</td>';

$v = function($v) { return $v; };

function closesession()
{
    global $sqlite;
    $sqlite->close();
}

$HBYTE_TYPE_DICT = [ '', 'K', 'M', 'G', 'P'];

function human_bytes($src, $is_bit = false, $use1024 = null)
{
    global $HBYTE_TYPE_DICT;

    if ($use1024 == null)
    {
        global $useiByte;
        $use1024 = $useiByte;
    }

    $k = $use1024 ? 1024 : 1000;

    if ($is_bit) $src /= 8;

    $hnum = 0;
    $type = 0;
    do {
        $hnum = $src / pow($k, $type);
        if ($hnum < 1000) break;
        $type ++;
    }
    while ($type <= count($HBYTE_TYPE_DICT));

    $aftstr = $HBYTE_TYPE_DICT[$type] . ($use1024 ? 'i' : '') . 'B';

    return (floor($hnum * 100) / 100) . ' ' . $aftstr;
}

function uptime($sec)
{
    $s_1d = 3600 * 24;
    $s_1h = 3600;

    $d = floor($sec / $s_1d);
    $h = floor($sec / $s_1h % $s_1h);
    $m = floor($sec / 60 % 60);
    $s = $sec % 60;

    if ($h < 10) $h = '0' . $h;
    if ($m < 10) $m = '0' . $m;
    if ($s < 10) $s = '0' . $s;

    return "{$d}d $h:$m:$s";
}