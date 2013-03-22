<?php

namespace Layout;

class Model extends \DB\Jig\Mapper {

    /** @var \Base */
    protected $f3;

    public function __construct()
    {
        $this->f3 = \Base::instance();
        parent::__construct($this->f3->get('DB'), 'layout.json');
    }

}