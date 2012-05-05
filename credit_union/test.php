#!/usr/bin/php5
<?php
ini_set( 'display_errors', 1 ); 
ini_set( 'log_errors', 1 ); 
error_reporting( E_ALL );

require_once( 'creditUnion.classes.php' );
require_once( 'creditUnion.tests.php' );

$m = new TestMember();
$m->test();

$a = new TestAccount();
$a->test();

$c = new TestCredit_Union();
$c->test();
$c->report();