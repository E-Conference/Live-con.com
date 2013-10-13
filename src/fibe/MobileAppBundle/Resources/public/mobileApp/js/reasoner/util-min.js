

(function(){function TextFile(a){if(!a){throw"URL of the file is not specified!"}this.url=a}TextFile.prototype={getText:function(){var b;if(!this.url){throw"URL of the file is not specified!"}b=new XMLHttpRequest();try{b.open("GET",this.url,false);b.send(null);return b.responseText}catch(a){throw a}}};function TabControl(d,f){var c,e,b,a;this.tabs=d;this.classNames=f;b=d.length||0;for(a=0;a<b;a++){e=d[a];this.setOnClickHandler(this,e.tabId);if(e.enabled){this.setEnabled(e.tabId,true)}else{this.setEnabled(e.tabId,false)}document.getElementById(e.boxId).style.display="none"}c=this.findFirstEnabled();if(c){this.select(c.tabId)}}TabControl.prototype={find:function(d){var c=this.tabs,b=c.length,a;for(a=0;a<b;a++){if(c[a].tabId===d){return c[a]}}return undefined},setOnClickHandler:function(a,c){var b=document.getElementById(c).onclick;document.getElementById(c).onclick=function(){if(b){b()}a.select(c);return false}},findFirstEnabled:function(){var d,c=this.tabs,b=c.length,a;for(a=0;a<b;a++){d=c[a];if(d.enabled){return d}}},setEnabled:function(d,a){var b,c;if(!this.classNames||(!a&&!this.classNames.disabled)){return}c=this.find(d);if(!c){return}if(!a){document.getElementById(c.tabId).className=this.classNames.disabled;if(this.selected&&this.selected.tabId===d){document.getElementById(c.boxId).style.display="none";b=this.findFirstEnabled();if(b){this.select(b.tabId)}}}else{document.getElementById(c.tabId).className=this.classNames.enabled||""}c.enabled=a},select:function(b){var a;if(!this.classNames||!this.classNames.active){return}a=this.find(b);if(!a||!a.enabled){return}if(this.selected){document.getElementById(this.selected.tabId).className=this.classNames.inactive||"";document.getElementById(this.selected.boxId).style.display="none"}document.getElementById(a.tabId).className=this.classNames.active;document.getElementById(a.boxId).style.display="block";this.selected=a}};function TreeControl(i,f,j,t,u){var g,h,o,p,a,k,b,q,n,s,r,l,e,d,c,m;this.hierarchy=i;this.hostId=f;this.highlightClass=u;c=document.createElement("div");k=new jsw.util.Queue();n=new jsw.util.Queue();o=i.length;for(p=0;p<o;p++){n.enqueue(i[p]);k.enqueue(c)}s=0;while(!n.isEmpty()){q=n.dequeue();a=k.dequeue();g=q.children;o=g.length;l=q.names;e=l.length;b="";for(d=0;d<e;d++){b+=l[d]+", "}r=document.createElement("div");r.style.display="block";m=document.createElement("a");m.setAttribute("class",j);m.innerHTML=b.substring(0,b.length-2);r.appendChild(m);if(o>0){this.assignItemOnClick(r);r.appendChild(document.createTextNode(" ("));m=document.createElement("span");m.setAttribute("class",t);m.innerHTML=o;r.appendChild(m);r.appendChild(document.createTextNode(")"));h=document.createElement("div");h.style.display="none";h.style.marginLeft="20px";for(p=0;p<o;p++){n.enqueue(g[p]);k.enqueue(h)}r.appendChild(h)}a.appendChild(r);s++}this.showFirstLevel(c);document.getElementById(f).appendChild(c)}TreeControl.prototype={assignItemOnClick:function(a){a.firstChild.onclick=function(){var e,d,b,c;d=a.lastChild;e=d.firstChild;b=0;c=0;while(e!==null){if(e.style.display!=="none"){c++}e.style.display="block";e=e.nextSibling;b++}d.style.display=(c<b||d.style.display==="none")?"block":"none"}},showMatches:function(q){var g,o,b,m,l,k,w,r,e,h,s,p,t,u,n,f,d,j,i,v,c;function a(y,x){j=true;return(v)?v+x+"</span>":x}k=this.hierarchy;t=k.length;e=this.hostId;r=document.getElementById(e);c=r.removeChild(r.firstChild);b=c.firstChild;p=new jsw.util.Queue();m=new jsw.util.Queue();for(u=0;u<t;u++){p.enqueue(k[u]);m.enqueue(b);b=b.nextSibling}l=new RegExp("("+q+")");w=this.highlightClass;v=(w)?'<span class="'+w+'">':"";while(!p.isEmpty()){s=p.dequeue();b=m.dequeue();g=s.children;t=g.length;if(t>0){o=b.lastChild.firstChild;for(u=0;u<t;u++){p.enqueue(g[u]);m.enqueue(o);o=o.nextSibling}}n=s.names;f=n.length;j=false;h="";if(q===""||v===""){for(d=0;d<f;d++){h+=n[d]+", "}}else{for(d=0;d<f;d++){h+=n[d].replace(l,a)+", "}}b.firstChild.innerHTML=h.substring(0,h.length-2);if(q===""){b.style.display="block";if(t>0){b.lastChild.style.display="none"}}else{if(j){i=b;do{i.style.display="block";i.lastChild.style.display="block";i=i.parentNode}while(i.parentNode!==null)}else{b.style.display="none"}}}if(q===""){this.showFirstLevel(c)}r.appendChild(c)},showFirstLevel:function(a){var b,c;b=a.firstChild;while(b!==null){b.style.display="block";if(b.childNodes.length>1){c=b.lastChild;c.style.display="block"}b=b.nextSibling}}}});