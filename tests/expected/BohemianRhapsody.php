<?php

namespace God\Save\The\Queen;

class BohemianRhapsody extends \Phormium\Model
{
    protected static $_meta = array (
        'database' => 'testdb',
        'table' => 'bohemian_rhapsody',
        'pk' => 'is_this',
    );

    public $is_this;
    public $the_real;
    public $life;
}
