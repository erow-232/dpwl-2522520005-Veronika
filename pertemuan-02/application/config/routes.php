<?php
$route = [];
$route['default_controller'] = 'home';
$route['info/(:any)'] = 'home/info/$1';
$route['mahasiswa/(:num)'] = 'home/mahasiswa/$1';