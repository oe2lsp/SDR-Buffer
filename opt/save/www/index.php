<?
require('function.php'); 
# vertical position <p> for hour marking
# get position from mouse on image
#     calc data based on minutes.txt
#     touch support
#     click -> start websdr


$selected_day= addslashes($_GET['d']);
$selected_band= addslashes($_GET['b']);

$b= selectband($selected_band);
if ($b) {
  $band= $b['band'];
  $center_freq= $b['center_freq'];
  $bandwidth= $b['bandwidth'];
  $footer= $b['footer'];
}

?>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?
  echo ("<link rel=\"stylesheet\" href=\"style.css?".rand(1000,9999)."\">");
?>      
    <meta charset="UTF-8">
    <script>
<?

printf("bw = ".$bandwidth."\n");
printf("center = ".$center_freq."\n");
printf("band = '".$band."'\n");
printf("day = '".$selected_day."'\n");
?>      var controller;
      var signal;

      runPrepare();

      function bigWaterfall(e) {
        var chk= document.getElementById("chk_zoom");
        var wf=document.getElementById("waterfallbig");
        img = document.getElementById('waterfallimg');
	
        img = document.getElementById("waterfallimg");
        pos_x = event.offsetX?(event.offsetX):e.pageX-img.offsetLeft;
        pos_y = event.offsetY?(event.offsetY):e.pageY+img.offsetTop;

        a=getMinutes(pos_y);
        qrg=getQRG(pos_x)
        ptext=a+"<span class='cursorsmall'>UTC</span> "+qrg+"<span class='cursorsmall'>MHz</span>";
        cursor(pos_x,pos_y-40,ptext)
	
        if (chk.checked) {
          wf.innerHTML="<img src='"+band+"/"+day+"/out_big/"+a+".png'>"
          wf.style.visibility="visible" 
        }
        else {
          wf.style.visibility="hidden";
        }

      }
      function getQRG(x) {
        img = document.getElementById('waterfallimg');
        rel = bw/img.width*(x)    
        qrg = center-bw/2+rel
        qrg=Math.round(qrg*1000)/1000	 
        return qrg	         
      }	     
      function getMinutes(y) {    
        y=Math.floor(y)
        img = document.getElementById('waterfallimg');
        minid = Math.floor(y/3);
        minmouse = minutes[minid];
        minmouse = minmouse.toString().padStart(4, '0')
        return minmouse;
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
		 
      }       
      function runPrepare() {
        controller = new AbortController();
        signal = controller.signal;
      }
      function runAbort() {
        if ( typeof controller.abort == 'function' ) {
          controller.abort();
        }
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

      
      async function run(e) {
	      
        img = document.getElementById('waterfallimg');
        pos_y = event.offsetY?(event.offsetY):event.pageY-img.offsetTop;
        rect = e.target.getBoundingClientRect();
        pos_x = e.clientX - rect.left; //x position within the element.
        pos_y = e.clientY - rect.top;
        dtime=getMinutes(pos_y)
	      
        runAbort();
        runPrepare();  
        for await (let line of makeTextFileLineIterator('owx.php?d='+day+'&t='+dtime+'&b='+band)) {
          param = line.includes('session:') 
          if (param) {
            session_number = line.split('session:') 
            full = session_number.includes('full')      
            if (full) {
              alert("All slots full please try again later")  
              return;
            }     
            console.log(line);
            session_path='/'+session_number[1]
            var win = window.open(session_path, '_blank')
            win.focus()
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
} else if ( $b != null ) {
  //shortwave band, nothing to do here
} else {
  /////ADD BANDS HERE ///////////    
  $day= listband("80m",$selected_day);
}


if ( strlen($selected_day) <3) {
  printf("<br><span class='big'>Select from availables dates above!</span><br>");
} else {
  printf("<br>selected:".$day." &nbsp; load zoom<input type=\"checkbox\" id='chk_zoom' name='chk_zoom' /><br>");
}
?>
    <div id="waterfallbig"></div>
    <div id="waterfall">
    <div id="cursor"></div>
<?
if ($band && $day) {
  waterfall($band,$day);
}
?>
    </div>
    <div id="footerfall">
<?
if (strlen($band)>1) {
  printf("<br>".$footer."<br>");

}

?>
    </div>
  </body>
</html>
