<?php
$f3 = require ('lib/base.php');
$f3->set('AUTOLOAD','controllers/;');
$f3->set('DEBUG',3);

$f3->config('config.ini');

$f3->set('DB',
    new \DB\SQL(
        'mysql:host=localhost;port=3306;dbname=dimaquime_androidappdb',
        'dimaquime',
        'kra6uab$rTfn'
    )
);

$f3->route('GET|POST /', 'Main\ControllerUnderConstruction->uc');

$f3->route('GET|POST /@id', 'Event\ControllerEvent->GetConcreteEvent');
$f3->route('GET|POST /events', 'Event\ControllerEvent->GetAllEvents');

$f3->route('GET|POST /log/@login/@pwd', 'User\ControllerUser->login');
$f3->route('GET|POST /reg/@login/@pwd','User\ControllerUser->register');
$f3->route('GET|POST /add/@token/@report_id','User\ControllerUser->addReport');
$f3->route('GET|POST /remove/@token/@report_id','User\ControllerUser->removeReport');
$f3->route('GET|POST /visited/@token', 'User\ControllerUser->LoadAllVisitedReports');

$f3->run();
?>


