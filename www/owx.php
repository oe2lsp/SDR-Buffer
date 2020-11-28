<?
session_start();
$ts= $_GET['t'];
$day= $_GET['d'];
$b= $_GET['b'];

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
  $connection = @fsockopen('127.0.0.1', $port);
  if (is_resource($connection) == FALSE) { 
	 #openwebrx_save.py -d 20201110 -ts 1700 -p 7073 
    $r= shell_exec('cd  /opt/save/openwebrx_file/ && python2 openwebrx_save.py -b '.$band.' -s '.$samplingrate.' -c '.$center.' -d '.$day.' -ts '.$ts.' -p '.$port.' > /dev/null 2>/dev/null & echo $!');
    //printf('cd  /opt/save/openwebrx_file/ && python2 openwebrx_save.py -b '.$band.' -s '.$samplingrate.' -c '.$center.' -d '.$day.' -ts '.$ts.' -p '.$port.' > /dev/null 2>/dev/null & echo $!');
    //$r= shell_exec('cd  /opt/save/openwebrx_file3/ && python3 openwebrx_save.py -d '.$day.' -ts '.$ts.' -p '.$port.' > /dev/null 2>/dev/null & echo $!');
    $r= trim($r);
    $session="";
    switch ($port) {
      case 7070:
        $session="save1";
	break;
      case 7071:
        $session="save2";
        break;
      case 7072:
        $session="save3";
        break;
      case 7073:
        $session="save4";
        break;
      case 7074:
        $session="save5";
        break;
      case 7075:
        $session="save6";
        break;
      case 7076:
        $session="save7";
        break;
      case 7077:
        $session="save8";
        break;
      case 7078:
        $session="save9";
        break;
      case 7079:
        $session="save10";
        break;
      case 7080:
        $session="save11";
        break;
      case 7081:
        $session="save12";
        break;
      case 7082:
        $session="save13";
        break;
      case 7083:
        $session="save14";
        break;
      case 7084:
        $session="save15";
        break;
      default:
        $session="full";
    }
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
  if ((connection_status() != 0) || ($cnt > 600) || connection_aborted()) {
    echo "dead \n";	  
    shell_exec('kill -9 '.$r);
    die();
  }	
  sleep(1);
  $cnt++;
  echo "cnt ".$cnt."\n";	  
  #echo str_repeat(' ',1024*64);
}	

//check connection if dead kill owx

?>
