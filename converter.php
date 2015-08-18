<?php
/*
 * Converter uses ffmpeg https://www.ffmpeg.org
 *
 * Installing on mac with Homebrew:
 * $ brew install ffmpeg
 *
 * If shell_exec() is denied in webserver php.ini
 * this script must be run by a command:
 * $ php converter.php
 *
 * */

$video_source    = "./trailer.mp4";
$frames_folder   = "./frames"; // must be empty

$result_fps      = 10; // frames per second
$result_duration = 25; // length of resulted video in seconds
$result_file     = "./video.txt";

for ($i = 1; $i <= $result_fps*$result_duration; $i++) {
    // serve only seconds (time limit - 1 minute, WIP)
    $time = $i*$result_fps / 100;

    if($time < 10) {
        $time = "0".$time;
    }

    $count = sprintf('%03d', $i);
    shell_exec("ffmpeg -ss 00:00:{$time} -i trailer.mp4 -frames:v 1 {$frames_folder}/frame{$count}.jpg");
}

$frames_cource = scandir($frames_folder);

$dump = array();

foreach($frames_cource as $frame) {
    if(strstr($frame, '.jpg')) {
        $raw = file_get_contents($frames_folder ."/".$frame);
        $base64 = "data:image/jpeg;base64," . base64_encode($raw);
        $dump[] = $base64;
    }
}

file_put_contents($result_file, json_encode($dump, 64));

echo "\n-----------------------\nDONE!\n";
