<?
require('function.php'); 

function listband($band,$day_input) {  
  $days= scandir($band);
  $list= "";
  printf("<h2>".$band."</h2>\n");


  for ($i=2; $i<count($days); $i++) {
    printf(" <a href='?b=".$band."&d=".$days[$i]."'>".$days[$i]."</a>"); 
    //printf('%%%%'.$day_input."____".$days[$i] );
    if ( $day_input == $days[$i] ) {
      $list= $days[$i];
    }
  }
  return $list;
}

function waterfall($band,$day) {

  $imagepath= $band."/".$day."/out/";
  $images= scandir($imagepath);
  //printf("###".$imagepath."---".$images);
  if ($images) {
    //printf("###".$imagepath);
    for ($i=2; $i<count($images); $i++) {
      if (substr($images[$i],2,2) == "00" ) {
        printf("<div>".substr($images[$i],0,2).":".substr($images[$i],2,2)."</div>");
      }
      printf("<a onclick='run(\"".$band."\",\"".$day."\",\"".$images[$i]."\")' touchstart='javascript:bigWaterfall(\"".$band."\",\"".$day."\",\"".$images[$i]."\");' touchmove='javascript:bigWaterfall(\"".$band."\",\"".$day."\",\"".$images[$i]."\");' onmousemove='javascript:bigWaterfall(\"".$band."\",\"".$day."\",\"".$images[$i]."\");' onmouseover='bigWaterfall(\"".$band."\",\"".$day."\",\"".$images[$i]."\")'><img src='".$imagepath.$images[$i]."' id='".$images[$i]."'></a>\n");

    }
  }

}


$selected_day= addslashes($_GET['d']);
$selected_band= addslashes($_GET['b']);

$b= selectband($selected_band);
if ($b) {
  $band= $b['band'];
  $center_freq= $b['center_freq'];
  $bandwidth= $b['bandwidth'];
}

?>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <script>
<?
printf("bw = ".$bandwidth."\n");
printf("center = ".$center_freq."\n");
?>      var controller;
      var signal;
      runPrepare();

      function bigWaterfall(band, day, dtime) {
        var chk= document.getElementById("chk_zoom");
        var wf=document.getElementById("waterfallbig");
        if (chk.checked) {
          wf.innerHTML="<img src='"+band+"/"+day+"/out_big/"+dtime+"'>"

          wf.style.visibility="visible" 
        }
        else {
          wf.style.visibility="hidden";
        }
        img = document.getElementById(dtime);
        pos_x = event.offsetX?(event.offsetX):event.pageX;//-img.offsetLeft;
        pos_y = event.offsetY?(event.offsetY):event.pageY;//-img.offsetTop;
        pos_y = img.offsetTop;
        a=dtime.split(".")
        qrg=getQRG(dtime,pos_x)
        ptext=a[0]+"<span class='cursorsmall'>LT</span> "+qrg+"<span class='cursorsmall'>MHz</span>";
        cursor(pos_x-30,pos_y-40,ptext)
        /*if (typeof profileMetadata[pos_x] !== 'undefined')
        {
          document.getElementById("profile-marker").style.visibility = "visible";
          document.getElementById("profile-marker").style.left = pos_x-1.5+"px";
          deleteProfileMpos();
          rfPoint(profileMetadata[pos_x].lat, profileMetadata[pos_x].lon);
          document.getElementById('profile-free').innerHTML = "Free space to ground: " + profileMetadata[pos_x].free + "m &nbsp;&nbsp; Radius of 1st order Fresnel zone: " + profileMetadata[pos_x].fresnel + "m" ;
        }*/
      }
      function getQRG(id,x) {
        img = document.getElementById(id);
        rel = bw/img.width*(x)    
        qrg = center-bw/2+rel
        qrg=Math.round(qrg*1000)/1000	 
        return qrg	         
      }	      
      function cursor(x,y,text) { 
        pointer = document.getElementById("cursor");
        pointer.style.position="absolute"
        pointer.style.top=y;//"300px"
        pointer.style.left=x;//"300px"
        pointer.innerHTML=text

      }
      function startPlayback() {
        fetch('owx.php?d=20201110&t=1400').then(function(response) {
          return response.text().then(function(text) {
            console.log(text);
          });
        });
		 
		 //	 .then(users => console.log(users))
         // .then(users => console.log(users));
      }       
      function runPrepare() {
        controller = new AbortController();
        signal = controller.signal;
      }
      function runAbort() {
        controller.abort();
      }	       
      async function* makeTextFileLineIterator(fileURL) {
        const utf8Decoder = new TextDecoder('utf-8');
        const response = await fetch(fileURL,{signal});
        const reader = response.body.getReader();
        let { value: chunk, done: readerDone } = await reader.read();
        chunk = chunk ? utf8Decoder.decode(chunk) : '';

        const re = /\n|\r|\r\n/gm;
        let startIndex = 0;
        let result;

        for (;;) {
          let result = re.exec(chunk);
          if (!result) {
            if (readerDone) {
              break;
            }
            let remainder = chunk.substr(startIndex);
            ({ value: chunk, done: readerDone } = await reader.read());
            chunk = remainder + (chunk ? utf8Decoder.decode(chunk) : '');
            startIndex = re.lastIndex = 0;
            continue;
          }
          yield chunk.substring(startIndex, result.index);
          startIndex = re.lastIndex;
        }
        if (startIndex < chunk.length) {
          // last line didn't end in a newline char
          yield chunk.substr(startIndex);
        }
      }

      
      async function run(band,day,dtime) {
        dtime=dtime.split('.')
        runAbort();
        runPrepare();  
        for await (let line of makeTextFileLineIterator('owx.php?d='+day+'&t='+dtime[0]+'&b='+band)) {
          param = line.includes('session:') 
          if (param) {
            test = line.split('session:') 
            full = test.includes('full')      
            if (full) {
              alert("All slots full please try again later")  
              return;
            }     
            console.log(line);
            var win = window.open('/'+test[1], '_blank');
            win.focus();
          }   
        }
      }


            
    </script>
  </head>
  <body>
    <a href="?n=0"><h1>SDR-Buffer</h1></a> 
<?


if (strlen($band) > 1) {
  $day= listband($band,$selected_day);
} else {
  $day= listband("2m",$selected_day);
  $day= listband("70cm",$selected_day);
}

if ( strlen($selected_day) <3) {
  printf("<br><span class='big'>Select from availables dates above!</span><br>");
} else {
  printf("<br>selected:".$day." &nbsp; load zoom<input type=\"checkbox\" id='chk_zoom' name='chk_zoom' /><br>");
}
?>
    <div id="waterfallbig"></div>
    <div id="cursor"></div>
    <div id="waterfall">
<?
if ($band && $day) {
  //printf("<br>selected:".$list." &nbsp; load zoom<input type=\"checkbox\" id='chk_zoom' name='chk_zoom' /><br>");
  waterfall($band,$day);
}
?>
    </div>
  </body>
</html>
