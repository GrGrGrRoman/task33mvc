<?php 

return
[
    // MainController
    '' =>
    [
        'controller' => 'main',
        'action' => 'index',
    ],
    'chatroom' =>
    [
        'controller' => 'main',
        'action' => 'chatroom',
    ],

    // Account Controller    
    'login' =>
    [
        'controller' => 'account',
        'action' => 'login',
    ],
    'logout' =>
    [
        'controller' => 'account',
        'action' => 'logout',
    ],
    'leave' =>
    [
        'controller' => 'account',
        'action' => 'leave',
    ],
    'register' =>
    [
        'controller' => 'account',
        'action' => 'register',
    ],
    'confirm/{token:\w+}' =>
    [
        'controller' => 'account',
        'action' => 'confirm',
    ],
    'profile' =>
    [
        'controller' => 'account',
        'action' => 'profile',
    ],    
    'profileavataredit' =>
    [
        'controller' => 'account',
        'action' => 'profileavataredit',
    ],

];