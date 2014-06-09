#!/usr/bin/php
<?php
use Infrastructure\Gearman\GearmanWorker;

include 'bootstrap.php';

$worker = new GearmanWorker('127.0.0.1:4730');
$worker->addFunction('doStuff', function (\GearmanJob $job) {
    echo "doStuff invoked\n";
    var_dump($job->workload());
});
$worker->addFunction('doOtherStuff', function (\GearmanJob $job) {
    echo "doOtherStuff invoked\n";
    var_dump($job->workload());
});
$worker->addFunction('doException', function (\GearmanJob $job) {
    echo "doException invoked\n";
    throw new Exception('woot');
});
$worker->spawn();
