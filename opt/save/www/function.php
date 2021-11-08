<?
function selectband($band_input) {
  $ret=[];
  switch ($band_input) {
    case "80m":
      $ret['center_freq'] = 3.65;
      $ret['bandwidth']= 0.3;
      $ret['band']= "80m";
      $ret['footer']="PC sponsored by OE1MCU";
      return $ret;

    default:
      if (strlen($band_input)>1 ) {
        $ret['center_freq'] = 0;
        $ret['bandwidth']= 0;
        $ret['band']= "";
        $ret['footer']="";
        return $ret;
      }
      return null;
  }
}

function listband($band,$day_input) {
  $days= scandir($band);
  $list= "";
  printf("<h2>".$band."</h2>\n");


  for ($i=2; $i<count($days); $i++) {
    printf(" <a href='/sdrbuffer-kw/index.php?b=".$band."&d=".$days[$i]."'>".$days[$i]."</a>");
    //printf('%%%%'.$day_input."____".$days[$i] );
    if ( $day_input == $days[$i] ) {
      $list= $days[$i];
    }
  }
  return $list;
}

function loadminutes($band,$day)
{
  $minutespath= $band."/".$day."/out_comp/minutes.txt";

  $minutes= file_get_contents($minutespath, false);
  $min_array= explode("\n",$minutes);  
  return $min_array;
}

function waterfall($band,$day) {
  $minutes= loadminutes($band,$day);
  $imagepath= $band."/".$day."/out_comp/minutes.png";
  printf("<img src='/sdrbuffer-kw/".$imagepath."' id='waterfallimg'  style='height:".(count($minutes)*3).";' 
     oncontextmenu='javascript:run(event);' 
     onmousedown='javascript:run(event);' 
     touchstart='javascript:bigWaterfall(event);' 
     touchmove='javascript:bigWaterfall(event);'
     onmousemove='javascript:bigWaterfall(event);'
     onmouseover='javascript:bigWaterfall(event);'>");
  $cnt=0;
  $min_meta="";
  foreach($minutes as &$min) {
    if (substr($min,2,2) == "00" ) { 
      printf("<div class=\"minuteline\" style=\"top:".($cnt*3)."px;\"> ".substr($min,0,2).":".substr($min,2,2)."</div>\n");  
    }
    $min_meta=$min_meta."\"".$min."\",";
    $cnt++;
	  
  }
  printf("\n<script>var minutes=[".$min_meta."];</script>");

}


?>
