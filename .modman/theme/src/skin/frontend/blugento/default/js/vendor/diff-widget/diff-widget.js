/*! blugento-theme v1.0.0 - 2023-01-24 20:07:47 */
;!function(a){a.fn.diffWidget=function(b){function c(a,b){b.indexOf(".")!=-1?a.css("background-image","url("+b+")"):a.css("background-color",b)}var d={width:"900px",height:"300px",top:"red",bottom:"blue",position:"50%"},b=a.extend({},d,b),e=a('<div class="diffWidget"><div class="wrapper"><div class="first"></div><div class="second"></div></div></div>'),f=e.find(".wrapper"),g=f.find(".first"),h=f.find(".second");c(g,b.top),c(h,b.bottom),e.css("width",b.width),e.css("height",b.height),g.width(b.position),f.on("mousemove",function(a){g.width(a.offsetX)}),a(this).append(e)}}(jQuery);;
