function toogleBroadcast(e, asset_id) {    
    v = e.target.checked;
    label = e.path[1].childNodes[0];
    console.log(label);
    if (v) {
        label.className = 'checked';
    } else {
        label.className = '';
    }
    e.stopPropagation();    
}

function toggleVisibiltiy(id) {
    var e = document.getElementById(id).style;
    e.display = (e.display == 'none')?'block':'none';
}

function onAssetFile(v,id) {           
    var e = document.getElementById(id);
    if (v=='') {
        e.innerHTML = 'Please select an asset file';
        e.style.background='';
    } else {
        e.innerHTML = v;
        e.style.background='rgba(200, 255, 200, 0.4)';
    }            
}

function updateDisplayStatus(id) {    
   var xhttp = new XMLHttpRequest();
   xhttp.open("GET", "../rest-api/displays.php", false);
   xhttp.setRequestHeader("Content-type", "application/json");
   xhttp.send();
   var resp = JSON.parse(xhttp.responseText);   
      
   var html = '';
   html += 'Server Time: '+resp['time']+'<br>';
   html += 'Displays:<br>';
   html += '  <ul>'
   for(i=0; i<resp['displays'].length; i++) {
	  var display_data = resp['displays'][i];
	  
      lastSee = display_data['diff'];
      if (lastSee>86400) {
          break; /* more than 1 day : too old */
      } else if (lastSee>3600) {
          strLastSee = ""+Math.ceil(lastSee/3600)+" hours";
      } else if (lastSee>60) {
          strLastSee = ""+Math.ceil(lastSee/60)+"mn";
      } else {
          strLastSee = ""+lastSee+"s";
      }
	  
      html += '<li>['+display_data['ip']+'] seen '+strLastSee+' ago</li>';
	  html += '<ul>';
      html += '<li> Sequence: '+display_data['assetId']+'</li>';
	  html += '</ul>';
   }
   html += '  </ul>'   
   html += '</li>'
   html += '</ul>'
   document.getElementById(id).innerHTML = html;
}