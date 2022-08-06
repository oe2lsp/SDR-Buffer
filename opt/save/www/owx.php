<?
session_start();
$ts= $_GET['t']; //time
$day= $_GET['d']; //day
$b= $_GET['b']; //band

require('function.php'); 

$b_par= selectband($b);
if ($b_par) {
  $band= $b_par['band'];
  $center_freq= $b_par['center_freq'];
  $bandwidth= $b_par['bandwidth'];
}
else {
  die("no parameters");
}
$samplingrate= $bandwidth* 1000000;
$center= $center_freq * 1000000;
//get free port
//7073 usw
if (is_numeric($ts) == 0 ) {
  echo "ts no int";	
  die;
}
if (is_numeric($day) == 0 ) {
  echo "d no int";	
  die;
}
@ini_set('zlib.output_compression',0); 
@ini_set('implicit_flush',1);
@ob_end_clean(); 
//set_time_limit(0);
header("Cache-Control: no-cache, must-revalidate"); 
header('X-Accel-Buffering: no');
ob_implicit_flush(1);
ignore_user_abort(true);


$ports= array(7070, 7071, 7072, 7073, 7074, 7075, 7076, 7077, 7078, 7079, 7080, 7081, 7082, 7083, 7084);
$r= null;
foreach ($ports as $port) {
  //echo("portX:".$port."\n");
  $connection = @fsockopen('127.0.0.1', $port, $ferrorcode, $ferrormsg, 0.1);
  //echo("port:".$port."\n");
  if (is_resource($connection) == FALSE) { 
    echo('cd  /opt/save/simple_openwebrx/ && python3 openwebrx.py -b '.$band.' -s '.$samplingrate.' -c '.$center.' -d '.$day.' -ts '.$ts.' -p '.$port.' > /dev/null 2>/dev/null & echo $!');
    $r= shell_exec('cd  /opt/save/simple_openwebrx/ && python3 openwebrx.py -b '.$band.' -s '.$samplingrate.' -c '.$center.' -d '.$day.' -ts '.$ts.' -p '.$port.' > /dev/null 2>/dev/null & echo $!');
    $r= trim($r);
    $session="";
    switch ($port) {
      case 7070:
        $session="savekw1";
	break;
      case 7071:
        $session="savekw2";
        break;
      case 7072:
        $session="savekw3";
        break;
      case 7073:
        $session="savekw4";
        break;
      case 7074:
        $session="savekw5";
        break;
      case 7075:
        $session="savekw6";
        break;
      case 7076:
        $session="savekw7";
        break;
      case 7077:
        $session="savekw8";
        break;
      case 7078:
        $session="savekw9";
        break;
      case 7079:
        $session="savekw10";
        break;
      case 7080:
        $session="savekw11";
        break;
      case 7081:
        $session="savekw12";
        break;
      case 7082:
        $session="savekw13";
        break;
      case 7083:
        $session="savekw14";
        break;
      case 7084:
        $session="savekw15";
        break;
      default:
        $session="full";
    }
    //wait for session started
    usleep(500000);
    echo "session:".$session."\n";
    $_SESSION['open']=$session;
    echo $port." found";
    break;
  }
  else {
    fclose($connection);
  }

}

echo "\npid ".$r;
//ob_flush();
//flush();
//start owx with port, day time and get pid
//python openwebrx_save.py -d 20200930 -ts 1515 -p 7073 &> /dev/null & echo $!

//return adress (port) (flush?)
echo "test\n";
echo str_repeat(' ',1024*64);
$cnt= 0;
while (true) {
  echo "running....\n";	
  #kill after 22 minutes
  if ((connection_status() != 0) || ($cnt > 1320) || connection_aborted()) {
    echo "dead \n";	  
    #shell_exec('kill -9 '.$r);
    shell_exec('/opt/save/kill.sh '.$r);
    //https://www.wikitechy.com/tutorials/linux/best-way-to-kill-all-child-processes-in-linux
    //pkill -TERM -P 27888
    die();
  }	
  sleep(1);
  $cnt++;
  echo "cnt ".$cnt."\n";	  
  #echo str_repeat(' ',1024*64);
}	

//check connection if dead kill owx

?>
