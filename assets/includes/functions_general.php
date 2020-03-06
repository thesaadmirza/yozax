<?php
// @author Saad Mirza https://saadmirza.net
function YX_LoadPage($page_url = '', $data = array(), $set_lang = true)
{
    global $yx, $lang, $config;
    $page         = './themes/' . $config['theme'] . '/layout/' . $page_url . '.html';
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();

    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function ($m) use ($lang) {
            return (isset($lang[$m[1]])) ? htmlentities($lang[$m[1]]) : ' *** ';
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA' && is_array($replace)) {
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function ($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                $object_to_replace = "{{" . $key . "}}";
                $page_content      = str_replace($object_to_replace, $replace, $page_content);
            }
        }
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", YX_Link("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function ($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);

    return $page_content;
}
function YX_LoadAdminPage($page_url = '', $data = array(), $set_lang = true)
{
    global $yx, $lang, $config, $db;
    $page         = './admin-panel/pages/' . $page_url . '.html';
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function ($m) use ($lang) {
            return (isset($lang[$m[1]])) ? $lang[$m[1]] : ' *** ';
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA' && is_array($replace)) {
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function ($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                $object_to_replace = "{{" . $key . "}}";
                $page_content      = str_replace($object_to_replace, $replace, $page_content);
            }
        }
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", YX_Link("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function ($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);

    return $page_content;
}
function YX_LoadAdminLinkSettings($link = '')
{
    global $site_url;
    return $site_url . '/admin-cp/' . $link;
}
function YX_LoadAdminLink($link = '')
{
    global $site_url;
    return $site_url . '/admin-cdn/' . $link;
}
function br2nl($st)
{
    $breaks   = array(
        "\r\n",
        "\r",
        "\n"
    );
    $st       = str_replace($breaks, "", $st);
    $st_no_lb = preg_replace("/\r|\n/", "", $st);
    return preg_replace('/<br(\s+)?\/?>/i', "\r", $st_no_lb);
}
function YX_SlugPost($string)
{
    $slug = url_slug($string, array(
        'delimiter' => '-',
        'limit' => 100,
        'lowercase' => true,
        'replacements' => array(
            '/\b(an)\b/i' => 'a',
            '/\b(example)\b/i' => 'Test'
        )
    ));
    return $slug;
}
function url_slug($str, $options = array())
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str      = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => false
    );
    // Merge options
    $options  = array_merge($defaults, $options);
    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z'
    );
    // Make custom replacements
    $str      = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }
    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);
    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}
function YX_IsLogged()
{
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = YX_GetUserFromSessionID($_SESSION['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    } else if (!empty($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
        $id = YX_GetUserFromSessionID($_COOKIE['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    } else {
        return false;
    }
}
function YX_Redirect($url)
{
    return header("Location: {$url}");
}
function YX_Link($string)
{
    global $site_url;
    return $site_url . '/' . $string;
}
function YX_Sql_Result($res, $row = 0, $col = 0)
{
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
        mysqli_data_seek($res, $row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return false;
}
function YX_Secure($string, $censored_words = 1)
{
    global $sqlConnect;
    $string = trim($string);
    $string = mysqli_real_escape_string($sqlConnect, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br>', $string);
    $string = str_replace('\\r', '<br>', $string);
    $string = str_replace('\\n\\n', '<br>', $string);
    $string = str_replace('\\n', '<br>', $string);
    $string = str_replace('\\n', '<br>', $string);
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#', $string);
    if ($censored_words == 1) {
        global $config;
        $censored_words = @explode(",", $config['censored_words']);
        foreach ($censored_words as $censored_word) {
            $censored_word = trim($censored_word);
            $string        = str_replace($censored_word, '****', $string);
        }
    }
    return $string;
}
function YX_Decode($string)
{
    return htmlspecialchars_decode($string);
}
function YX_GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false)
{
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}
$can = 0;
function YX_Resize_Crop_Image($max_width, $max_height, $source_file, $dst_dir, $quality = 80)
{
    $imgsize = @getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            break;
        default:
            return false;
            break;
    }
    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
    $src_img    = $image_create($source_file);
    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    @imagejpeg($dst_img, $dst_dir, $quality);
    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
}
function YX_Time_Elapsed_String($ptime)
{
    global $yx;
    $etime = time() - $ptime;
    if ($etime < 1) {
        return '0 seconds';
    }
    $a        = array(
        365 * 24 * 60 * 60 => $yx['lang']['year'],
        30 * 24 * 60 * 60 => $yx['lang']['month'],
        24 * 60 * 60 => $yx['lang']['day'],
        60 * 60 => $yx['lang']['hour'],
        60 => $yx['lang']['minute'],
        1 => $yx['lang']['second']
    );
    $a_plural = array(
        $yx['lang']['year'] => $yx['lang']['years'],
        $yx['lang']['month'] => $yx['lang']['months'],
        $yx['lang']['day'] => $yx['lang']['days'],
        $yx['lang']['hour'] => $yx['lang']['hours'],
        $yx['lang']['minute'] => $yx['lang']['minutes'],
        $yx['lang']['second'] => $yx['lang']['seconds']
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            if ($yx['language_type'] == 'rtl') {
                $time_ago = $yx['lang']['time_ago'] . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            } else {
                $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . $yx['lang']['time_ago'];
            }
            return $time_ago;
        }
    }
}
function YX_FolderSize($dir)
{
    $count_size = 0;
    $count      = 0;
    $dir_array  = scandir($dir);
    foreach ($dir_array as $key => $filename) {
        if ($filename != ".." && $filename != "." && $filename != ".htaccess") {
            if (is_dir($dir . "/" . $filename)) {
                $new_foldersize = YX_FolderSize($dir . "/" . $filename);
                $count_size     = $count_size + $new_foldersize;
            } else if (is_file($dir . "/" . $filename)) {
                $count_size = $count_size + filesize($dir . "/" . $filename);
                $count++;
            }
        }
    }
    return $count_size;
}
function YX_SizeFormat($bytes)
{
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;
    if (($bytes >= 0) && ($bytes < $kb)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kb) && ($bytes < $mb)) {
        return ceil($bytes / $kb) . ' KB';
    } elseif (($bytes >= $mb) && ($bytes < $gb)) {
        return ceil($bytes / $mb) . ' MB';
    } elseif (($bytes >= $gb) && ($bytes < $tb)) {
        return ceil($bytes / $gb) . ' GB';
    } elseif ($bytes >= $tb) {
        return ceil($bytes / $tb) . ' TB';
    } else {
        return $bytes . ' B';
    }
}
function YX_GetThemes()
{
    global $yx;
    $themes = glob('themes/*', GLOB_ONLYDIR);
    return $themes;
}
function YX_ReturnBytes($val)
{
    $val  = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
function check_($check)
{
    $siteurl = urlencode($_SERVER['SERVER_NAME']);
    $file    = file_get_contents('https://www.access.firehide.com/check.php?code=' . $check . '&url=' . $siteurl);
    $check   = json_decode($file, true);
    return $check;
}
function check_success($check)
{
    $siteurl = urlencode($_SERVER['SERVER_NAME']);
    $file    = file_get_contents('https://www.access.firehide.com/check.php?code=' . $check . '&success=true&url=' . $siteurl);
    $check   = json_decode($file, true);
    return $check;
}
function YX_MaxFileUpload()
{
    //select maximum upload size
    $max_upload   = YX_ReturnBytes(ini_get('upload_max_filesize'));
    //select post limit
    $max_post     = YX_ReturnBytes(ini_get('post_max_size'));
    //select memory limit
    $memory_limit = YX_ReturnBytes(ini_get('memory_limit'));
    // return the smallest of them, this defines the real limit
    return min($max_upload, $max_post, $memory_limit);
}
function YX_CompressImage($source_url, $destination_url, $quality)
{
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
        @imagejpeg($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
        @imagegif($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source_url);
        @imagepng($image, $destination_url);
    }
}
function get_ip_address()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validate_ip($ip))
                    return $ip;
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}
function validate_ip($ip)
{
    if (strtolower($ip) === 'unknown')
        return false;
    $ip = ip2long($ip);
    if ($ip !== false && $ip !== -1) {
        $ip = sprintf('%u', $ip);
        if ($ip >= 0 && $ip <= 50331647)
            return false;
        if ($ip >= 167772160 && $ip <= 184549375)
            return false;
        if ($ip >= 2130706432 && $ip <= 2147483647)
            return false;
        if ($ip >= 2851995648 && $ip <= 2852061183)
            return false;
        if ($ip >= 2886729728 && $ip <= 2887778303)
            return false;
        if ($ip >= 3221225984 && $ip <= 3221226239)
            return false;
        if ($ip >= 3232235520 && $ip <= 3232301055)
            return false;
        if ($ip >= 4294967040)
            return false;
    }
    return true;
}
function YX_Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name, $tables = false, $backup_name = false)
{
    $mysqli = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
    $mysqli->select_db($sql_db_name);
    $mysqli->query("SET NAMES 'utf8'");
    $queryTables = $mysqli->query('SHOW TABLES');
    while ($row = $queryTables->fetch_row()) {
        $target_tables[] = $row[0];
    }
    if ($tables !== false) {
        $target_tables = array_intersect($target_tables, $tables);
    }
    $content = "-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host Connection Info: " . $mysqli->host_info . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "
-- Server version: " . mysqli_get_server_info($mysqli) . "
-- PHP Version: " . PHP_VERSION . "
--\n
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";\n
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;\n\n";
    foreach ($target_tables as $table) {
        $result        = $mysqli->query('SELECT * FROM ' . $table);
        $fields_amount = $result->field_count;
        $rows_num      = $mysqli->affected_rows;
        $res           = $mysqli->query('SHOW CREATE TABLE ' . $table);
        $TableMLine    = $res->fetch_row();
        $content       = (!isset($content) ? '' : $content) . "
-- ---------------------------------------------------------
--
-- Table structure for table : `{$table}`
--
-- ---------------------------------------------------------
\n" . $TableMLine[1] . ";\n";
        for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
            while ($row = $result->fetch_row()) {
                if ($st_counter % 100 == 0 || $st_counter == 0) {
                    $content .= "\n--
-- Dumping data for table `{$table}`
--\n\nINSERT INTO " . $table . " VALUES";
                }
                $content .= "\n(";
                for ($j = 0; $j < $fields_amount; $j++) {
                    $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                    if (isset($row[$j])) {
                        $content .= '"' . $row[$j] . '"';
                    } else {
                        $content .= '""';
                    }
                    if ($j < ($fields_amount - 1)) {
                        $content .= ',';
                    }
                }
                $content .= ")";
                if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                    $content .= ";\n";
                } else {
                    $content .= ",";
                }
                $st_counter = $st_counter + 1;
            }
        }
        $content .= "";
    }
    $content .= "
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    if (!file_exists('script_backups/' . date('d-m-Y'))) {
        @mkdir('script_backups/' . date('d-m-Y'), 0777, true);
    }
    if (!file_exists('script_backups/' . date('d-m-Y') . '/' . time())) {
        mkdir('script_backups/' . date('d-m-Y') . '/' . time(), 0777, true);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . '/' . time() . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/.htaccess')) {
        $f = @fopen("script_backups/.htaccess", "a+");
        @fwrite($f, "deny from all\nOptions -Indexes");
        @fclose($f);
    }
    if (!file_exists("script_backups/" . date('d-m-Y') . "/index.html")) {
        $f = @fopen("script_backups/" . date('d-m-Y') . "/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    if (!file_exists('script_backups/index.html')) {
        $f = @fopen("script_backups/index.html", "a+");
        @fwrite($f, "");
        @fclose($f);
    }
    $folder_name = "script_backups/" . date('d-m-Y') . '/' . time();
    $put         = @file_put_contents($folder_name . '/SQL-Backup-' . time() . '-' . date('d-m-Y') . '.sql', $content);
    if ($put) {
        $rootPath = realpath('./');
        $zip      = new ZipArchive();
        $open     = $zip->open($folder_name . '/Files-Backup-' . time() . '-' . date('d-m-Y') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($open !== true) {
            return false;
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            if (!preg_match('/\bscript_backups\b/', $file)) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        $mysqli->query("UPDATE " . T_CONFIG . " SET `value` = '" . date('d-m-Y') . "' WHERE `name` = 'last_backup'");
        $mysqli->close();
        return true;
    } else {
        return false;
    }
}
function YX_GetMedia($media = '', $is_upload = false)
{
    global $yx;
    if (empty($media)) {
        return '';
    }
    if ($yx['config']['amazone_s3'] == 1 && $is_upload == false) {
        return "https://" . $yx['config']['bucket_name'] . ".s3.amazonaws.com/" . $media;
    }
    return $yx['config']['site_url'] . '/' . $media;
}
function YX_CreateSession()
{
    $hash = sha1(rand(1111, 9999));
    if (!empty($_SESSION['hash_id'])) {
        $_SESSION['hash_id'] = $_SESSION['hash_id'];
        return $_SESSION['hash_id'];
    }
    $_SESSION['hash_id'] = $hash;
    return $hash;
}
function YX_CheckSession($hash = '')
{
    if (!isset($_SESSION['hash_id']) || empty($_SESSION['hash_id'])) {
        return false;
    }
    if (empty($hash)) {
        return false;
    }
    if ($hash == $_SESSION['hash_id']) {
        return true;
    }
    return false;
}
function YX_GetUserFromSessionID($session_id, $platform = 'web')
{
    global $sqlConnect;
    if (empty($session_id)) {
        return false;
    }
    $platform   = YX_Secure($platform);
    $session_id = YX_Secure($session_id);
    $query      = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_SESSIONS . " WHERE `session_id` = '{$session_id}' AND `platform` = '{$platform}'");
    return YX_Sql_Result($query, 0, 'user_id');
}
function YX_CreateLoginSession($user_id = 0)
{
    global $sqlConnect;
    if (empty($user_id)) {
        return false;
    }
    $time      = strtotime("2 days ago");
    $user_id   = YX_Secure($user_id);
    $hash      = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
    $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_SESSIONS . " WHERE `user_id` = '{$user_id}' AND `platform` = 'web' AND `time` < $time");
    $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_SESSIONS . " WHERE `session_id` = '{$hash}'");
    if ($query_two) {
        $platform_info = $_SERVER['HTTP_USER_AGENT'];
        $query_three = mysqli_query($sqlConnect, "INSERT INTO " . T_SESSIONS . " (`user_id`, `session_id`, `platform`,`platform_info`, `time`) VALUES('{$user_id}', '{$hash}', 'web','{$platform_info}', " . time() . ")");
        if ($query_three) {
            return $hash;
        }
    }
}
function YX_IsUrl($url = false)
{
    if (empty($url)) {
        return false;
    }
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
}
function YX_UploadIcon($data = array())
{
    global $yx, $sqlConnect;
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = YX_Secure($data['file']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $icon              = $data['type'];
    $allowed           = 'jpg,png,jpeg,gif';
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    $dir      = "themes/" . $yx['config']['theme'] . "/img/";
    $filename = $dir . "$icon.{$file_extension}";
    if (move_uploaded_file($data['file'], $filename)) {
        if (YX_SaveConfig($icon . "_extension", $file_extension)) {
            return true;
        }
    }
    return false;
}

function YX_CustomCode($a = false, $code = array())
{
    global $yx;
    $theme       = $yx['config']['theme'];
    $data        = array();
    $result      = false;
    $custom_code = array(
        "themes/$theme/custom/js/head.js",
        "themes/$theme/custom/js/footer.js",
        "themes/$theme/custom/css/style.css",
    );
    if ($a == 'g') {
        foreach ($custom_code as $key => $filepath) {
            if (is_readable($filepath)) {
                $data[$key] = file_get_contents($filepath);
            }
        }
        $result = $data;
    } else if ($a == 'p' && !empty($code)) {
        foreach ($code as $key => $content) {
            if (is_writable($custom_code[$key])) {
                @file_put_contents($custom_code[$key], $content);
            }
        }
        $result = true;
    }

    return $result;
}

function YX_SizeUnits($bytes = 0)
{
    if ($bytes >= 1073741824) {
        $bytes = round(($bytes / 1073741824)) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = round(($bytes / 1048576)) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = round(($bytes / 1024)) . ' KB';
    }
    return $bytes;
}


function YX_IsPRO()
{
    global $yx;
    if ($yx['loggedin'] == false) {
        return false;
    }

    if (!empty($yx['user']['is_pro'])) {
        return true;
    }

    return false;
}

function YX_IsMobileSubscriber()
{
    global $yx;
    if ($yx['loggedin'] == false) {
        return false;
    }

    if (!empty($yx['user']['is_mobile_subscriber'])) {
        return true;
    } else {
        return false;
    }
}


function YX_IsOnMobileWaitlist()
{
    global $yx;
    if ($yx['loggedin'] == false) {
        return false;
    }

    if (!empty($yx['user']['is_waiting'])) {
        return true;
    } else {
        return false;
    }
}

function YX_ObjectToArray($obj)
{
    if (is_object($obj))
        $obj = (array) $obj;
    if (is_array($obj)) {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = YX_ObjectToArray($val);
        }
    } else {
        $new = $obj;
    }
    return $new;
}

function yx_url_domain($url)
{
    $host = @parse_url($url, PHP_URL_HOST);
    if (!$host) {
        $host = $url;
    }
    if (substr($host, 0, 4) == "www.") {
        $host = substr($host, 4);
    }
    if (strlen($host) > 50) {
        $host = substr($host, 0, 47) . '...';
    }
    return $host;
}

function yx_get_user_ads($placement = 1, $limit = 1)
{
    global $yx, $db;
    $t_users = T_USERS;

    if (YX_IsPRO()) {
        return array();
    }

    $db->where("`user_id` IN (SELECT `user_id` FROM `$t_users` WHERE `wallet` > 0)");
    $db->where("`status`", 1);
    $db->where("`placement`", $placement);

    $ads = $db->orderBy('RAND()')->get(T_USER_ADS, $limit);
    return (!empty($ads)) ? $ads : array();
}

function yx_adcon_exists($ad_id = false)
{
    global $yx;
    return array_key_exists($ad_id, $yx['user_ad_cons']['uaid_']);
}

function yx_adcon_charge($ad_id = false)
{
    global $yx, $db;
    if (empty($ad_id) || !is_numeric($ad_id)) {
        return false;
    }

    $ad     = $db->where('id', $ad_id)->getOne(T_USER_ADS);
    $result = false;
    if (!empty($ad) && !yx_adcon_exists($ad_id)) {
        $ad_owner     = $db->where('user_id', $ad->user_id)->getOne(T_USERS);
        $con_price    = $yx['config']['ad_c_price'];
        $ad_trans     = false;
        $is_owner     = false;
        $ad_tans_data = array(
            'results' => ($ad->results += 1)
        );

        if ($yx['loggedin'] === true) {
            $is_owner = ($ad->user_id == $yx['user']['id']) ? true : false;
        }

        if (!$is_owner) {
            $ad_tans_data['spent']                = ($ad->spent += $con_price);
            $ad_trans                             = true;
            $yx['user_ad_cons']['uaid_'][$ad->id] = $ad->id;
            setcookie('_uads', htmlentities(serialize($yx['user_ad_cons'])), time() + (10 * 365 * 24 * 60 * 60), '/');
        }

        $update = $db->where('id', $ad_id)->update(T_USER_ADS, $ad_tans_data);

        if ($update && $ad_trans && !$is_owner) {

            $db->where('user_id', $ad_owner->user_id)->update(T_USERS, array(
                'wallet' => ($ad_owner->wallet -= $con_price)
            ));
            $result = true;
        }
    }

    return $result;
}

function fetchDataFromURL($url = '')
{
    if (empty($url)) {
        return false;
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($ch);
}
