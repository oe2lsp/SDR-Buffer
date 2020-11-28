<?
function selectband($band_input) {
  $ret=[];
  switch ($band_input) {
    case "2m":
      $ret['center_freq'] = 145.0;
      $ret['bandwidth']= 2.4;
      $ret['band']= "2m";
      return $ret;  
    case "70cm":
      $ret['center_freq'] = 435.0;
      $ret['bandwidth']= 10.0;
      $ret['band']= "70cm";
      return $ret;   
    default:
      return null;
  }
}


?>