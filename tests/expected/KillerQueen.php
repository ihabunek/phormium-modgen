<?php

namespace God\Save\The\Queen;

class KillerQueen extends \Phormium\Model
{
    protected static $_meta = array (
        'database' => 'testdb',
        'table' => 'killer_queen',
        'pk' => array (
            'killer',
            'queen',
        ),
    );

    public $killer;
    public $queen;
    public $gunpowder;
    public $gelatine;
    public $dynamite;
    public $laser_beam;
}
