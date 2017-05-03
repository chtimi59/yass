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

function setVisibility(id, visibility) {
    var e = document.getElementById(id).style;
    e.display = (visibility)?'block':'none';
}

function toggleVisibility(id) {
    var e = document.getElementById(id).style;
    e.display = (e.display == 'none')?'block':'none';
}

function stopEvent(e, filterOutElt) {
    if(filterOutElt == e.target) { return true; }
    if(e.preventDefault) { e.preventDefault(); }
    if(e.stopPropagation) { e.stopPropagation(); }
    return false;
}

var dragInOutCount = 0;
function startDragFile() {
    var e = document.getElementsByTagName("BODY")[0];
    e.style.backgroundColor='#b6d0be';
    var e = document.getElementById('file-dummy');
    e.style.backgroundColor='#b6d0be';
    dragInOutCount++;
}

function stopDragFile() {
    dragInOutCount--;
    if (dragInOutCount!=0) return;
    var e = document.getElementsByTagName("BODY")[0];
    e.style.backgroundColor='';
    var e = document.getElementById('file-dummy');
    e.style.background='';
}

function onDrop() {
    console.log('drop');
    dragInOutCount=1;
    stopDragFile();        
}

function onAssetFileChange(v,id) {           
    var e = document.getElementById(id);
    if (v=='') {
        e.innerHTML = 'Please select an asset file';
    } else {
        e.innerHTML = v;
    }            
}

function onAssetFileDragEnter(id) {    
    console.log('enter');       
    var e = document.getElementById(id);
    e.style.borderWidth='5px'; 
    e.style.borderColor='#FF0'; 
}

function onAssetFileDragLeave(id) {           
    console.log('leave');
    var e = document.getElementById(id);
    e.style.borderWidth=''; 
    e.style.borderColor=''; 
}

function basename(path) {
   return path.split(/[\\/]/).pop(); 
}

function getFormElt(formId,fieldName) {
   return document.querySelector("form[id='"+formId+"'] input[name='"+fieldName+"']");
}

function updateDisplayStatus(id) {    
   var xhttp = new XMLHttpRequest();
   xhttp.open("GET", "../display/api.php", false);
   xhttp.setRequestHeader("Content-type", "application/json");
   xhttp.send();
   var resp = JSON.parse(xhttp.responseText);   
   //console.log(resp);
   
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
	  
      strSequence = ''
      var curAssetId = display_data['assetId'];
      for(j=0; j<resp['sequence'].length; j++) {
         var assetId = resp['sequence'][j];
         strSequence += '<div class=\'asset '+((assetId==curAssetId)?'on':'')+'\'>'+assetId+ '</div>';
      }
      
      html += '<li>['+display_data['ip']+'] '+strSequence+' <span class=\'lastseen\'>(seen '+strLastSee+' ago)</span></li>';
   }
   html += '  </ul>'   
   html += '</li>'
   html += '</ul>'
   document.getElementById(id).innerHTML = html;
}