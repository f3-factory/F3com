<?php
/**
    Navigation Controller
 **/
namespace Navigation;

class View {

    static public function render($menu,$tmpl = 'navigation_main.html') {
        $f3 = \Base::instance();
        $nav = new Model();

        $f3->set('menu', $nav->getPages($menu));
        $f3->set('current_page_path',$f3->get('PARAMS.page'));
        $content = \Template::instance()->render($f3->get('TMPL').$tmpl);
        $f3->clear('menu');
        return $content;
    }

    static public function renderTag($args)
    {
        $attr = $args['@attrib'];
        $tmp = \Template::instance();
        foreach ($attr as &$att)
            $att = $tmp->token($att);
        if (array_key_exists('menu', $attr)) {
            $nav_code = '\Navigation\View::render(\''.$attr['menu'].'\');';
            if (array_key_exists('tmpl', $attr))
                $nav_code = "\Navigation\View::render('".$attr['menu']."','".$attr['tmpl']."');";
            return '<?php echo '.$nav_code.' ?>';
        }
    }


}
