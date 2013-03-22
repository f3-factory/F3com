<?php
/**
    viewHelper

    The contents of this file are subject to the terms of the GNU General
    Public License Version 3.0. You may not use this file except in
    compliance with the license. Any of the license terms and conditions
    can be waived if you get permission from the copyright holder.
 **/

class ViewHelper
{

    /**
     * returns current base domain url
     * @author KOTRET, thx dude ;)
     * @return string
     */
    static function getBaseUrl()
    {
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0,
            strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")).$s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
        return urldecode($protocol."://".$_SERVER['SERVER_NAME'].$port.\Base::instance()->get('BASE').'/');
    }


    static function renderMarkdown($args)
    {
        $tmp = \Template::instance();
        $md_string = (isset($args[0])) ? '"'.addslashes($args[0]).'"' : '';
        if (array_key_exists('@attrib', $args)) {
            $attr = $args['@attrib'];
            foreach ($attr as &$att) {
                if (is_int(strpos($att, '@')))
                    $att = $tmp->token($att);
                else
                    $att = "'".$att."'";
            }
            if (array_key_exists('src', $attr))
                $md_string = 'file_exists('.$attr['src'].') ? \Base::instance()->read('.$attr['src'].') : ""';
        }
        $md = '<?php '.
            '$md_content='.$md_string.'; '.
            'echo \Markdown::instance()->convert($md_content);'.
            ' ?>';
        return $md;
    }

    /**
     * sweetens <select>-TAG rendering with `group` attribute
     * @param $args
     * @return string
     */
    static function renderSelect($args)
    {
        $attr = $args['@attrib'];
        $tmp = \Template::instance();
        foreach ($attr as &$att)
            $att = $tmp->token($att);
        $tags = '';
        $html = (isset($args[0])) ? $args[0] : '';
        if (array_key_exists('group', $attr)) {
            $html = '<?php foreach('.$attr['group'].' as $key => $val) {'.
                ' echo \'<option value="\'.$key.\'"\'.'.
                '( ( isset($_REQUEST[\''.$attr['name'].'\']) && $_REQUEST[\''.$attr['name'].'\'] == $key)?'.
                '\' selected="selected"\':\'\').\'>\'.$val.\'</option>\';'.'} ?>';
            unset($attr['group']);
        }
        foreach ($attr as $key => $val)
            $tags .= ' '.$key.'="'.$val.'"';
        return '<select'.$tags.'>'.$html.'</select>';
    }

}
