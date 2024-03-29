<?php
/*
 +---------------------------------------------------------------------------+
 | File Manager 0.4                                                          |
 +---------------------------------------------------------------------------+
 | Author: James Wheaton                                                     |
 +---------------------------------------------------------------------------+
 |                                                                           |
 | This program is free software; you can redistribute it and/or             |
 | modify it under the terms of the GNU General Public License               |
 | as published by the Free Software Foundation; either version 2            |
 | of the License, or (at your option) any later version.                    |
 |                                                                           |
 | This program is distributed in the hope that it will be useful,           |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
 | GNU General Public License for more details.                              |
 |                                                                           |
 | You should have received a copy of the GNU General Public License         |
 | along with this program; if not, write to the Free Software Foundation,   |
 | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
 |                                                                           |
 +---------------------------------------------------------------------------+
*/

$time_start = microtime(1);
error_reporting(E_ALL);

/*********************************************************************
 * trying to show variable data types but got too lazy after a while *
 *********************************************************************/
/*  (string)    */  $self = basename($_SERVER['PHP_SELF']);
/*  (array)     */  $filelist = array();
/*  (array)     */  $dirlist = array();
/*  (int)       */  $color_number = 1;
/*  (array)     */  $bad = array('..', '../', '..\\', '//');
/*  (string)    */  $_GET['sort'] = (isset($_GET['sort'])) ? $_GET['sort'] : 'nameasc';
/*  (string)    */  $status = '';
/*  (bool)      */  $showhidden = false;

/************
 * Get $dir *
 ************/
if (isset($_GET['up']))
{
    $dir = dirname($_GET['dir']);
    $dir = empty($dir) ? '.' : str_replace($bad, '', $dir);
}
else
{
    $dir = empty($_GET['dir']) ? '.' : str_replace($bad, '', $_GET['dir']);
}
if ($dir{0} === '/')
{
    $dir = substr($dir, 1);
}
$readable_dir = ($dir === '.') ? substr(($t = strrchr(getcwd(), '/')) !== false ? $t : '', 1) : substr(($t = strrchr($dir, '/')) !== false ? $t : '', 1);

/*********************
 * Web page template *
 *********************/
function template($title, $style, $body, $microtime)
{
    global $time_start, $time_end;
    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>' . $title . '</title>
        <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
        <meta http-equiv="Content-Language" content="en-us" />
        <link rel="stylesheet" type="text/css" href="' . $style . '" media="screen" />
        <script type="text/javascript">
        //<![CDATA[
        //show OR hide funtion depends on if element is shown or hidden
        function show(id) {
        if (document.getElementById) { // DOM3 = IE5, NS6
                if (document.getElementById(id).style.display == "none"){
                    document.getElementById(id).style.display = \'block\';
                } else {
                    document.getElementById(id).style.display = \'none\';
                }
            } else { 
                if (document.layers) {
                    if (document.id.display == "none"){
                        document.id.display = \'block\';
                    } else {
                        document.id.display = \'none\';
                    }
                } else {
                    if (document.all.id.style.visibility == "none"){
                        document.all.id.style.display = \'block\';
                    } else {
                        document.all.id.style.display = \'none\';
                    }
                }
            }
        }
        //]]>
        </script>
    </head>
    <body onload="show(\'functions\')"> 
    <h1>uranther.com</h1>
    <div id="wrapper">' . $body;
    if ($microtime === true)
    {
        $html .= "\n\t" . '</div>' . "\n\t" . '<div id="footer">' . 'Page generated in ' .
round(($time_end - $time_start), 6) . ' seconds.</div>' . "\n\t" . '</body>' . "\n" . '</html>';
    }

    return $html;
}

/***************
 * Upload file *
 ***************
if (isset($_POST['submitupload']))
{
    if (@move_uploaded_file($_FILES['uploadfile']['tmp_name'],
        $dir . '/' . basename($_FILES['uploadfile']['name'])))
    {
        $status = ' &minus; upload success';
    }
    else
    {
        $status = ' &minus; upload error: check permissions';
    }
}*/

/*************
 * Move file *
 *************
else if (isset($_POST['submitmove']))
{
    if (isset($_POST['movesrcfile']) and !empty($_POST['movesrcfile']))
    {
        if (is_file($_POST['movesrcfile']) or is_dir($_POST['movesrcfile']))
        {
            if (isset($_POST['movedestfile']) and !empty($_POST['movedestfile']))
            {
                if(!@rename($_POST['movesrcfile'], $_POST['movedestfile']))
                {
                    $status = ' &minus; move error: check permissions';
                }
                else
                {
                    $status = ' &minus; move success';
                }
            }
            else
            {
                $status = ' &minus; move error: need destination';
            }
        }
        else
        {
            $status = ' &minus; move error: file/directory does not exist';
        }
    }
    else
    {
        $status = ' &minus; move error: need source';
    }
}*/

/***************
 * Delete file *
 ***************
else if (isset($_POST['submitdel']))
{
    if (isset($_POST['delfile']) and !empty($_POST['delfile']))
    {
        if (is_file($_POST['delfile']))
        {
            if (@unlink($_POST['delfile']))
            {
                $status = ' &minus; delete success';
            }
            else
            {
                $status = ' &minus; delete error: check permissions';
            }
        }
        else if (is_dir($_POST['delfile']))
        {
            $status = ' &minus; delete error: no support for directories';
        }
        else
        {
            $status = ' &minus; delete error: file does not exist';
        }
    }
    else
    {
        $status = ' &minus; delete error: need file';
    }
}*/

/**************
 * Chmod file *
 **************
else if (isset($_POST['submitchmod']))
{
    if (isset($_POST['chmodsrcfile']) and !empty($_POST['chmodsrcfile']) and
        isset($_POST['chmodfile']) and !empty($_POST['chmodfile']))
    {
        if (is_file($_POST['chmodsrcfile']))
        {
            if (is_numeric($_POST['chmodfile']))
            {
                if (@chmod($_POST['chmodsrcfile'], octdec($_POST['chmodfile'])))
                {
                    $status = ' &minus; chmod success ' . octdec($_POST['chmodfile']);
                }
                else
                {
                    $status = ' &minus; chmod error: check permissions or chmod value';
                }
            }
            else
            {
                $status = ' &minus; chmod error: please enter a numeric chmod value';
            }
        }
        else
        {
            $status = ' &minus; chmod error: file does not exist';
        }
    }
    else
    {
        $status = ' &minus; chmod error: need file or chmod value';
    }
}*/

/***************
 * View source *
 ***************/
function download($src)
{
    global $bad, $self;
    $src = str_replace($bad, '', $src);
    $srcdir = dirname($src);

    if (file_exists($src) and is_file($src) and is_readable($src))
    {
        if (is_file_ext($src, array('.php', '.php3')))
        {
            header('Content-type: application/force-download');
            header('Content-length: ' . filesize($src));
            return file_get_contents($src);
        }
        else
        {
            $body = "\n\t\t" . '<div class="notice">' .
                    "\n\t\t\t" . '<p>Can only download a PHP file.</p>' .
                "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $srcdir . '">Return to index</a>' .
                    "\n\t\t" . '</div>';
            return template('Download error: ' . $src,
                $self . '?style',
                $body, false);

        }
    }
    else
    {
            $body = "\n\t\t" . '<div class="notice">' .
                    "\n\t\t\t" . '<p>No file.</p>' .
                "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $srcdir . '">Return to index</a>' .
                    "\n\t\t" . '</div>';
            return template('Download error: ' . $src,
                $self . '?style',
                $body, false);

    }
}
function sourceview($src)
{
    global $bad, $self;
    $src = str_replace($bad, '', $src);
    $srcdir = dirname($src);
    
    if (empty($_GET['src']))
    {
        $body = "\n\t\t" . '<div class="notice">' .
                "\n\t\t\t" . '<p>Need file.</p>' .
            "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $srcdir . '">Return to index</a>' .
            "\n\t\t" . '</div>';
        $title = 'Source view error: ' . $src;
        $style = $self . '?style';
    }
    else
    {
        if (file_exists($src) and is_file($src) and is_readable($src))
        {
            if (is_file_ext($src, array('.php', '.php3')))
            {
                $body = '<h3><a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?dir=' . dirname($src) . '">Return</a>&nbsp;&nbsp;&nbsp;<a href="http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/' . $src . '">View this file</a>&nbsp;&nbsp;&nbsp;<a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?download=' . $src . '">Download this file</a></h3>' . 
                    highlight_file($src, true);
                $title = 'Source view: ' . $src;
                $style = $self . '?sourcecss';
            }
            else
            {
                $body = "\n\t\t" . '<div class="notice">' .
                        "\n\t\t\t" . '<p>Can only view source of a PHP file.</p>';
                        
                if (strstr($_SERVER['HTTP_REFERER'], 'submitsearch=Search'))
                {
                    $body .= "\n\t\t\t" . '<a class="dir" href="' . $_SERVER['HTTP_REFERER'] . '">Return to index</a>' .
                            "\n\t\t" . '</div>';
                }
                else
                {
                    $body .= "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $srcdir . '">Return to index</a>' .
                            "\n\t\t" . '</div>';
                }
                $title = 'Source view error: ' . $src;
                $style = $self . '?style';
            }
        }
        else 
        {
            $body = "\n\t\t" . '<div class="notice">' .
                    "\n\t\t\t" . '<p>File does not exist or is not readable.</p>' .
                "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $srcdir . '">Return to index</a>' .
                "\n\t\t" . '</div>';
            $title = 'Source view error: ' . $src;
            $style = $self . '?style';
        }

    }
    return template($title, $style, $body, false);
}

/*********
 * Icons *
 *********/
// base64_decode() rapes the shit out of the server
// Saved in icons.txt for reference.

/****************
 * Image viewer *
 ****************/
function view_image($imagefile)
{
    global $self, $bad;
    
    $imagefile = str_replace($bad, '', $imagefile);
    $imagedir = dirname($imagefile);
    $filename = basename($imagefile);
    if (file_exists($imagefile) and is_file($imagefile) and is_readable($imagefile))
    {
        list($width, $height, $type, $attr) = @getimagesize($imagefile);
        switch ($type)
        {
            case 1:
                $type = 'GIF';
                break;
            case 2:
                $type = 'JPEG';
                break;
            case 3:
                $type = 'PNG';
                break;
            default:
                $type = 'Unsupported'; // This had better not happen
        }

        if ($type === 'Unsupported')
        {
            $body = "\n\t\t" . '<div class="notice">' .
                    "\n\t\t\t" . '<p>Image file is not supported</p>' .
                "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $imagedir . '">Return to index</a>' .
                    "\n\t\t" . '</div>';
            $html = template('Image viewer error: ' . $imagedir,
                $self . '?style&amp;imageview=' . $_GET['imageview'],
                $body, false);
            $file_info = array(NULL, NULL, NULL, NULL, $html);
        }
        else
        {   
            $minwidth = $width < 250 ? 250 : $width;
            $body  = "\n\t\t"   . '<div class="image">' .
                     "\n\t\t\t" . '<img alt="' . $imagefile . '" src="' . $imagefile . '" ' . $attr . ' />' . '<hr />' .
                 "\n\t\t\t" . 'Filename: ' . $filename . '<br />' .
                 "\n\t\t\t" . 'Dimensions: ' . $width . ' x ' . $height . ' px<br />' .
                 "\n\t\t\t" . 'Filetype: ' . $type . '<br />' .
                 "\n\t\t\t" . 'Filesize: ' . fsize_unit_convert(filesize($imagefile)) . '<br />';
            if (!empty($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], 'submitsearch=Search'))
            {
                $body .=
                    "\n\t\t\t" . '<a style="margin-left: ' . ($minwidth/2 - 50) . 'px" class="dir" href="' . $_SERVER['HTTP_REFERER'] . '">Return to index</a>';
            }
            else
            {
                $body .=
                    "\n\t\t\t" . '<a style="margin-left: ' . ($minwidth/2 - 50) . 'px" class="dir" href="' . $self . '?dir=' . $imagedir . '">Return to index</a>';
            }
            
            $body .= "\n\t\t"   . '</div>';

            $html = template('Image viewer: ' . $imagedir,
                $self . '?style&amp;imageview=' . $_GET['imageview'],
                $body, false);
            $file_info = array($width, $height, $type, $attr, $html);
        }
    }
    else
    {
        $body = "\n\t\t" . '<div class="notice">' .
                "\n\t\t\t" . '<p>Image file ' . $imagefile . ' does not exist</p>' .
            "\n\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $imagedir . '">Return to index</a>' .
                "\n\t\t" . '</div>';
        $html = template('Image viewer error: ' . $imagedir,
            $self . '?style&amp;imageview=' . $_GET['imageview'],
            $body, false);
        $file_info = array(NULL, NULL, NULL, NULL, $html);
    }
    
    return $file_info;
}

/*****************************************************
 * Search for a case-insensitive string in filenames *
 *****************************************************/
function search($string, $dh)
{
    $count = 0;
    
    while (($file = readdir($dh)) !== false)
    {
        if (stristr($file, $string))
        {
            $filelist[$count] = $file;
            $count++;
        }
    }
    closedir($dh);
    
    if (empty($filelist))
    {
        $filelist[0] = '.';
        $filelist[1] = '..';
    }
    
    return $filelist;
}

/*****************************************
 * strpos() needs to supports arrays >:O *
 *****************************************/
function is_file_ext($haystack, $needles)
{
    for ($i = 0; $i < count($needles); $i++)
    {
        $bool = strstr($haystack, $needles[$i]);
        if($bool !== false)
        {
            return true;
        }
    }
    return false;
}

/**********************************
 * Multi-purpose sorting function *
 **********************************/
function multi_sort($array, $order, $function)
{
    if (empty($array))
    {
        return;
    }
    
    // $order must equal 'asc' or 'desc'
    assert($order === 'asc' or $order === 'desc');
    
    for ($i = 0; $i < count($array); $i++)
    {
        $sorted[$i][0] = $function($array[$i]);
        $sorted[$i][1] = $array[$i];
    }
    
    if ($order === 'asc')
    {
        sort($sorted);
    }
    else if ($order === 'desc')
    {
        rsort($sorted);
    }

    assert(count($sorted) === count($array));
    
    for ($i = 0; $i < count($array); $i++)
    {
        $array_key = array_search($sorted[$i][1], $array);
        $new_array[$i] = $array[$array_key];
    }
    
    return $new_array;  
}

/****************************
 * File size unit converter *
 ****************************/
function fsize_unit_convert($bytes)
{
    $units = array('b', 'kb', 'mb', 'gb');
    $converted = $bytes . ' ' . $units[0];
    for ($i = 0; $i < count($units); $i++)
    {
        if (($bytes/pow(1024, $i)) >= 1)
        {$converted = round($bytes/pow(1024, $i), 2) . ' ' . $units[$i];}
    }
    return $converted;
}

/**********************
 * Get file extension *
 **********************/
function get_ext($filename)
{
    $ext = substr(
        ($t = strrchr($filename,'.')) !== false 
        ? $t
        : 'xnone', /* x is gonna be substr'd */
        1);
    return $ext;
}

/**********************
 * Format fileperms() *
 **********************/
function format_perms($perms)
{
    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }
    
    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
        (($perms & 0x0800) ? 's' : 'x' ) :
        (($perms & 0x0800) ? 'S' : '-'));
    
    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
        (($perms & 0x0400) ? 's' : 'x' ) :
        (($perms & 0x0400) ? 'S' : '-'));
    
    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
        (($perms & 0x0200) ? 't' : 'x' ) :
        (($perms & 0x0200) ? 'T' : '-'));
    
    return $info;
}

/************
 * CSS FILE *
 ************/
function sourceview_css()
{
    header('Content-type: text/css');
    $css = <<<CSS
html, body {
    padding: 0; margin: 0;
    height: 100%;
    width: 100%;
}
body {
    font-size: .9em;
    background: #fff;
    font-family: "Bitstream Vera Sans Mono", "Courier New", monospace;
    overflow: auto;
    white-space: pre;
}
h3 {
    padding: 0;
    margin: 25px;
}
a:link {
    color: #00bfff;
    font-weight: bold;
    text-decoration: none;
}
a:visited {
    color: #0099cd;
    font-weight: bold;
    text-decoration: none;
}
a:hover {
    color: #00b1ee;
    text-decoration: underline;
}
CSS;
    header('Content-length: ' . strlen($css));
    return $css;
}
if (isset($_GET['sourcecss']))
{
    echo sourceview_css(); die();
}

function css_style()
{
    header('Content-type: text/css');
    $css = <<<CSS
body {
    color: #333;
    background: #fff;
    padding: 25px;
    font: .9em Arial, Helvetica, "Bitstream Vera Sans", Tahoma, sans-serif;
    text-align: center;
}
#wrapper {
    margin: 0 auto;
    text-align: left;
}
#footer {
    font-size: 0.7em;
    margin: 0 auto;
    padding: 15px;
    clear: both;
}
table {
    border-collapse: collapse;
    background: #fff;
    margin-right: 10px;
    width: 100%;
}
td {
    border: 1px solid #fff;
    padding: 5px;
}
.tr1 {
    font-size: .9em;
    background: #fdfdfd;
    border-bottom: 1px solid #eee;
}
.tr2 {
    font-size: .9em;
    background: #f6f6f6;
    border-bottom: 1px solid #eee;
}
#functions {
    padding: 20px;
    background: #f6f6f6;
}
#func_left {
    width: 50%;
    float: left;
}
#func_right {
    width: 50%;
    margin-left: 50%;
}
input {
    margin-bottom: 2px;
}
label {
    font-size: 1.2em;
}
h2 a:link, .head:link {
    color: #333;
    text-decoration: none;
}
h2 a:visited, .head:visited {
    color: #333;
    text-decoration: none;
}
h2 a:hover, .head:hover {
    color: #666;
    text-decoration: underline;
}
#toggle:link, .file:link {
    color: #1d8fff;
    text-decoration: none;
}
#toggle:visited, .file:visited {
    color: #1773cd;
    text-decoration: none;
}
#toggle:hover, .file:hover {
    color: #1b86ee;
    text-decoration: underline;
}
.dir:link {
    color: #00bfff;
    font-weight: bold;
    text-decoration: none;
}
.dir:visited {
    color: #0099cd;
    font-weight: bold;
    text-decoration: none;
}
.dir:hover {
    color: #00b1ee;
    text-decoration: underline;
}
.notice {
    background: #f6f6f6;
    font-size: 0.9em;
    text-align: center;
    margin: 100px auto;
    width: 250px;
    padding: 100px;
}
.bullet {
    white-space: pre;
    text-decoration: none;
    font: bold 1.1em "Bitstream Vera Sans Mono", "Courier New", monospace;
}
CSS;

if (isset($_GET['imageview']))
{
    $imageview = view_image($_GET['imageview']);
    $width = $imageview[0] < 250 ? 250 : $imageview[0];
    $css .= '
.image {
    background: #f6f6f6;
    font-size: 0.9em;
    margin: 0 auto;
    width: ' . $width . 'px;
    padding: 25px;
}
.image img {
    display: block;
    margin: 0 auto;
}
.image hr {
    width: ' . ($width - 50) . 'px;
    border: 0 none;
    height: 1px;
    background: #666;
    margin: 15px auto;
}';
}
    header('Content-length: ' . strlen($css));
    return $css;
}
if(isset($_GET['style']))
{
    echo css_style(); die();
}

/***************
 * View image? *
 ***************/
if (isset($_GET['imageview']))
{
    $imageview = view_image($_GET['imageview']);
    echo $imageview[4];
    die();
}

/***************
 * Source view *
 ***************/
if (isset($_GET['src']))
{
    echo sourceview($_GET['src']);
    die();
}

/*****************
 * Download text *
 *****************/
if (isset($_GET['download']))
{
    echo download($_GET['download']);
    die();
}

/**********************************************
 * Fill $filelist and $dirlist and print HTML *
 **********************************************/
if (is_dir($dir)) 
{
    if ($dh = @chdir($dir))
    {
        if ($dh = opendir('.'))
        {
            if (isset($_GET['submitsearch']))
            {
                if (isset($_GET['searchfile']) && !empty($_GET['searchfile']))
                {
                    $filelist = search($_GET['searchfile'], $dh);
                    $count = count($filelist);
                    
                    if ($filelist[0] === '.' && $filelist[1] === '..')
                    {
                        $status = ' &minus; search: no files found with search string: ' . $_GET['searchfile'];
                    }
                }
                else
                {
                    $status = ' &minus; search error: no search string entered';
                }
            }
            else
            {
                $count = 0;
                if (!$showhidden)
                {
                    $filelist[$count++] = '.';
                    $filelist[$count++] = '..';
                }
                while (($file = readdir($dh)) !== false)
                {
                    if ($showhidden)
                    {
                        $filelist[$count] = $file;
                        $count++;
                    }
                    else
                    {
                        if ($file{0} !== '.')
                        {
                            $filelist[$count] = $file;
                            $count++;
                        }
                    }
                }
                closedir($dh);
            }
        }

        /***********************************
         * Separate directories from files *
         ***********************************/
        for ($i = 0, $h = 0; $i < $count; $i++, $h++)
        {
            if (is_dir($filelist[$i]))
            {
                $dirlist[$h] = $filelist[$i];
                unset($filelist[$i]);
            }
        }

        //-----> the $count of $filelist has changed, now using count($filelist)


        /***************
         * Alphabetize *
         ***************/
        natcasesort($filelist);         // maintains key/value associations
        $filelist = array_values($filelist);    // I don't care about associations!
        natcasesort($dirlist);
        $dirlist = array_values($dirlist);

        /**********************
         * Sorting algorithms *
         **********************/
        switch ($_GET['sort'])
        {
            case 'sizeasc':
                $filelist = multi_sort($filelist, 'asc', 'filesize');
                break;
            case 'sizedesc':
                $filelist = multi_sort($filelist, 'desc', 'filesize');
                break;
            case 'dateasc':
                $filelist = multi_sort($filelist, 'asc', 'filemtime');
                $dirlist = multi_sort($dirlist, 'asc', 'filemtime');
                break;
            case 'datedesc':
                $filelist = multi_sort($filelist, 'desc', 'filemtime');
                $dirlist = multi_sort($dirlist, 'desc', 'filemtime');
                break;
            case 'typeasc':
                $filelist = multi_sort($filelist, 'asc', 'get_ext');
                break;
            case 'typedesc':
                $filelist = multi_sort($filelist, 'desc', 'get_ext');
                break;
            default:
                if (empty($_GET['sort']) or 
                    $_GET['sort'] === 'nameasc' or
                    $_GET['sort'] === 'namedesc')
                {
                    /* do nothing */
                }
                else
                {
                    $status = ' &minus; invalid sorting algorithm: ' . $_GET['sort'];
                }
        }
    
        /*****************************
         * User-intervened functions *
         *****************************/
        // Javascript hide
        /*
        $body = "\n\t\t" . '<a href="#" id="toggle" onclick="show(\'functions\');">Toggle functions</a>' .
            "\n\t\t" . '<div id="functions">' .
            "\n\t\t\t" . '<div id="func_left">' .
            "\n\t\t\t" . '<form method="post" action="' . $self . '" enctype="multipart/form-data">' . "\n\t\t\t\t" . '<div>' . "\n\t\t\t\t\t" . '<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />' . "\n\t\t\t\t\t" . '<label >Upload file:</label><br />' . "\n\t\t\t\t\t" . '<input type="file" name="uploadfile" size="20" /><br />' . "\n\t\t\t\t\t" . '<input type="submit" value="Upload file" name="submitupload" />' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t" . '</form>' .

            "\n\t\t\t" . '<form method="post" action="' . $self . '">' . "\n\t\t\t\t" . '<div>' . "\n\t\t\t\t\t" . '<label >Move file:</label><br />' . "\n\t\t\t\t\t" . '<input type="text" name="movesrcfile" size="28" value="' . $dir . '/" /><br />' . "\n\t\t\t\t\t" . '<input type="text" name="movedestfile" size="28" value="' . $dir . '/" /><br />' . "\n\t\t\t\t\t" . '<input type="submit" value="Move file" name="submitmove" />' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t" . '</form>' .
            "\n\t\t\t" . '</div>' .
            
            "\n\t\t\t" . '<div id="func_right">' .
            "\n\t\t\t" . '<form method="get" action="' . $self . '">' . "\n\t\t\t\t" . '<div>' . "\n\t\t\t\t\t" . '<label >Search for file:</label><br />' . "\n\t\t\t\t\t" . '<input type="text" name="searchfile" size="28"  /><br />' . "\n\t\t\t\t\t" . '<input type="submit" value="Search" name="submitsearch" />' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t" . '</form>' .
            
            "\n\t\t\t" . '<form method="post" action="' . $self . '">' . "\n\t\t\t\t" . '<div>' . "\n\t\t\t\t\t" . '<label >Delete file:</label><br />' . "\n\t\t\t\t\t" . '<input type="text" name="delfile" size="28" value="' . $dir . '/" /><br />' . "\n\t\t\t\t\t" . '<input type="submit" value="Delete file" name="submitdel" />' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t" . '</form>' .

            "\n\t\t\t" . '<form method="post" action="' . $self . '">' . "\n\t\t\t\t" . '<div>' . "\n\t\t\t\t\t" . '<label >Chmod file:</label><br />' . "\n\t\t\t\t\t" . '<input type="text" name="chmodsrcfile" size="23" value="' . $dir . '/" /> <input type="text" name="chmodfile" size="3" value="0644" /><br />' . "\n\t\t\t\t\t" . '<input type="submit" value="Chmod file" name="submitchmod" />' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t" . '</form>' .
            "\n\t\t\t" . '</div>' .

            "\n\t\t\t" . '<div style="clear: left;"></div>' .
            "\n\t\t"    . '</div>';
        */
        /**********************************
         * Start directory contents table *
         **********************************/
        $body = "\n\t\t"    . '<h2><a href="' . $self . '?dir=' . $dir . '">' . $readable_dir . $status . '</a></h2>' . 
             "\n\t\t"       . '<table>' .
             "\n\t\t\t"     . '<tr>';

        // SORT BY FILENAME -- LINK
        if ($_GET['sort'] === 'nameasc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=namedesc">Filename</a></td>';
        }
        else if ($_GET['sort'] === 'namedesc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=nameasc">Filename</a></td>';
        }
        else // Default to ascending order (it makes sense)
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=nameasc">Filename</a></td>';
        }


        // SORT BY FILE EXTENSION -- LINK
        if ($_GET['sort'] === 'typeasc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=typedesc">Filetype</a></td>';
        }
        else if ($_GET['sort'] === 'typedesc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=typeasc">Filetype</a></td>';
        }
        else // Default to ascending order (it makes sense)
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=typeasc">Filetype</a></td>';
        }


        // SORT BY FILESIZE -- LINK
        if ($_GET['sort'] === 'sizeasc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=sizedesc">Filesize</a></td>';
        }
        else if ($_GET['sort'] === 'sizedesc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=sizeasc">Filesize</a></td>';           
        }
        else // Default to ascending order (it makes sense)
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=sizeasc">Filesize</a></td>';
        }

        // SORT BY DATE MODIFIED -- LINK
        if ($_GET['sort'] === 'dateasc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=datedesc">Last Modified</a></td>';
        }
        else if ($_GET['sort'] === 'datedesc')
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=dateasc">Last Modified</a></td>';
        }
        else // Default to ascending order (it makes sense)
        {
            $body .= "\n\t\t\t\t"   . '<td><a class="head" href="' . $self . '?dir=' . $dir . '&amp;sort=dateasc">Last Modified</a></td>';
        }
        
        $body .= "\n\t\t\t\t"   . '<td>File Permissions</td>' . // sorting by file permissions is gay
             "\n\t\t\t"     . '</tr>';


        /************************************
         * Print directory contents in HTML *
         ************************************/

        // Directories first
        for ($h = 0; $h < count($dirlist); $h++)
        {
            /********************
             * Sort by filename *
             ********************/
            if ($h === 0 and $_GET['sort'] !== 'nameasc')
            {
                if ($_GET['sort'] === 'nameasc' or $_GET['sort'] === 'namedesc')
                {
                    $z = count($dirlist) - 1;
                }
                else
                {
                    $z = $h;
                }
            }
            else
            {
                if ($_GET['sort'] === 'namedesc')
                {   $z--;   }
                else { $z = $h; }
            }
            
        
            $color_number = (!isset($color_number) or $color_number === 2) ?
                    $color_number = 1 : $color_number = 2;

            $body .= "\n\t\t\t" . '<tr class="tr' . $color_number . '">';
            
            if (is_dir($dirlist[$z]))
            {
                $fileperms = fileperms($dirlist[$z]);
                $body .= "\n\t\t\t\t"   . '<td>';
                     
                switch ($dirlist[$z])
                {
                    case '.':
                        $body .= "\n\t\t\t\t\t" . '<a class="bullet" title="' . $readable_dir . '">&nbsp;</a>' .
                                 "\n\t\t\t\t\t" . '<a class="dir" href="' . $self . '">.</a>';
                        break;
                    case '..':
                        $body .= "\n\t\t\t\t\t" . '<a class="bullet" title="Go up a directory">&uarr;</a>' .
                                 "\n\t\t\t\t\t" . '<a class="dir" href="' . $self . '?dir=' . $dir . '&amp;up">..</a>';
                        break;
                    default:
                        $body .= "\n\t\t\t\t\t" . '<a class="bullet" title="directory">&bull;</a>' .
                             "\n\t\t\t\t\t"     . '<a class="dir" href="' . $self . '?dir=' . $dir . '/' . $dirlist[$z] . '">' . $dirlist[$z] . '</a>';
                }
                    
                    $body .= "\n\t\t\t\t"   . '</td>' .
                     "\n\t\t\t\t"   . '<td></td>' .
                     "\n\t\t\t\t"   . '<td></td>' .
                     "\n\t\t\t\t"   . '<td>' . date("m/d/y H:i:s", filemtime($dirlist[$z])) . '</td>' .
                     "\n\t\t\t\t"   . '<td>' . format_perms($fileperms) . ' (' . substr(sprintf('%o', $fileperms), -4) . ')</td>';
            }

            $body .= "\n\t\t\t" . '</tr>';
        }
        
        // Files next
        for ($i = 0; $i < count($filelist); $i++)
        {
            /********************
             * Sort by filename *
             ********************/
            if ($i === 0 and $_GET['sort'] !== 'nameasc')
            {
                if ($_GET['sort'] === 'nameasc' or $_GET['sort'] === 'namedesc')
                {
                    $y = count($filelist) - 1;
                }
                else
                {
                    $y = $i;
                }
            }
            else
            {
                if ($_GET['sort'] === 'namedesc')
                {   $y--;   }
                else { $y = $i; }
            }

        
            $color_number = (!isset($color_number) or $color_number === 2) ?
                    $color_number = 1 : $color_number = 2;
            
            $body .= "\n\t\t\t" . '<tr class="tr' . $color_number . '">';
            
            if (is_file($filelist[$y]))
            {
                $fileperms = fileperms($filelist[$y]);
                $body .= "\n\t\t\t\t"   . '<td>' .
                         "\n\t\t\t\t\t" . '<a class="bullet" title="file">&nbsp;</a>';
                if (!is_file_ext($filelist[$y], array('.gif', '.jpg', '.jpeg', '.jpe', '.png')))
                {
                    if (is_file_ext($filelist[$y], array('.php', '.php3')))
                    {
                        $body .= "\n\t\t\t\t\t" . '<a class="file" href="' . $self . '?src=' . $dir . '/' . $filelist[$y] . '">' . $filelist[$y] . '</a>';
                    }
                    else
                    {
                    $body .= "\n\t\t\t\t\t" . '<a class="file" href="' . $dir . '/' . $filelist[$y] . '">' . $filelist[$y] . '</a>';    
                    }
                }
                else
                {
                    $body .= "\n\t\t\t\t\t" . '<a class="file" href="' . $self . '?imageview=' . $dir . '/' . $filelist[$y] . '">' . $filelist[$y] . '</a>';
                }
                $body .= "\n\t\t\t\t"   . '</td>' .
                     "\n\t\t\t\t"   . '<td>' .  get_ext($filelist[$y]) . '</td>' .
                     "\n\t\t\t\t"   . '<td>' . fsize_unit_convert(filesize($filelist[$y])) . '</td>' .
                     "\n\t\t\t\t"   . '<td>' . date("m/d/y H:i:s", filemtime($filelist[$y])) . '</td>' .
                     "\n\t\t\t\t"   . '<td>' . format_perms($fileperms) . ' (' . substr(sprintf('%o', $fileperms), -4) . ')</td>'; 
            }

        
            $body .= "\n\t\t\t</tr>";
        }

        $body .= "\n\t\t</table>";

        $title = 'File Manager: ' . $readable_dir;
    }
    else
    {
        $body = "\n\t\t". '<div class="notice">' .
             "\n\t\t\t" . '<p>Cannot change directory; check permissions.<p>' .
             "\n\t\t\t" . '<a class="dir" href="' . $self . '">Return to index</a>' .
             "\n\t\t"   . '</div>';
        $title = 'File Manager error: ' . $readable_dir;
    }
}
else
{
    $body = "\n\t\t". '<div class="notice">' .
         "\n\t\t\t" . '<p>That\'s not a directory!</p>' .
         "\n\t\t\t" . '<a class="dir" href="' . $self . '">Return to index</a>' .
         "\n\t\t"   . '</div>';
    $title = 'File Manager error: ' . $readable_dir;
}

        $time_end = microtime(1); 

echo template($title, $self . '?style', $body, true);

?>
