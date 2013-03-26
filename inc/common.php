<?php


class Common
{

    /**
     * display error page
     */
    public function error()
    {
        $f3 = \Base::instance();
        $f3->set('headline', $f3->get('ERROR.code').' - '.$f3->get('ERROR.text'));

        if ($f3->get('ERROR.code') == 500) {
            // show highlighted strack trace, code was taken from Base->error,
            // maybe there's an easier way to include this in a custom error handler?
            $trace = $f3->get('ERROR.trace');
            $highlight = PHP_SAPI != 'cli' &&
                $f3->get('HIGHLIGHT') && is_file($css = $f3->get('UI').'css/'.$f3::CSS);
            $out = '';
            $eol = "\n";
            foreach ($trace as $frame) {
                $line = '';
                if (isset($frame['class']))
                    $line .= $frame['class'].$frame['type'];
                if (isset($frame['function']))
                    $line .= $frame['function'].'('.(isset($frame['args']) ?
                        $f3->csv($frame['args']) : '').')';
                $src = $f3->fixslashes($frame['file']).':'.$frame['line'].' ';
                error_log('- '.$src.$line);
                $out .= '• '.($highlight ?
                    ($f3->highlight($src).' '.$f3->highlight($line)) :
                    ($src.$line)).$eol;
            }
            $f3->set('trace', ($highlight ? ('<style>'.file_get_contents($css).'</style>') : '').
                ($f3->get('DEBUG') ? ('<pre>'.$out.'</pre>'.$eol) : ''));
        } else {
            $f3->set('trace', '');
        }
        $content = \Template::instance()->render($f3->get('TMPL').'404.html');
        $f3->set('content', $content);
        $f3->set('page.title', 'Error '.$f3->get('ERROR.code'));
        echo \Template::instance()->render($f3->get('TMPL').'layout.html');
    }


    public function installJIG()
    {
        $f3 = \Base::instance();
        $db = $f3->get('DB');

        if (!file_exists('db/pages.json')) {
            // adding basic data
            $f3->set('pages', array(
               array(
                   'title' => 'Home',
                   'slug' => 'home',
                   'lang' => 'en',
                   'deleted' => 0,
               ),
               array(
                   'title' => 'Examples',
                   'slug' => 'examples',
                   'lang' => 'en',
                   'deleted' => 0,
               ),
               array(
                   'title' => 'Documentation',
                   'slug' => 'documentation',
                   'lang' => 'en',
                   'deleted' => 0,
               ),
               array(
                   'title' => 'Plugins',
                   'slug' => 'plugins',
                   'lang' => 'en',
                   'deleted' => 0,
               )
            ));
            $pages = new \DB\Jig\Mapper($db, 'pages.json');
            for ($i = 0; $i < count($f3->get('pages')); $i++) {
                $pages->copyfrom('pages.'.$i);
                $pages->save();
                $pages->reset();
            }
            echo "SUCCESS: pages table created <br>";

        } else {
            echo "pages table already created <br>";
        }
    }

}
