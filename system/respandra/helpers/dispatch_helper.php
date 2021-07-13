<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// i18n provides strings in the current language
function i18n($key, $value = null)
{
    static $_i18n = array();

    if ($key === 'source') {
      if (file_exists($value))
        $_i18n = parse_ini_file($value, true);
      else
        $_i18n = parse_ini_file(FCPATH . 'lang/en_US.ini', true);
    } elseif ($value == null)
        return (isset($_i18n[$key]) ? $_i18n[$key] : '_i18n_' . $key . '_i18n_');
    else
        $_i18n[$key] = $value;
}

function config($key, $value = null)
{
    static $_config = array();

    if ($key === 'source' && file_exists($value))
        $_config = parse_ini_file($value, true);
    elseif ($value == null)
        return (isset($_config[$key]) ? $_config[$key] : null);
    else
        $_config[$key] = $value;
}

function save_config($data = array(), $new = array())
{
    global $config_file;

    $string = file_get_contents($config_file) . "\n";

    foreach ($data as $word => $value) {
        $value = str_replace('"', '\"', $value);
        $string = preg_replace("/^" . $word . " = .+$/m", $word . ' = "' . $value . '"', $string);
    }
    $string = rtrim($string);
    foreach ($new as $word => $value) {
        $value = str_replace('"', '\"', $value);
        $string .= "\n" . $word . ' = "' . $value . '"' . "\n";
    }
    $string = rtrim($string);
    return file_put_contents($config_file, $string);
}

function offline()
{
	echo "Site is offline!";
	die;
}

// Load menu modules admin
function admin_menu()
{
    foreach(glob(FCPATH.'modules/*/data/adminMenu.json') as $file)
    {
        $json = file_get_contents($file);
        if(!empty($json)) {
            $menu[] = json_decode($json);
        }
    }
    // Urutkan dari id paling kecil
    usort($menu, function($a, $b) { //Sort the array using a user defined function
        return $a->id > $b->id ? 1 : -1; //Compare the id 1: -1 artinya mulai dari yang terkecil, jika -1: 1 artinya dari yang terbesar
    });                                                                                                                                                                                                        
    // Create admin menu for module
    foreach($menu as $k => $v)
    {
        echo '<li class="nav-item">';
        if(!isset($v->submenu) && empty($v->submenu))
        {
            echo '<a href="' . site_url() . $v->url . '" class="nav-link">';
            echo '<i class="nav-icon ' . $v->icon . '"></i>';
            echo '<p>' . $v->name . '</p>';
        } else {
            echo '<a href="' . $v->url . '" class="nav-link">';
            echo '<i class="nav-icon ' . $v->icon . '"></i>';
            echo '<p>' . $v->name . '<i class="right fa fa-angle-left"></i></p>';
        }
        echo '</a>';
        if(isset($v->submenu) && !empty($v->submenu))
        {
            echo '<ul class="nav nav-treeview">';
            foreach($v->submenu as $d)
            {
                echo '<li class="nav-item"><a href="' . site_url() . $d->url . '" class="nav-link"><p>' . $d->name . '</p></a></li>';
            }
            
            echo '</ul>';
        }
        echo '</li>';
    }

}

// Copy folder and files
function copy_folders($oldfolder, $newfolder)
{
    if (is_dir($oldfolder))
    {
        $dir = opendir($oldfolder);
        if (!is_dir($newfolder))
        {
            mkdir($newfolder, 0775, true);
        }
        while (($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($oldfolder . '/' . $file))
                {
                    copy_folders($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
                else
                {
                    copy($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}

// Move folder and files
function move_folders($oldfolder, $newfolder)
{
    if (is_dir($oldfolder))
    {
        $dir = opendir($oldfolder);
        if (!is_dir($newfolder))
        {
            mkdir($newfolder, 0775, true);
        }
        while (($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($oldfolder . '/' . $file))
                {
                    copy_folders($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
                else
                {
                    copy($oldfolder . '/' . $file, $newfolder . '/' . $file);
                }
            }
        }
        closedir($dir);
        delete_folders($oldfolder);
    }
}

// Delete folder and files
function delete_folders($folder)
{
    if (false === file_exists($folder)) {
        return false;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            if (false === rmdir($fileinfo->getRealPath())) {
                return false;
            }
        } else {
            if (false === unlink($fileinfo->getRealPath())) {
                return false;
            }
        }
    }

    return rmdir($folder);
}

// HTML Minifier
function minify_html($input) {
    if(trim($input) === "") return $input;
    // Remove extra white-space(s) between HTML attribute(s)
    $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
        return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
    }, str_replace("\r", "", $input));
    // Minify inline CSS declaration(s)
    if(strpos($input, ' style=') !== false) {
        $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
            return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
        }, $input);
    }
    if(strpos($input, '</style>') !== false) {
      $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function($matches) {
        return '<style' . $matches[1] .'>'. minify_css($matches[2]) . '</style>';
      }, $input);
    }
    if(strpos($input, '</script>') !== false) {
      $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function($matches) {
        return '<script' . $matches[1] .'>'. minify_js($matches[2]) . '</script>';
      }, $input);
    }

    return preg_replace(
        array(
            // t = text
            // o = tag open
            // c = tag close
            // Keep important white-space(s) after self-closing HTML tag(s)
            '#<(img|input)(>| .*?>)#s',
            // Remove a line break and two or more white-space(s) between tag(s)
            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
            '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
            '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
            // Remove HTML comment(s) except IE comment(s)
            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
        ),
        array(
            '<$1$2</$1>',
            '$1$2$3',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            '$1',
            ""
        ),
    $input);
}

// CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
function minify_css($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
            // Remove unused white-space(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
            '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
            // Replace `:0 0 0 0` with `:0`
            '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
            // Replace `background-position:0` with `background-position:0 0`
            '#(background-position):0(?=[;\}])#si',
            // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
            '#(?<=[\s:,\-])0+\.(\d+)#s',
            // Minify string value
            '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
            '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
            // Minify HEX color code
            '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
            // Replace `(border|outline):none` with `(border|outline):0`
            '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
            // Remove empty selector(s)
            '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
        ),
        array(
            '$1',
            '$1$2$3$4$5$6$7',
            '$1',
            ':0',
            '$1:0 0',
            '.$1',
            '$1$3',
            '$1$2$4$5',
            '$1$2$3',
            '$1:0',
            '$1$2'
        ),
    $input);
}

// JavaScript Minifier
function minify_js($input) {
    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
    $input);
}

