<?php

return array(
    'indexes' => array(
        'name_index' => array(
            'columns' => array('name'),
        ),
    ),
    'id' => array(
        'id' => array(
            'type' => 'integer',
            'generator' => array(
                'strategy' => 'AUTO',
            ),
        ),
    ),
    'fields' => array(
        'name' => array(
            'type' => 'string',
            'length' => 50,
        ),
        'email' => array(
            'type' => 'string',
            'length' => 50,
        ),
    ),
);
