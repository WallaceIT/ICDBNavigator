/*
 * Qrcodesvg  Javascript library for QrCode generation
 *
 * Copyright 2012, Vincent Pellé - pelle.vincent@gmail.com
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * 
 * Includes Javascript QR Encoder
 * Copyright 2010, tz@execpc.com, released under GPLv3
 */
var Qrcodesvg=function(){function e(e){return JSON.parse(JSON.stringify(e))}return function(t,n,r,i){var s=[],i=i||[],o,u,a,f,r=r,l=0;var c=function(e,t){if(e==undefined||t==undefined){throw new Error("A Square needs x and y coords")}this.x=e;this.y=t;this.corners=[]};this.init=function(){if(Raphael==undefined)throw new Error("Raphael object not found");f=new Qrcodesvg.encoder(t,i["ecclevel"]||1);a=f.data();o=Raphael(n,r,r);u=e(a);this.detectPatterns()};this.square_exists=function(e,t){try{return a[t][e]==1?true:false}catch(n){return false}};this.createSquare=function(e,t){var n=new c(e,t);var r=[this.square_exists(e,t-1),this.square_exists(e+1,t),this.square_exists(e,t+1),this.square_exists(e-1,t)];n.corners=[];var i=0,s;for(;i<r.length;i++){s=i>0?i-1:r.length-1;n.corners.push(r[i]==false&&r[s]==false)}return n};this.detectPatterns=function(){var e=0,t;for(;e<u.length;e++){for(t=0;t<u[e].length;t++){if(u[e][t]==1){pattern=[];pattern=this.detectX(e,t,pattern);s.push(pattern)}}}};this.detectY=function(e,t,n){var r=-1;while(r<2){if(e+r>=0&&u[e+r]!=undefined){if(u[e+r][t]==1){n.push(this.createSquare(t,e+r));u[e+r][t]=0;if(e+r!=e){n=this.detectY(e+r,t,n)}n=this.detectX(e+r,t,n)}}r++}return n};this.detectX=function(e,t,n){var r=-1;while(r<2){if(t+r>=0&&u[e][t+r]!=undefined){if(u[e][t+r]==1){n.push(this.createSquare(t+r,e));u[e][t+r]=0;if(t+r!=t){n=this.detectX(e,t+r,n)}n=this.detectY(e,t+r,n)}}r++}return n};this.draw=function(t,n){var i=0,t=t||{},u,a,c,h,p,n=n||{fill:"#000000","stroke-width":1,stroke:"#000000"},d=e(n);if(t["method"]==undefined){t["method"]="classic"}else if(Qrcodesvg.drawMethods[t["method"]]==undefined){throw new Error("Drawing method not found")}h=t["fill-colors"];a=(r-l*2)/f.getwidth();for(;i<s.length;i++){if(h&&t["fill-colors-scope"]!="square"){p=h[i%h.length]}for(u=0;u<s[i].length;u++){if(h&&t["fill-colors-scope"]=="square"){p=h[u%h.length]}t["dot"]=s[i][u];t["px"]=a;t["frame_size"]=l;if(t["scope"]=="square"){t["dot"].corners=[true,true,true,true]}if(p){d["fill"]=p;if(n["stroke"]==undefined&&n["stroke-width"]){d["stroke"]=p}}Qrcodesvg.drawMethods[t["method"]](o,t,d)}}};this.setBackground=function(e,t){var n=r,e=e||{},i=t["stroke-width"]||0,s=e["padding"]||0,u=e["margin"]||0,a,f;l=i+s+u;if(i)n-=i;if(u)n-=u*2;a=f=i?i/2:0;o.rect(a+u,f+u,n,n).attr(t)};this.makeSVG=function(e,t){return o.toSVG()};this.init()}}();Qrcodesvg.encoder=function(){var e={adelta:[0,11,15,19,23,27,31,16,18,20,22,24,26,28,20,22,24,24,26,28,28,22,24,24,26,26,28,28,24,24,26,26,26,28,28,24,26,26,26,28,28],vpat:[3220,1468,2713,1235,3062,1890,2119,1549,2344,2936,1117,2583,1330,2470,1667,2249,2028,3780,481,4011,142,3098,831,3445,592,2517,1776,2234,1951,2827,1070,2660,1345,3177],fmtword:[30660,29427,32170,30877,26159,25368,27713,26998,21522,20773,24188,23371,17913,16590,20375,19104,13663,12392,16177,14854,9396,8579,11994,11245,5769,5054,7399,6608,1890,597,3340,2107],eccblocks:[1,0,19,7,1,0,16,10,1,0,13,13,1,0,9,17,1,0,34,10,1,0,28,16,1,0,22,22,1,0,16,28,1,0,55,15,1,0,44,26,2,0,17,18,2,0,13,22,1,0,80,20,2,0,32,18,2,0,24,26,4,0,9,16,1,0,108,26,2,0,43,24,2,2,15,18,2,2,11,22,2,0,68,18,4,0,27,16,4,0,19,24,4,0,15,28,2,0,78,20,4,0,31,18,2,4,14,18,4,1,13,26,2,0,97,24,2,2,38,22,4,2,18,22,4,2,14,26,2,0,116,30,3,2,36,22,4,4,16,20,4,4,12,24,2,2,68,18,4,1,43,26,6,2,19,24,6,2,15,28,4,0,81,20,1,4,50,30,4,4,22,28,3,8,12,24,2,2,92,24,6,2,36,22,4,6,20,26,7,4,14,28,4,0,107,26,8,1,37,22,8,4,20,24,12,4,11,22,3,1,115,30,4,5,40,24,11,5,16,20,11,5,12,24,5,1,87,22,5,5,41,24,5,7,24,30,11,7,12,24,5,1,98,24,7,3,45,28,15,2,19,24,3,13,15,30,1,5,107,28,10,1,46,28,1,15,22,28,2,17,14,28,5,1,120,30,9,4,43,26,17,1,22,28,2,19,14,28,3,4,113,28,3,11,44,26,17,4,21,26,9,16,13,26,3,5,107,28,3,13,41,26,15,5,24,30,15,10,15,28,4,4,116,28,17,0,42,26,17,6,22,28,19,6,16,30,2,7,111,28,17,0,46,28,7,16,24,30,34,0,13,24,4,5,121,30,4,14,47,28,11,14,24,30,16,14,15,30,6,4,117,30,6,14,45,28,11,16,24,30,30,2,16,30,8,4,106,26,8,13,47,28,7,22,24,30,22,13,15,30,10,2,114,28,19,4,46,28,28,6,22,28,33,4,16,30,8,4,122,30,22,3,45,28,8,26,23,30,12,28,15,30,3,10,117,30,3,23,45,28,4,31,24,30,11,31,15,30,7,7,116,30,21,7,45,28,1,37,23,30,19,26,15,30,5,10,115,30,19,10,47,28,15,25,24,30,23,25,15,30,13,3,115,30,2,29,46,28,42,1,24,30,23,28,15,30,17,0,115,30,10,23,46,28,10,35,24,30,19,35,15,30,17,1,115,30,14,21,46,28,29,19,24,30,11,46,15,30,13,6,115,30,14,23,46,28,44,7,24,30,59,1,16,30,12,7,121,30,12,26,47,28,39,14,24,30,22,41,15,30,6,14,121,30,6,34,47,28,46,10,24,30,2,64,15,30,17,4,122,30,29,14,46,28,49,10,24,30,24,46,15,30,4,18,122,30,13,32,46,28,48,14,24,30,42,32,15,30,20,4,117,30,40,7,47,28,43,22,24,30,10,67,15,30,19,6,118,30,18,31,47,28,34,34,24,30,20,61,15,30],glog:[255,0,1,25,2,50,26,198,3,223,51,238,27,104,199,75,4,100,224,14,52,141,239,129,28,193,105,248,200,8,76,113,5,138,101,47,225,36,15,33,53,147,142,218,240,18,130,69,29,181,194,125,106,39,249,185,201,154,9,120,77,228,114,166,6,191,139,98,102,221,48,253,226,152,37,179,16,145,34,136,54,208,148,206,143,150,219,189,241,210,19,92,131,56,70,64,30,66,182,163,195,72,126,110,107,58,40,84,250,133,186,61,202,94,155,159,10,21,121,43,78,212,229,172,115,243,167,87,7,112,192,247,140,128,99,13,103,74,222,237,49,197,254,24,227,165,153,119,38,184,180,124,17,68,146,217,35,32,137,46,55,63,209,91,149,188,207,205,144,135,151,178,220,252,190,97,242,86,211,171,20,42,93,158,132,60,57,83,71,109,65,162,31,45,67,216,183,123,164,118,196,23,73,236,127,12,111,246,108,161,59,82,41,157,85,170,251,96,134,177,187,204,62,90,203,89,95,176,156,169,160,81,11,245,22,235,122,117,44,215,79,174,213,233,230,231,173,232,116,214,244,234,168,80,88,175],gexp:[1,2,4,8,16,32,64,128,29,58,116,232,205,135,19,38,76,152,45,90,180,117,234,201,143,3,6,12,24,48,96,192,157,39,78,156,37,74,148,53,106,212,181,119,238,193,159,35,70,140,5,10,20,40,80,160,93,186,105,210,185,111,222,161,95,190,97,194,153,47,94,188,101,202,137,15,30,60,120,240,253,231,211,187,107,214,177,127,254,225,223,163,91,182,113,226,217,175,67,134,17,34,68,136,13,26,52,104,208,189,103,206,129,31,62,124,248,237,199,147,59,118,236,197,151,51,102,204,133,23,46,92,184,109,218,169,79,158,33,66,132,21,42,84,168,77,154,41,82,164,85,170,73,146,57,114,228,213,183,115,230,209,191,99,198,145,63,126,252,229,215,179,123,246,241,255,227,219,171,75,150,49,98,196,149,55,110,220,165,87,174,65,130,25,50,100,200,141,7,14,28,56,112,224,221,167,83,166,81,162,89,178,121,242,249,239,195,155,43,86,172,69,138,9,18,36,72,144,61,122,244,245,247,243,251,235,203,139,11,22,44,88,176,125,250,233,207,131,27,54,108,216,173,71,142,0]};return function(t,n){var r=[],i=[],s=[],o=[],u=[],a,f,l,c,h,p,n=n==undefined?1:n,d=[],v=3,m=3,g=40,y=10,b;this._get_constant=function(t){return e[t]};this.getwidth=function(){return f};this.setmask=function(e,t){var n;if(e>t){n=e;e=t;t=n}n=t;n*=t;n+=t;n>>=1;n+=e;o[n]=1};this.putalign=function(e,t){var n;s[e+f*t]=1;for(n=-2;n<2;n++){s[e+n+f*(t-2)]=1;s[e-2+f*(t+n+1)]=1;s[e+2+f*(t+n)]=1;s[e+n+1+f*(t+2)]=1}for(n=0;n<2;n++){this.setmask(e-1,t+n);this.setmask(e+1,t-n);this.setmask(e-n,t-1);this.setmask(e+n,t+1)}};this.modnn=function(e){while(e>=255){e-=255;e=(e>>8)+(e&255)}return e};this.appendrs=function(e,t,n,i){var s,o,u;for(s=0;s<i;s++)r[n+s]=0;for(s=0;s<t;s++){u=this._get_constant("glog")[r[e+s]^r[n]];if(u!=255)for(o=1;o<i;o++)r[n+o-1]=r[n+o]^this._get_constant("gexp")[this.modnn(u+d[i-o])];else for(o=n;o<n+i;o++)r[o]=r[o+1];r[n+i-1]=u==255?0:this._get_constant("gexp")[this.modnn(u+d[0])]}};this.ismasked=function(e,t){var n;if(e>t){n=e;e=t;t=n}n=t;n+=t*t;n>>=1;n+=e;return o[n]};this.applymask=function(e){var t,n,r,i;switch(e){case 0:for(n=0;n<f;n++)for(t=0;t<f;t++)if(!(t+n&1)&&!this.ismasked(t,n))s[t+n*f]^=1;break;case 1:for(n=0;n<f;n++)for(t=0;t<f;t++)if(!(n&1)&&!this.ismasked(t,n))s[t+n*f]^=1;break;case 2:for(n=0;n<f;n++)for(r=0,t=0;t<f;t++,r++){if(r==3)r=0;if(!r&&!this.ismasked(t,n))s[t+n*f]^=1}break;case 3:for(i=0,n=0;n<f;n++,i++){if(i==3)i=0;for(r=i,t=0;t<f;t++,r++){if(r==3)r=0;if(!r&&!this.ismasked(t,n))s[t+n*f]^=1}}break;case 4:for(n=0;n<f;n++)for(r=0,i=n>>1&1,t=0;t<f;t++,r++){if(r==3){r=0;i=!i}if(!i&&!this.ismasked(t,n))s[t+n*f]^=1}break;case 5:for(i=0,n=0;n<f;n++,i++){if(i==3)i=0;for(r=0,t=0;t<f;t++,r++){if(r==3)r=0;if(!((t&n&1)+!(!r|!i))&&!this.ismasked(t,n))s[t+n*f]^=1}}break;case 6:for(i=0,n=0;n<f;n++,i++){if(i==3)i=0;for(r=0,t=0;t<f;t++,r++){if(r==3)r=0;if(!((t&n&1)+(r&&r==i)&1)&&!this.ismasked(t,n))s[t+n*f]^=1}}break;case 7:for(i=0,n=0;n<f;n++,i++){if(i==3)i=0;for(r=0,t=0;t<f;t++,r++){if(r==3)r=0;if(!((r&&r==i)+(t+n&1)&1)&&!this.ismasked(t,n))s[t+n*f]^=1}}break}return};this.badruns=function(e){var t,n=0;for(t=0;t<=e;t++)if(u[t]>=5)n+=v+u[t]-5;for(t=3;t<e-1;t+=2)if(u[t-2]==u[t+2]&&u[t+2]==u[t-1]&&u[t-1]==u[t+1]&&u[t-1]*3==u[t]&&(u[t-3]==0||t+3>e||u[t-3]*3>=u[t]*4||u[t+3]*3>=u[t]*4))n+=g;return n};this.badcheck=function(){var e,t,n,r,i,o=0,a=0;for(t=0;t<f-1;t++)for(e=0;e<f-1;e++)if(s[e+f*t]&&s[e+1+f*t]&&s[e+f*(t+1)]&&s[e+1+f*(t+1)]||!(s[e+f*t]||s[e+1+f*t]||s[e+f*(t+1)]||s[e+1+f*(t+1)]))o+=m;for(t=0;t<f;t++){u[0]=0;for(n=r=e=0;e<f;e++){if((i=s[e+f*t])==r)u[n]++;else u[++n]=1;r=i;a+=r?1:-1}o+=this.badruns(n)}if(a<0)a=-a;var l=a;count=0;l+=l<<2;l<<=1;while(l>f*f)l-=f*f,count++;o+=count*y;for(e=0;e<f;e++){u[0]=0;for(n=r=t=0;t<f;t++){if((i=s[e+f*t])==r)u[n]++;else u[++n]=1;r=i}o+=this.badruns(n)}return o};this.genframe=function(){var e,u,v,m,g,y,b,w;m=t.length;a=0;do{a++;v=(n-1)*4+(a-1)*16;l=this._get_constant("eccblocks")[v++];c=this._get_constant("eccblocks")[v++];h=this._get_constant("eccblocks")[v++];p=this._get_constant("eccblocks")[v];v=h*(l+c)+c-3+(a<=9);if(m<=v)break}while(a<40);f=17+4*a;g=h+(h+p)*(l+c)+c;for(m=0;m<g;m++)i[m]=0;r=t.slice(0);for(m=0;m<f*f;m++)s[m]=0;for(m=0;m<(f*(f+1)+1)/2;m++)o[m]=0;for(m=0;m<3;m++){v=0;u=0;if(m==1)v=f-7;if(m==2)u=f-7;s[u+3+f*(v+3)]=1;for(e=0;e<6;e++){s[u+e+f*v]=1;s[u+f*(v+e+1)]=1;s[u+6+f*(v+e)]=1;s[u+e+1+f*(v+6)]=1}for(e=1;e<5;e++){this.setmask(u+e,v+1);this.setmask(u+1,v+e+1);this.setmask(u+5,v+e);this.setmask(u+e+1,v+5)}for(e=2;e<4;e++){s[u+e+f*(v+2)]=1;s[u+2+f*(v+e+1)]=1;s[u+4+f*(v+e)]=1;s[u+e+1+f*(v+4)]=1}}if(a>1){m=this._get_constant("adelta")[a];u=f-7;for(;;){e=f-7;while(e>m-3){this.putalign(e,u);if(e<m)break;e-=m}if(u<=m+9)break;u-=m;this.putalign(6,u);this.putalign(u,6)}}s[8+f*(f-8)]=1;for(u=0;u<7;u++){this.setmask(7,u);this.setmask(f-8,u);this.setmask(7,u+f-7)}for(e=0;e<8;e++){this.setmask(e,7);this.setmask(e+f-8,7);this.setmask(e,f-8)}for(e=0;e<9;e++)this.setmask(e,8);for(e=0;e<8;e++){this.setmask(e+f-8,8);this.setmask(8,e)}for(u=0;u<7;u++)this.setmask(8,u+f-7);for(e=0;e<f-14;e++)if(e&1){this.setmask(8+e,6);this.setmask(6,8+e)}else{s[8+e+f*6]=1;s[6+f*(8+e)]=1}if(a>6){m=this._get_constant("vpat")[a-7];v=17;for(e=0;e<6;e++)for(u=0;u<3;u++,v--)if(1&(v>11?a>>v-12:m>>v)){s[5-e+f*(2-u+f-11)]=1;s[2-u+f-11+f*(5-e)]=1}else{this.setmask(5-e,2-u+f-11);this.setmask(2-u+f-11,5-e)}}for(u=0;u<f;u++)for(e=0;e<=u;e++)if(s[e+f*u])this.setmask(e,u);g=r.length;for(y=0;y<g;y++)i[y]=r.charCodeAt(y);r=i.slice(0);e=h*(l+c)+c;if(g>=e-2){g=e-2;if(a>9)g--}y=g;if(a>9){r[y+2]=0;r[y+3]=0;while(y--){m=r[y];r[y+3]|=255&m<<4;r[y+2]=m>>4}r[2]|=255&g<<4;r[1]=g>>4;r[0]=64|g>>12}else{r[y+1]=0;r[y+2]=0;while(y--){m=r[y];r[y+2]|=255&m<<4;r[y+1]=m>>4}r[1]|=255&g<<4;r[0]=64|g>>4}y=g+3-(a<10);while(y<e){r[y++]=236;r[y++]=17}d[0]=1;for(y=0;y<p;y++){d[y+1]=1;for(b=y;b>0;b--)d[b]=d[b]?d[b-1]^this._get_constant("gexp")[this.modnn(this._get_constant("glog")[d[b]]+y)]:d[b-1];d[0]=this._get_constant("gexp")[this.modnn(this._get_constant("glog")[d[0]]+y)]}for(y=0;y<=p;y++)d[y]=this._get_constant("glog")[d[y]];v=e;u=0;for(y=0;y<l;y++){this.appendrs(u,h,v,p);u+=h;v+=p}for(y=0;y<c;y++){this.appendrs(u,h+1,v,p);u+=h+1;v+=p}u=0;for(y=0;y<h;y++){for(b=0;b<l;b++)i[u++]=r[y+b*h];for(b=0;b<c;b++)i[u++]=r[l*h+y+b*(h+1)]}for(b=0;b<c;b++)i[u++]=r[l*h+y+b*(h+1)];for(y=0;y<p;y++)for(b=0;b<l+c;b++)i[u++]=r[e+y+b*p];r=i;e=u=f-1;v=g=1;w=(h+p)*(l+c)+c;for(y=0;y<w;y++){m=r[y];for(b=0;b<8;b++,m<<=1){if(128&m)s[e+f*u]=1;do{if(g)e--;else{e++;if(v){if(u!=0)u--;else{e-=2;v=!v;if(e==6){e--;u=9}}}else{if(u!=f-1)u++;else{e-=2;v=!v;if(e==6){e--;u-=8}}}}g=!g}while(this.ismasked(e,u))}}r=s.slice(0);m=0;u=3e4;for(v=0;v<8;v++){this.applymask(v);e=this.badcheck();if(e<u){u=e;m=v}if(m==7)break;s=r.slice(0)}if(m!=v)this.applymask(m);u=this._get_constant("fmtword")[m+(n-1<<3)];for(v=0;v<8;v++,u>>=1)if(u&1){s[f-1-v+f*8]=1;if(v<6)s[8+f*v]=1;else s[8+f*(v+1)]=1}for(v=0;v<7;v++,u>>=1)if(u&1){s[8+f*(f-7+v)]=1;if(v)s[6-v+f*8]=1;else s[7+f*8]=1}return s};this.data=function(){var e,t;var n=[];for(e=0;e<f;e++){n.push(b.slice(e*f,f+e*f))}return n};b=this.genframe(t)}}();Qrcodesvg.drawMethods={classic:function(e,t,n){t["radius"]=0;this.round(e,t,n)},round:function(e,t,n){var r=t["dot"].x*t["px"]+t["frame_size"],i=t["dot"].y*t["px"]+t["frame_size"],s=t["px"],o=s,u=t["dot"].corners||[true,true,true,true],a=t["radius"]!=undefined?t["radius"]:5,f=" M ";f+=(u[0]?r+a:r)+","+i+" L ";f+=(u[1]?r+s-a:r+s)+","+i;f+=!u[1]?" L ":" Q "+(r+s)+","+i+" "+(r+s)+","+(i+a)+" L ";f+=r+s+","+(u[2]?i+o-a:i+o);f+=!u[2]?" L ":" Q "+(r+s)+","+(i+o)+" "+(r+s-a)+","+(i+o)+" L ";f+=(u[3]?r+a:r)+","+(i+o);f+=!u[3]?" L ":" Q "+r+","+(i+o)+" "+r+","+(i+o-a)+" L ";f+=r+","+(u[0]?i+a:i);f+=!u[0]?"":" Q "+r+","+i+" "+(r+a)+","+i;f+=" Z";e.path(f).attr(n)},bevel:function(e,t,n){var r=t["dot"].x*t["px"]+t["frame_size"],i=t["dot"].y*t["px"]+t["frame_size"],s=t["px"],o=s,u=t["radius"]!=undefined?t["radius"]:5,a=t["dot"].corners||[true,true,true,true],f=[],l=0,c=" M ";f.push([a[0]?r+u:r,i]);f.push([a[1]?r+s-u:r+s,i]);if(a[1]){f.push([r+s,i+u])}f.push([r+s,a[2]?i+o-u:i+o]);if(a[2]){f.push([r+s-u,i+o])}f.push([a[3]?r+u:r,i+o]);if(a[3]){f.push([r,i+o-u])}f.push([r,a[0]?i+u:i]);if(a[0]){f.push([r+u,i])}for(;l<f.length;l++){c+=f[l][0]+" "+f[l][1]+" ";c+=l!=f.length-1?"L":"Z"}e.path(c).attr(n)}}