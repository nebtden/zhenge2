<?php

ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set('PRC'); // 切换到中国的时间

$run_time = strtotime('+1 day'); // 定时任务第一次执行的时间是明天的这个时候
$interval = 3600*12; // 每12个小时执行一次

if(!file_exists(dirname(__FILE__).'/cron-run')) exit(); // 在目录下存放一个cron-run文件，如果这个文件不存在，说明已经在执行过程中了，该任务就不能再激活，执行第二次，否则这个文件被多次访问的话，服务器就要崩溃掉了

do {
    if(!file_exists(dirname(__FILE__).'/cron-switch')) break; // 如果不存在cron-switch这个文件，就停止执行，这是一个开关的作用
    $gmt_time = microtime(true); // 当前的运行时间，精确到0.0001秒
    $loop = isset($loop) && $loop ? $loop : $run_time - $gmt_time; // 这里处理是为了确定还要等多久才开始第一次执行任务，$loop就是要等多久才执行的时间间隔
    $loop = $loop > 0 ? $loop : 0;
    if(!$loop) break; // 如果循环的间隔为零，则停止
    sleep(1);
    // ...
    // 执行某些代码
    $myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
    $txt = "Mickey Mouse\n";
    fwrite($myfile, $txt);
    $txt = "Minnie Mouse\n";
    fwrite($myfile, $txt);
    fclose($myfile);
    echo 111;

    // ...
    @unlink(dirname(__FILE__).'/cron-run'); // 这里就是通过删除cron-run来告诉程序，这个定时任务已经在执行过程中，不能再执行一个新的同样的任务
    $loop = $interval;
} while(true);