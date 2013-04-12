<?php
/**
    Navigation Menu Builder
 **/
namespace Navigation;

class View {

    static public function render($menu,$tmpl = 'navigation_main.html') {
        $f3 = \Base::instance();
        $nav = new Model();
        $current_page = $f3->get('PARAMS.page');
        $pages = $nav->getPages($menu);
        $activePageFound = false;
        foreach($pages as &$page)
            if($current_page == $page['slug']) {
                $page['active'] = true;
                $activePageFound = true;
            } else
                $page['active'] = false;
        if(!$activePageFound)
            $pages = self::resolveParentActivePage($current_page,$pages);
        $f3->set('menu', $pages);
        $content = \Template::instance()->render($f3->get('TMPL').$tmpl);
        $f3->clear('menu');
        return $content;
    }

    static private function resolveParentActivePage($current_page,$pages)
    {
        // get parent page id
        $pageModel = new \Page\Model();
        $pageModel->load(array('@slug = ?', $current_page));
        if (!$pageModel->dry() && !empty($pageModel->pid)) {
            // load parent page
            $pageModel->load(array('@_id = ? ', $pageModel->pid));
            if (!$pageModel->dry()) {
                $parent_page = $pageModel->cast();
                // see, if parent page is in the set of menu pages
                foreach ($pages as &$page)
                    if ($page['slug'] == $parent_page['slug']) {
                        $page['active'] = true;
                        return $pages;
                    }
                return self::resolveParentActivePage($parent_page['slug'],$pages);
            }
        }
        return $pages;
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
