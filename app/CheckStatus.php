<?php
if(empty($argv[1])) {
    die('Job Id Cannot Null!');
}

//require '../lib/Resque/Job/Status.php';
//require '../lib/Resque.php';

require './vendor/autoload.php';

date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$status = new Resque_Job_Status($argv[1]);
if(!$status->isTracking()) {
    die("Resque is not tracking the status of this job.\n");
}

echo "Tracking status of ".$argv[1].". Press [break] to stop.\n\n";
while(true) {
    fwrite(STDOUT, "Status of ".$argv[1]." is: ".$status->get()."\n");
    sleep(1);
}