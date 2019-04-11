<?php
/**
 * Created by PhpStorm.
 * User: dawood.ikhlaq
 * Date: 02/04/2019
 * Time: 15:29
 */



include 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();


if($argc<2)
{
    echo "Dir path is missing".PHP_EOL;
    echo "Example {$argv[0]} /home/dir".PHP_EOL;
    die;
}

$directoryToTrack = realpath($argv[1]);

if(!is_dir($directoryToTrack))
{
    echo "directory doesn't exist".PHP_EOL;
    die;
}
$tracker = new \DirTrack\DirTrack($directoryToTrack);


while(1)
{
    try{
        $tracker->work();
    }catch (Exception $exception)
    {
        echo 'Following error occured'.PHP_EOL;
        echo $exception->getMessage().PHP_EOL;
    }
    sleep(60*env('SLEEP_AFTER_QUERY',5));
}