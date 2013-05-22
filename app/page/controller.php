<?php
/**
    Page Controller

    The contents of this file are subject to the terms of the GNU General
    Public License Version 3.0. You may not use this file except in
    compliance with the license. Any of the license terms and conditions
    can be waived if you get permission from the copyright holder.
 **/
namespace Page;

class Controller
{
    protected
        $data,          // page meta data
        $include,       // template include
        $content;       // page contents

    public function __construct() {
        $this->data = array (
            'title' => '',
            'template' => ''
        );
        $this->include = '';
        $this->displayEdit = false;
    }

    /*
     * check if we run this from our own dev machine
     * this wiki is not intended to be edited on the live server     
     */
    protected function checkEnviroment($f3, $params)
    {
        if (is_int(strpos(strtolower($f3->get('HOST')), $f3->get('DOMAIN')))) {

            if (array_key_exists('marker', $params) && !empty($params['marker'])) {
                $edit_link = $f3->get('REPO').'/edit/master/'.
                             $params['page'].'/'.$params['marker'].'.md';
            } else {
                $edit_link = $f3->get('REPO').'/tree/master/'.$params['page'];
            }
            $f3->set('edit_link', $edit_link);
            $this->include = $f3->get('TMPL').'fork.html';
            return false;
        }
        return true;
    }

    /**
     * display page form
     * @param $f3
     * @param $params
     */
    public function edit($f3, $params)
    {
        $page_slug = '';
        if(array_key_exists('page',$params) && !empty($params['page'])) {
            $page_slug = $params['page'];
        }
        
        if(!$this->checkEnviroment($f3,$params))
            return;
        
        $layout = new \Layout\Model();
        $model = new Model();

        if(!empty($page_slug)) {
            $model->loadExistingPage($page_slug);
            if(!$model->dry()) {
                $this->data = $model->cast();

                $layout->load(array('@file = ?', $model->template));
                if (!$layout->dry()) {
                    // set markdown file paths
                    $marker = array();
                    foreach ($layout->marker as $mk) {
                        $path = $f3->get('MDCONTENT').$page_slug.'/'.$mk.'.md';
                        $marker[$mk] = (file_exists($path)) ? $f3->read($path) : '';
                    }
                    $f3->set('layout', $marker);
                } else {
                    $f3->error('500', sprintf('Layout `%s` not found',$model->template));
                }

            }
        }
        $title = (array_key_exists('title',$this->data)) ? $this->data['title'] : '';
        $this->data['title'] = 'Edit Page'.(($title)?': '.$title:'');

        $f3->set('FORM_title', $title);
        $f3->set('ACTION','edit/'.$page_slug);
        if(!$model->dry()) {
            $f3->set('backend_layout',$f3->get('TMPL').'backend/'.$model->template);
            $f3->set('REQUEST.template', $model->template);
        }
        $layout->reset();
        // load template list
        $templates = array();
        foreach($layout->find() as $item)
            $templates[$item->file] = $item->name;
        $f3->set('templates', $templates);

        // load page tree
        $pages = $model->find();
        $pageTree = array();
        $pagesByID = array();
        foreach($pages as $index => $page)
            $pagesByID[$page->_id] = $page->cast();
        // reorder to tree
        foreach ($pagesByID as &$value)
            if ($parent = $value['pid'])
                $pagesByID[$parent]['childs'][] = &$value;
            else
                $pageTree[] = &$value;
        $f3->set('parentPagesTree', array('' => '/') + $this->renderParentPages($pageTree));
        if (!$model->dry())
            $f3->set('REQUEST.pid', $model->pid);
        $this->include = $f3->get('TMPL').'edit.html';
    }

    private function renderParentPages($pageTree, $lvl = '')
    {
        $return = array();
        foreach ($pageTree as $page) {
            $return[$page['_id']] = $lvl.' '.$page['title'];
            if (array_key_exists('childs', $page) && !empty($page['childs']))
                $return = $return + $this->renderParentPages($page['childs'], $lvl.'&nbsp;&nbsp;.&nbsp;&nbsp;');
        }
        return $return;
    }

    /**
     * save submitted page data
     * @param $f3
     * @param $params
     * @return bool
     */
    public function save($f3, $params)
    {    
        if(!$this->checkEnviroment($f3,$params))
            return;

        $web = \Web::instance();
        $model = new Model();
        $slug_title = $web->slug($f3->get('POST.title'));

        if (empty($params['page'])) {
            $model->load(array('@slug = ?', $slug_title));
            if (!$model->dry())
                $f3->error(500, 'Another page with same title already exists.');
        } else {
            $model->load(array('@slug = ?', $web->slug($params['page'])));
            if (!$model->dry()) {
                if ($model->slug != $slug_title) {
                    if (!$this->rename())
                        return false;
                }
            }
        }

        // find layout marker
        $layout = new \Layout\Model();
        $layout->load(array('@file = ?', $f3->get('POST.template')));
        if (!$layout->dry()) {
            // write markdown files
            if (!is_writable($path = $f3->get('MDCONTENT')))
                trigger_error('data folder is not writable: '.$f3->get('MDCONTENT'));
            @mkdir($f3->get('MDCONTENT').$slug_title.'/');
            $marker = array();
            foreach ($layout->marker as $mk) {
                $path = $f3->get('MDCONTENT').$params['page'].'/'.$mk.'.md';
                $val = $f3->get('POST.'.$mk);
                if ($f3->exists('POST.'.$mk) && !empty($val))
                    $f3->write($path, $val);
            }
        } else {
            $f3->error('500', 'Layout not found');
        }

        // save page config
        $model->title = $f3->get('POST.title');
        $model->template = $f3->get('POST.template');
        $model->pid = $f3->get('POST.pid');
        $model->slug = $slug_title;
        $model->lang = 'en'; // TODO: support multilanguage
        $model->save();

        $this->data['title'] = 'page saved successfully';
        $this->include = $f3->get('TMPL').'saved.html';
    }

    /**
     * display page content
     * @param $f3
     * @param $params
     */
    public function view($f3, $params)
    {
        $model = new \Page\Model();
        $model->loadExistingPage($params['page']);

        if ($model->dry())
            $f3->error('404', 'The requested Page does not exist.');

        $this->data = $model->cast();
        // sub-template / page layout
        if (
            array_key_exists('template', $this->data) &&
            !empty($this->data['template'])
        ) {
            $layout = new \Layout\Model();
            $layout->load(array('@file = ?', $model->template));
            if(!$layout->dry()) {
                // set markdown file paths
                $marker = array();
                foreach ($layout->marker as $mk)
                    $marker[$mk] = $f3->get('MDCONTENT').$params['page'].'/'.$mk.'.md';
                $f3->mset(array('layout'=>$marker));
            } else {
                $f3->error('500', 'Layout not found');
            }
            $this->include = $f3->get('TMPL').'layout/'.$model->template;
        } else {
            $f3->error('500', 'No page layout template selected');
        }
    }

    /**
     * rename a page
     */
    public function rename()
    {
        $f3 = \Base::instance();
        if(!$this->checkEnviroment($f3,$f3->get('PARAMS')))
           return;

        // TODO: check if page could be renamed
        // check if new name has already been taken
        // scan all other pages for links, that should be repaired and do it

        // extra points: make it possible to remain a page
        // that redirects with a 301 Error (Moved Permanently) to the new URI
         \Base::instance()->error('501','renaming a page is not available.');
        return false;
    }

    /**
     * delete a page
     */
    public function delete($f3,$params)
    {
        if(!$this->checkEnviroment($f3,$params))
           return;

        $model = new Model();
        $model->loadExistingPage($params['page']);
        if(!$model->dry()) {
            $model->erase();
            // TODO: delete directory contents
        }
        $this->content = '<div class="alert">Page has been deleted</div>';
        $this->data['title'] = 'Deleting page '.$params['page'];
    }

    /**
     * render view
     */
    public function afterroute()
    {
        $f3 = \Base::instance();
        $data['page'] = $this->data;

        if (!empty($this->include))
            $data['include'] = $this->include;
        if (!empty($this->content))
            $data['content'] = $this->content;

        $view = new View();
        echo $view->render($data, $f3->get('TMPL').'layout.html');
    }

}
