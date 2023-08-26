/*! blugento-theme v1.0.0 - 2023-01-24 20:07:47 */
;"function"!=typeof Object.create&&(Object.create=function(a){function b(){}return b.prototype=a,new b}),function(a,b,c,d){var e={init:function(b,c){var d=this;d.elem=c,d.$elem=a(c),d.imageSrc=d.$elem.data("zoom-image")?d.$elem.data("zoom-image"):d.$elem.attr("src"),d.options=a.extend({},a.fn.elevateZoom.options,b),d.options.tint&&(d.options.lensColour="none",d.options.lensOpacity="1"),"inner"==d.options.zoomType&&(d.options.showLens=!1),d.$elem.parent().removeAttr("title").removeAttr("alt"),d.zoomImage=d.imageSrc,d.refresh(1),a("#"+d.options.gallery+" a").click(function(b){return d.options.galleryActiveClass&&(a("#"+d.options.gallery+" a").removeClass(d.options.galleryActiveClass),a(this).addClass(d.options.galleryActiveClass)),b.preventDefault(),a(this).data("zoom-image")?d.zoomImagePre=a(this).data("zoom-image"):d.zoomImagePre=a(this).data("image"),d.swaptheimage(a(this).data("image"),d.zoomImagePre),!1})},refresh:function(a){var b=this;setTimeout(function(){b.fetch(b.imageSrc)},a||b.options.refresh)},fetch:function(a){var b=this,c=new Image;c.onload=function(){b.largeWidth=c.width,b.largeHeight=c.height,b.startZoom(),b.currentImage=b.imageSrc,b.options.onZoomedImageLoaded(b.$elem)},c.src=a},startZoom:function(){var b=this;if(b.nzWidth=b.$elem.width(),b.nzHeight=b.$elem.height(),b.isWindowActive=!1,b.isLensActive=!1,b.isTintActive=!1,b.overWindow=!1,b.options.imageCrossfade&&(b.zoomWrap=b.$elem.wrap('<div style="height:'+b.nzHeight+"px;width:"+b.nzWidth+'px;" class="zoomWrapper" />'),b.$elem.css("position","absolute")),b.zoomLock=1,b.scrollingLock=!1,b.changeBgSize=!1,b.currentZoomLevel=b.options.zoomLevel,b.nzOffset=b.$elem.offset(),b.widthRatio=b.largeWidth/b.currentZoomLevel/b.nzWidth,b.heightRatio=b.largeHeight/b.currentZoomLevel/b.nzHeight,"window"==b.options.zoomType&&(b.zoomWindowStyle="overflow: hidden;background-position: 0px 0px;text-align:center;background-color: "+String(b.options.zoomWindowBgColour)+";width: "+String(b.options.zoomWindowWidth)+"px;height: "+String(b.options.zoomWindowHeight)+"px;float: left;background-size: "+b.largeWidth/b.currentZoomLevel+"px "+b.largeHeight/b.currentZoomLevel+"px;display: none;z-index:100;border: "+String(b.options.borderSize)+"px solid "+b.options.borderColour+";background-repeat: no-repeat;position: absolute;"),"inner"==b.options.zoomType){var c=b.$elem.css("border-left-width");b.zoomWindowStyle="overflow: hidden;margin-left: "+String(c)+";margin-top: "+String(c)+";background-position: 0px 0px;width: "+String(b.nzWidth)+"px;height: "+String(b.nzHeight)+"px;px;float: left;display: none;cursor:"+b.options.cursor+";px solid "+b.options.borderColour+";background-repeat: no-repeat;position: absolute;"}"window"==b.options.zoomType&&(b.nzHeight<b.options.zoomWindowWidth/b.widthRatio?lensHeight=b.nzHeight:lensHeight=String(b.options.zoomWindowHeight/b.heightRatio),b.largeWidth<b.options.zoomWindowWidth?lensWidth=b.nzWidth:lensWidth=b.options.zoomWindowWidth/b.widthRatio,b.lensStyle="background-position: 0px 0px;width: "+String(b.options.zoomWindowWidth/b.widthRatio)+"px;height: "+String(b.options.zoomWindowHeight/b.heightRatio)+"px;float: right;display: none;overflow: hidden;z-index: 999;-webkit-transform: translateZ(0);opacity:"+b.options.lensOpacity+";filter: alpha(opacity = "+100*b.options.lensOpacity+"); zoom:1;width:"+lensWidth+"px;height:"+lensHeight+"px;background-color:"+b.options.lensColour+";cursor:"+b.options.cursor+";border: "+b.options.lensBorderSize+"px solid "+b.options.lensBorderColour+";background-repeat: no-repeat;position: absolute;"),b.tintStyle="display: block;position: absolute;background-color: "+b.options.tintColour+";filter:alpha(opacity=0);opacity: 0;width: "+b.nzWidth+"px;height: "+b.nzHeight+"px;",b.lensRound="","lens"==b.options.zoomType&&(b.lensStyle="background-position: 0px 0px;float: left;display: none;border: "+String(b.options.borderSize)+"px solid "+b.options.borderColour+";width:"+String(b.options.lensSize)+"px;height:"+String(b.options.lensSize)+"px;background-repeat: no-repeat;position: absolute;"),"round"==b.options.lensShape&&(b.lensRound="border-top-left-radius: "+String(b.options.lensSize/2+b.options.borderSize)+"px;border-top-right-radius: "+String(b.options.lensSize/2+b.options.borderSize)+"px;border-bottom-left-radius: "+String(b.options.lensSize/2+b.options.borderSize)+"px;border-bottom-right-radius: "+String(b.options.lensSize/2+b.options.borderSize)+"px;"),b.zoomContainer=a('<div class="zoomContainer" style="-webkit-transform: translateZ(0);position:absolute;left:'+b.nzOffset.left+"px;top:"+b.nzOffset.top+"px;height:"+b.nzHeight+"px;width:"+b.nzWidth+'px;"></div>'),a("body").append(b.zoomContainer),b.options.containLensZoom&&"lens"==b.options.zoomType&&b.zoomContainer.css("overflow","hidden"),"inner"!=b.options.zoomType&&(b.zoomLens=a("<div class='zoomLens' style='"+b.lensStyle+b.lensRound+"'>&nbsp;</div>").appendTo(b.zoomContainer).click(function(){b.$elem.trigger("click")}),b.options.tint&&(b.tintContainer=a("<div/>").addClass("tintContainer"),b.zoomTint=a("<div class='zoomTint' style='"+b.tintStyle+"'></div>"),b.zoomLens.wrap(b.tintContainer),b.zoomTintcss=b.zoomLens.after(b.zoomTint),b.zoomTintImage=a('<img style="position: absolute; left: 0px; top: 0px; max-width: none; width: '+b.nzWidth+"px; height: "+b.nzHeight+'px;" src="'+b.imageSrc+'">').appendTo(b.zoomLens).click(function(){b.$elem.trigger("click")}))),isNaN(b.options.zoomWindowPosition)?b.zoomWindow=a("<div style='z-index:999;left:"+b.windowOffsetLeft+"px;top:"+b.windowOffsetTop+"px;"+b.zoomWindowStyle+"' class='zoomWindow'>&nbsp;</div>").appendTo("body").click(function(){b.$elem.trigger("click")}):b.zoomWindow=a("<div style='z-index:999;left:"+b.windowOffsetLeft+"px;top:"+b.windowOffsetTop+"px;"+b.zoomWindowStyle+"' class='zoomWindow'>&nbsp;</div>").appendTo(b.zoomContainer).click(function(){b.$elem.trigger("click")}),b.zoomWindowContainer=a("<div/>").addClass("zoomWindowContainer").css("width",b.options.zoomWindowWidth),b.zoomWindow.wrap(b.zoomWindowContainer),"lens"==b.options.zoomType&&b.zoomLens.css({backgroundImage:"url('"+b.imageSrc+"')"}),"window"==b.options.zoomType&&b.zoomWindow.css({backgroundImage:"url('"+b.imageSrc+"')"}),"inner"==b.options.zoomType&&b.zoomWindow.css({backgroundImage:"url('"+b.imageSrc+"')"}),b.$elem.bind("touchmove",function(a){a.preventDefault();var c=a.originalEvent.touches[0]||a.originalEvent.changedTouches[0];b.setPosition(c)}),b.zoomContainer.bind("touchmove",function(a){"inner"==b.options.zoomType&&b.showHideWindow("show"),a.preventDefault();var c=a.originalEvent.touches[0]||a.originalEvent.changedTouches[0];b.setPosition(c)}),b.zoomContainer.bind("touchend",function(a){b.showHideWindow("hide"),b.options.showLens&&b.showHideLens("hide"),b.options.tint&&"inner"!=b.options.zoomType&&b.showHideTint("hide")}),b.$elem.bind("touchend",function(a){b.showHideWindow("hide"),b.options.showLens&&b.showHideLens("hide"),b.options.tint&&"inner"!=b.options.zoomType&&b.showHideTint("hide")}),b.options.showLens&&(b.zoomLens.bind("touchmove",function(a){a.preventDefault();var c=a.originalEvent.touches[0]||a.originalEvent.changedTouches[0];b.setPosition(c)}),b.zoomLens.bind("touchend",function(a){b.showHideWindow("hide"),b.options.showLens&&b.showHideLens("hide"),b.options.tint&&"inner"!=b.options.zoomType&&b.showHideTint("hide")})),b.$elem.bind("mousemove",function(a){0==b.overWindow&&b.setElements("show"),b.lastX===a.clientX&&b.lastY===a.clientY||(b.setPosition(a),b.currentLoc=a),b.lastX=a.clientX,b.lastY=a.clientY}),b.zoomContainer.bind("mousemove",function(a){0==b.overWindow&&b.setElements("show"),b.lastX===a.clientX&&b.lastY===a.clientY||(b.setPosition(a),b.currentLoc=a),b.lastX=a.clientX,b.lastY=a.clientY}),"inner"!=b.options.zoomType&&b.zoomLens.bind("mousemove",function(a){b.lastX===a.clientX&&b.lastY===a.clientY||(b.setPosition(a),b.currentLoc=a),b.lastX=a.clientX,b.lastY=a.clientY}),b.options.tint&&"inner"!=b.options.zoomType&&b.zoomTint.bind("mousemove",function(a){b.lastX===a.clientX&&b.lastY===a.clientY||(b.setPosition(a),b.currentLoc=a),b.lastX=a.clientX,b.lastY=a.clientY}),"inner"==b.options.zoomType&&b.zoomWindow.bind("mousemove",function(a){b.lastX===a.clientX&&b.lastY===a.clientY||(b.setPosition(a),b.currentLoc=a),b.lastX=a.clientX,b.lastY=a.clientY}),b.zoomContainer.add(b.$elem).mouseenter(function(){0==b.overWindow&&b.setElements("show")}).mouseleave(function(){b.scrollLock||(b.setElements("hide"),b.options.onDestroy(b.$elem))}),"inner"!=b.options.zoomType&&b.zoomWindow.mouseenter(function(){b.overWindow=!0,b.setElements("hide")}).mouseleave(function(){b.overWindow=!1}),1!=b.options.zoomLevel,b.options.minZoomLevel?b.minZoomLevel=b.options.minZoomLevel:b.minZoomLevel=2*b.options.scrollZoomIncrement,b.options.scrollZoom&&b.zoomContainer.add(b.$elem).bind("mousewheel DOMMouseScroll MozMousePixelScroll",function(c){b.scrollLock=!0,clearTimeout(a.data(this,"timer")),a.data(this,"timer",setTimeout(function(){b.scrollLock=!1},250));var d=c.originalEvent.wheelDelta||c.originalEvent.detail*-1;return c.stopImmediatePropagation(),c.stopPropagation(),c.preventDefault(),d/120>0?b.currentZoomLevel>=b.minZoomLevel&&b.changeZoomLevel(b.currentZoomLevel-b.options.scrollZoomIncrement):b.options.maxZoomLevel?b.currentZoomLevel<=b.options.maxZoomLevel&&b.changeZoomLevel(parseFloat(b.currentZoomLevel)+b.options.scrollZoomIncrement):b.changeZoomLevel(parseFloat(b.currentZoomLevel)+b.options.scrollZoomIncrement),!1})},setElements:function(a){var b=this;return!!b.options.zoomEnabled&&("show"==a&&b.isWindowSet&&("inner"==b.options.zoomType&&b.showHideWindow("show"),"window"==b.options.zoomType&&b.showHideWindow("show"),b.options.showLens&&b.showHideLens("show"),b.options.tint&&"inner"!=b.options.zoomType&&b.showHideTint("show")),void("hide"==a&&("window"==b.options.zoomType&&b.showHideWindow("hide"),b.options.tint||b.showHideWindow("hide"),b.options.showLens&&b.showHideLens("hide"),b.options.tint&&b.showHideTint("hide"))))},setPosition:function(a){var b=this;return!!b.options.zoomEnabled&&(b.nzHeight=b.$elem.height(),b.nzWidth=b.$elem.width(),b.nzOffset=b.$elem.offset(),b.options.tint&&"inner"!=b.options.zoomType&&(b.zoomTint.css({top:0}),b.zoomTint.css({left:0})),b.options.responsive&&!b.options.scrollZoom&&b.options.showLens&&(b.nzHeight<b.options.zoomWindowWidth/b.widthRatio?lensHeight=b.nzHeight:lensHeight=String(b.options.zoomWindowHeight/b.heightRatio),b.largeWidth<b.options.zoomWindowWidth?lensWidth=b.nzWidth:lensWidth=b.options.zoomWindowWidth/b.widthRatio,b.widthRatio=b.largeWidth/b.nzWidth,b.heightRatio=b.largeHeight/b.nzHeight,"lens"!=b.options.zoomType&&(b.nzHeight<b.options.zoomWindowWidth/b.widthRatio?lensHeight=b.nzHeight:lensHeight=String(b.options.zoomWindowHeight/b.heightRatio),b.nzWidth<b.options.zoomWindowHeight/b.heightRatio?lensWidth=b.nzWidth:lensWidth=String(b.options.zoomWindowWidth/b.widthRatio),b.zoomLens.css("width",lensWidth),b.zoomLens.css("height",lensHeight),b.options.tint&&(b.zoomTintImage.css("width",b.nzWidth),b.zoomTintImage.css("height",b.nzHeight))),"lens"==b.options.zoomType&&b.zoomLens.css({width:String(b.options.lensSize)+"px",height:String(b.options.lensSize)+"px"})),b.zoomContainer.css({top:b.nzOffset.top}),b.zoomContainer.css({left:b.nzOffset.left}),b.mouseLeft=parseInt(a.pageX-b.nzOffset.left),b.mouseTop=parseInt(a.pageY-b.nzOffset.top),"window"==b.options.zoomType&&(b.Etoppos=b.mouseTop<b.zoomLens.height()/2,b.Eboppos=b.mouseTop>b.nzHeight-b.zoomLens.height()/2-2*b.options.lensBorderSize,b.Eloppos=b.mouseLeft<0+b.zoomLens.width()/2,b.Eroppos=b.mouseLeft>b.nzWidth-b.zoomLens.width()/2-2*b.options.lensBorderSize),"inner"==b.options.zoomType&&(b.Etoppos=b.mouseTop<b.nzHeight/2/b.heightRatio,b.Eboppos=b.mouseTop>b.nzHeight-b.nzHeight/2/b.heightRatio,b.Eloppos=b.mouseLeft<0+b.nzWidth/2/b.widthRatio,b.Eroppos=b.mouseLeft>b.nzWidth-b.nzWidth/2/b.widthRatio-2*b.options.lensBorderSize),b.mouseLeft<0||b.mouseTop<0||b.mouseLeft>b.nzWidth||b.mouseTop>b.nzHeight?void b.setElements("hide"):(b.options.showLens&&(b.lensLeftPos=String(Math.floor(b.mouseLeft-b.zoomLens.width()/2)),b.lensTopPos=String(Math.floor(b.mouseTop-b.zoomLens.height()/2))),b.Etoppos&&(b.lensTopPos=0),b.Eloppos&&(b.windowLeftPos=0,b.lensLeftPos=0,b.tintpos=0),"window"==b.options.zoomType&&(b.Eboppos&&(b.lensTopPos=Math.max(b.nzHeight-b.zoomLens.height()-2*b.options.lensBorderSize,0)),b.Eroppos&&(b.lensLeftPos=b.nzWidth-b.zoomLens.width()-2*b.options.lensBorderSize)),"inner"==b.options.zoomType&&(b.Eboppos&&(b.lensTopPos=Math.max(b.nzHeight-2*b.options.lensBorderSize,0)),b.Eroppos&&(b.lensLeftPos=b.nzWidth-b.nzWidth-2*b.options.lensBorderSize)),"lens"==b.options.zoomType&&(b.windowLeftPos=String(((a.pageX-b.nzOffset.left)*b.widthRatio-b.zoomLens.width()/2)*-1),b.windowTopPos=String(((a.pageY-b.nzOffset.top)*b.heightRatio-b.zoomLens.height()/2)*-1),b.zoomLens.css({backgroundPosition:b.windowLeftPos+"px "+b.windowTopPos+"px"}),b.changeBgSize&&(b.nzHeight>b.nzWidth?("lens"==b.options.zoomType&&b.zoomLens.css({"background-size":b.largeWidth/b.newvalueheight+"px "+b.largeHeight/b.newvalueheight+"px"}),b.zoomWindow.css({"background-size":b.largeWidth/b.newvalueheight+"px "+b.largeHeight/b.newvalueheight+"px"})):("lens"==b.options.zoomType&&b.zoomLens.css({"background-size":b.largeWidth/b.newvaluewidth+"px "+b.largeHeight/b.newvaluewidth+"px"}),b.zoomWindow.css({"background-size":b.largeWidth/b.newvaluewidth+"px "+b.largeHeight/b.newvaluewidth+"px"})),b.changeBgSize=!1),b.setWindowPostition(a)),b.options.tint&&"inner"!=b.options.zoomType&&b.setTintPosition(a),"window"==b.options.zoomType&&b.setWindowPostition(a),"inner"==b.options.zoomType&&b.setWindowPostition(a),b.options.showLens&&(b.fullwidth&&"lens"!=b.options.zoomType&&(b.lensLeftPos=0),b.zoomLens.css({left:b.lensLeftPos+"px",top:b.lensTopPos+"px"})),void 0))},showHideWindow:function(a){var b=this;"show"==a&&(b.isWindowActive||(b.options.zoomWindowFadeIn?b.zoomWindow.stop(!0,!0,!1).fadeIn(b.options.zoomWindowFadeIn):b.zoomWindow.show(),b.isWindowActive=!0)),"hide"==a&&b.isWindowActive&&(b.options.zoomWindowFadeOut?b.zoomWindow.stop(!0,!0).fadeOut(b.options.zoomWindowFadeOut,function(){b.loop&&(clearInterval(b.loop),b.loop=!1)}):b.zoomWindow.hide(),b.isWindowActive=!1)},showHideLens:function(a){var b=this;"show"==a&&(b.isLensActive||(b.options.lensFadeIn?b.zoomLens.stop(!0,!0,!1).fadeIn(b.options.lensFadeIn):b.zoomLens.show(),b.isLensActive=!0)),"hide"==a&&b.isLensActive&&(b.options.lensFadeOut?b.zoomLens.stop(!0,!0).fadeOut(b.options.lensFadeOut):b.zoomLens.hide(),b.isLensActive=!1)},showHideTint:function(a){var b=this;"show"==a&&(b.isTintActive||(b.options.zoomTintFadeIn?b.zoomTint.css({opacity:b.options.tintOpacity}).animate().stop(!0,!0).fadeIn("slow"):(b.zoomTint.css({opacity:b.options.tintOpacity}).animate(),b.zoomTint.show()),b.isTintActive=!0)),"hide"==a&&b.isTintActive&&(b.options.zoomTintFadeOut?b.zoomTint.stop(!0,!0).fadeOut(b.options.zoomTintFadeOut):b.zoomTint.hide(),b.isTintActive=!1)},setLensPostition:function(a){},setWindowPostition:function(b){var c=this;if(isNaN(c.options.zoomWindowPosition))c.externalContainer=a("#"+c.options.zoomWindowPosition),c.externalContainerWidth=c.externalContainer.width(),c.externalContainerHeight=c.externalContainer.height(),c.externalContainerOffset=c.externalContainer.offset(),c.windowOffsetTop=c.externalContainerOffset.top,c.windowOffsetLeft=c.externalContainerOffset.left;else switch(c.options.zoomWindowPosition){case 1:c.windowOffsetTop=c.options.zoomWindowOffety,c.windowOffsetLeft=+c.nzWidth;break;case 2:c.options.zoomWindowHeight>c.nzHeight&&(c.windowOffsetTop=(c.options.zoomWindowHeight/2-c.nzHeight/2)*-1,c.windowOffsetLeft=c.nzWidth);break;case 3:c.windowOffsetTop=c.nzHeight-c.zoomWindow.height()-2*c.options.borderSize,c.windowOffsetLeft=c.nzWidth;break;case 4:c.windowOffsetTop=c.nzHeight,c.windowOffsetLeft=c.nzWidth;break;case 5:c.windowOffsetTop=c.nzHeight,c.windowOffsetLeft=c.nzWidth-c.zoomWindow.width()-2*c.options.borderSize;break;case 6:c.options.zoomWindowHeight>c.nzHeight&&(c.windowOffsetTop=c.nzHeight,c.windowOffsetLeft=(c.options.zoomWindowWidth/2-c.nzWidth/2+2*c.options.borderSize)*-1);break;case 7:c.windowOffsetTop=c.nzHeight,c.windowOffsetLeft=0;break;case 8:c.windowOffsetTop=c.nzHeight,c.windowOffsetLeft=(c.zoomWindow.width()+2*c.options.borderSize)*-1;break;case 9:c.windowOffsetTop=c.nzHeight-c.zoomWindow.height()-2*c.options.borderSize,c.windowOffsetLeft=(c.zoomWindow.width()+2*c.options.borderSize)*-1;break;case 10:c.options.zoomWindowHeight>c.nzHeight&&(c.windowOffsetTop=(c.options.zoomWindowHeight/2-c.nzHeight/2)*-1,c.windowOffsetLeft=(c.zoomWindow.width()+2*c.options.borderSize)*-1);break;case 11:c.windowOffsetTop=c.options.zoomWindowOffety,c.windowOffsetLeft=(c.zoomWindow.width()+2*c.options.borderSize)*-1;break;case 12:c.windowOffsetTop=(c.zoomWindow.height()+2*c.options.borderSize)*-1,c.windowOffsetLeft=(c.zoomWindow.width()+2*c.options.borderSize)*-1;break;case 13:c.windowOffsetTop=(c.zoomWindow.height()+2*c.options.borderSize)*-1,c.windowOffsetLeft=0;break;case 14:c.options.zoomWindowHeight>c.nzHeight&&(c.windowOffsetTop=(c.zoomWindow.height()+2*c.options.borderSize)*-1,c.windowOffsetLeft=(c.options.zoomWindowWidth/2-c.nzWidth/2+2*c.options.borderSize)*-1);break;case 15:c.windowOffsetTop=(c.zoomWindow.height()+2*c.options.borderSize)*-1,c.windowOffsetLeft=c.nzWidth-c.zoomWindow.width()-2*c.options.borderSize;break;case 16:c.windowOffsetTop=(c.zoomWindow.height()+2*c.options.borderSize)*-1,c.windowOffsetLeft=c.nzWidth;break;default:c.windowOffsetTop=c.options.zoomWindowOffety,c.windowOffsetLeft=c.nzWidth}c.isWindowSet=!0,c.windowOffsetTop=c.windowOffsetTop+c.options.zoomWindowOffety,c.windowOffsetLeft=c.windowOffsetLeft+c.options.zoomWindowOffetx,c.zoomWindow.css({top:c.windowOffsetTop}),c.zoomWindow.css({left:c.windowOffsetLeft}),"inner"==c.options.zoomType&&(c.zoomWindow.css({top:0}),c.zoomWindow.css({left:0})),c.windowLeftPos=String(((b.pageX-c.nzOffset.left)*c.widthRatio-c.zoomWindow.width()/2)*-1),c.windowTopPos=String(((b.pageY-c.nzOffset.top)*c.heightRatio-c.zoomWindow.height()/2)*-1),c.Etoppos&&(c.windowTopPos=0),c.Eloppos&&(c.windowLeftPos=0),c.Eboppos&&(c.windowTopPos=(c.largeHeight/c.currentZoomLevel-c.zoomWindow.height())*-1),c.Eroppos&&(c.windowLeftPos=(c.largeWidth/c.currentZoomLevel-c.zoomWindow.width())*-1),c.fullheight&&(c.windowTopPos=0),c.fullwidth&&(c.windowLeftPos=0),"window"!=c.options.zoomType&&"inner"!=c.options.zoomType||(1==c.zoomLock&&(c.widthRatio<=1&&(c.windowLeftPos=0),c.heightRatio<=1&&(c.windowTopPos=0)),"window"==c.options.zoomType&&(c.largeHeight<c.options.zoomWindowHeight&&(c.windowTopPos=0),c.largeWidth<c.options.zoomWindowWidth&&(c.windowLeftPos=0)),c.options.easing?(c.xp||(c.xp=0),c.yp||(c.yp=0),c.loop||(c.loop=setInterval(function(){c.xp+=(c.windowLeftPos-c.xp)/c.options.easingAmount,c.yp+=(c.windowTopPos-c.yp)/c.options.easingAmount,c.scrollingLock?(clearInterval(c.loop),c.xp=c.windowLeftPos,c.yp=c.windowTopPos,c.xp=((b.pageX-c.nzOffset.left)*c.widthRatio-c.zoomWindow.width()/2)*-1,c.yp=((b.pageY-c.nzOffset.top)*c.heightRatio-c.zoomWindow.height()/2)*-1,c.changeBgSize&&(c.nzHeight>c.nzWidth?("lens"==c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"}),c.zoomWindow.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"})):("lens"!=c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvalueheight+"px"}),c.zoomWindow.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvaluewidth+"px"})),c.changeBgSize=!1),c.zoomWindow.css({backgroundPosition:c.windowLeftPos+"px "+c.windowTopPos+"px"}),c.scrollingLock=!1,c.loop=!1):Math.round(Math.abs(c.xp-c.windowLeftPos)+Math.abs(c.yp-c.windowTopPos))<1?(clearInterval(c.loop),c.zoomWindow.css({backgroundPosition:c.windowLeftPos+"px "+c.windowTopPos+"px"}),c.loop=!1):(c.changeBgSize&&(c.nzHeight>c.nzWidth?("lens"==c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"}),c.zoomWindow.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"})):("lens"!=c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvaluewidth+"px"}),c.zoomWindow.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvaluewidth+"px"})),c.changeBgSize=!1),c.zoomWindow.css({backgroundPosition:c.xp+"px "+c.yp+"px"}))},16))):(c.changeBgSize&&(c.nzHeight>c.nzWidth?("lens"==c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"}),c.zoomWindow.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"})):("lens"==c.options.zoomType&&c.zoomLens.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvaluewidth+"px"}),c.largeHeight/c.newvaluewidth<c.options.zoomWindowHeight?c.zoomWindow.css({"background-size":c.largeWidth/c.newvaluewidth+"px "+c.largeHeight/c.newvaluewidth+"px"}):c.zoomWindow.css({"background-size":c.largeWidth/c.newvalueheight+"px "+c.largeHeight/c.newvalueheight+"px"})),c.changeBgSize=!1),c.zoomWindow.css({backgroundPosition:c.windowLeftPos+"px "+c.windowTopPos+"px"})))},setTintPosition:function(a){var b=this;b.nzOffset=b.$elem.offset(),b.tintpos=String((a.pageX-b.nzOffset.left-b.zoomLens.width()/2)*-1),b.tintposy=String((a.pageY-b.nzOffset.top-b.zoomLens.height()/2)*-1),b.Etoppos&&(b.tintposy=0),b.Eloppos&&(b.tintpos=0),b.Eboppos&&(b.tintposy=(b.nzHeight-b.zoomLens.height()-2*b.options.lensBorderSize)*-1),b.Eroppos&&(b.tintpos=(b.nzWidth-b.zoomLens.width()-2*b.options.lensBorderSize)*-1),b.options.tint&&(b.fullheight&&(b.tintposy=0),b.fullwidth&&(b.tintpos=0),b.zoomTintImage.css({left:b.tintpos+"px"}),b.zoomTintImage.css({top:b.tintposy+"px"}))},swaptheimage:function(b,c){var d=this,e=new Image;d.options.loadingIcon&&(d.spinner=a("<div style=\"background: url('"+d.options.loadingIcon+"') no-repeat center;height:"+d.nzHeight+"px;width:"+d.nzWidth+'px;z-index: 2000;position: absolute; background-position: center center;"></div>'),d.$elem.after(d.spinner)),d.options.onImageSwap(d.$elem),e.onload=function(){d.largeWidth=e.width,d.largeHeight=e.height,d.zoomImage=c,d.zoomWindow.css({"background-size":d.largeWidth+"px "+d.largeHeight+"px"}),d.swapAction(b,c)},e.src=c},swapAction:function(b,c){var d=this,e=new Image;if(e.onload=function(){d.nzHeight=e.height,d.nzWidth=e.width,d.options.onImageSwapComplete(d.$elem),d.doneCallback()},e.src=b,d.currentZoomLevel=d.options.zoomLevel,d.options.maxZoomLevel=!1,"lens"==d.options.zoomType&&d.zoomLens.css({backgroundImage:"url('"+c+"')"}),"window"==d.options.zoomType&&d.zoomWindow.css({backgroundImage:"url('"+c+"')"}),"inner"==d.options.zoomType&&d.zoomWindow.css({backgroundImage:"url('"+c+"')"}),d.currentImage=c,d.options.imageCrossfade){var f=d.$elem,g=f.clone();if(d.$elem.attr("src",b),d.$elem.after(g),g.stop(!0).fadeOut(d.options.imageCrossfade,function(){a(this).remove()}),d.$elem.width("auto").removeAttr("width"),d.$elem.height("auto").removeAttr("height"),f.fadeIn(d.options.imageCrossfade),d.options.tint&&"inner"!=d.options.zoomType){var h=d.zoomTintImage,i=h.clone();d.zoomTintImage.attr("src",c),d.zoomTintImage.after(i),i.stop(!0).fadeOut(d.options.imageCrossfade,function(){a(this).remove()}),h.fadeIn(d.options.imageCrossfade),d.zoomTint.css({height:d.$elem.height()}),d.zoomTint.css({width:d.$elem.width()})}d.zoomContainer.css("height",d.$elem.height()),d.zoomContainer.css("width",d.$elem.width()),"inner"==d.options.zoomType&&(d.options.constrainType||(d.zoomWrap.parent().css("height",d.$elem.height()),d.zoomWrap.parent().css("width",d.$elem.width()),d.zoomWindow.css("height",d.$elem.height()),d.zoomWindow.css("width",d.$elem.width()))),d.options.imageCrossfade&&(d.zoomWrap.css("height",d.$elem.height()),d.zoomWrap.css("width",d.$elem.width()))}else d.$elem.attr("src",b),d.options.tint&&(d.zoomTintImage.attr("src",c),d.zoomTintImage.attr("height",d.$elem.height()),d.zoomTintImage.css({height:d.$elem.height()}),d.zoomTint.css({height:d.$elem.height()})),d.zoomContainer.css("height",d.$elem.height()),d.zoomContainer.css("width",d.$elem.width()),d.options.imageCrossfade&&(d.zoomWrap.css("height",d.$elem.height()),d.zoomWrap.css("width",d.$elem.width()));d.options.constrainType&&("height"==d.options.constrainType&&(d.zoomContainer.css("height",d.options.constrainSize),d.zoomContainer.css("width","auto"),d.options.imageCrossfade?(d.zoomWrap.css("height",d.options.constrainSize),d.zoomWrap.css("width","auto"),d.constwidth=d.zoomWrap.width()):(d.$elem.css("height",d.options.constrainSize),d.$elem.css("width","auto"),d.constwidth=d.$elem.width()),"inner"==d.options.zoomType&&(d.zoomWrap.parent().css("height",d.options.constrainSize),d.zoomWrap.parent().css("width",d.constwidth),d.zoomWindow.css("height",d.options.constrainSize),d.zoomWindow.css("width",d.constwidth)),d.options.tint&&(d.tintContainer.css("height",d.options.constrainSize),d.tintContainer.css("width",d.constwidth),d.zoomTint.css("height",d.options.constrainSize),d.zoomTint.css("width",d.constwidth),d.zoomTintImage.css("height",d.options.constrainSize),d.zoomTintImage.css("width",d.constwidth))),"width"==d.options.constrainType&&(d.zoomContainer.css("height","auto"),d.zoomContainer.css("width",d.options.constrainSize),d.options.imageCrossfade?(d.zoomWrap.css("height","auto"),d.zoomWrap.css("width",d.options.constrainSize),d.constheight=d.zoomWrap.height()):(d.$elem.css("height","auto"),d.$elem.css("width",d.options.constrainSize),d.constheight=d.$elem.height()),"inner"==d.options.zoomType&&(d.zoomWrap.parent().css("height",d.constheight),d.zoomWrap.parent().css("width",d.options.constrainSize),d.zoomWindow.css("height",d.constheight),d.zoomWindow.css("width",d.options.constrainSize)),d.options.tint&&(d.tintContainer.css("height",d.constheight),d.tintContainer.css("width",d.options.constrainSize),d.zoomTint.css("height",d.constheight),d.zoomTint.css("width",d.options.constrainSize),d.zoomTintImage.css("height",d.constheight),d.zoomTintImage.css("width",d.options.constrainSize))))},doneCallback:function(){var a=this;a.options.loadingIcon&&a.spinner.hide(),a.nzOffset=a.$elem.offset(),a.nzWidth=a.$elem.width(),a.nzHeight=a.$elem.height(),a.currentZoomLevel=a.options.zoomLevel,a.widthRatio=a.largeWidth/a.nzWidth,a.heightRatio=a.largeHeight/a.nzHeight,"window"==a.options.zoomType&&(a.nzHeight<a.options.zoomWindowWidth/a.widthRatio?lensHeight=a.nzHeight:lensHeight=String(a.options.zoomWindowHeight/a.heightRatio),a.options.zoomWindowWidth<a.options.zoomWindowWidth?lensWidth=a.nzWidth:lensWidth=a.options.zoomWindowWidth/a.widthRatio,a.zoomLens&&(a.zoomLens.css("width",lensWidth),a.zoomLens.css("height",lensHeight)))},getCurrentImage:function(){var a=this;return a.zoomImage},getGalleryList:function(){var b=this;return b.gallerylist=[],b.options.gallery?a("#"+b.options.gallery+" a").each(function(){var c="";a(this).data("zoom-image")?c=a(this).data("zoom-image"):a(this).data("image")&&(c=a(this).data("image")),c==b.zoomImage?b.gallerylist.unshift({href:""+c,title:a(this).find("img").attr("title")}):b.gallerylist.push({href:""+c,title:a(this).find("img").attr("title")})}):b.gallerylist.push({href:""+b.zoomImage,title:a(this).find("img").attr("title")}),b.gallerylist},changeZoomLevel:function(a){var b=this;b.scrollingLock=!0,b.newvalue=parseFloat(a).toFixed(2),newvalue=parseFloat(a).toFixed(2),maxheightnewvalue=b.largeHeight/(b.options.zoomWindowHeight/b.nzHeight*b.nzHeight),maxwidthtnewvalue=b.largeWidth/(b.options.zoomWindowWidth/b.nzWidth*b.nzWidth),"inner"!=b.options.zoomType&&(maxheightnewvalue<=newvalue?(b.heightRatio=b.largeHeight/maxheightnewvalue/b.nzHeight,b.newvalueheight=maxheightnewvalue,b.fullheight=!0):(b.heightRatio=b.largeHeight/newvalue/b.nzHeight,b.newvalueheight=newvalue,b.fullheight=!1),maxwidthtnewvalue<=newvalue?(b.widthRatio=b.largeWidth/maxwidthtnewvalue/b.nzWidth,b.newvaluewidth=maxwidthtnewvalue,b.fullwidth=!0):(b.widthRatio=b.largeWidth/newvalue/b.nzWidth,b.newvaluewidth=newvalue,b.fullwidth=!1),"lens"==b.options.zoomType&&(maxheightnewvalue<=newvalue?(b.fullwidth=!0,b.newvaluewidth=maxheightnewvalue):(b.widthRatio=b.largeWidth/newvalue/b.nzWidth,b.newvaluewidth=newvalue,b.fullwidth=!1))),"inner"==b.options.zoomType&&(maxheightnewvalue=parseFloat(b.largeHeight/b.nzHeight).toFixed(2),maxwidthtnewvalue=parseFloat(b.largeWidth/b.nzWidth).toFixed(2),newvalue>maxheightnewvalue&&(newvalue=maxheightnewvalue),newvalue>maxwidthtnewvalue&&(newvalue=maxwidthtnewvalue),maxheightnewvalue<=newvalue?(b.heightRatio=b.largeHeight/newvalue/b.nzHeight,newvalue>maxheightnewvalue?b.newvalueheight=maxheightnewvalue:b.newvalueheight=newvalue,b.fullheight=!0):(b.heightRatio=b.largeHeight/newvalue/b.nzHeight,newvalue>maxheightnewvalue?b.newvalueheight=maxheightnewvalue:b.newvalueheight=newvalue,b.fullheight=!1),maxwidthtnewvalue<=newvalue?(b.widthRatio=b.largeWidth/newvalue/b.nzWidth,newvalue>maxwidthtnewvalue?b.newvaluewidth=maxwidthtnewvalue:b.newvaluewidth=newvalue,b.fullwidth=!0):(b.widthRatio=b.largeWidth/newvalue/b.nzWidth,b.newvaluewidth=newvalue,b.fullwidth=!1)),scrcontinue=!1,"inner"==b.options.zoomType&&(b.nzWidth>=b.nzHeight&&(b.newvaluewidth<=maxwidthtnewvalue?scrcontinue=!0:(scrcontinue=!1,b.fullheight=!0,b.fullwidth=!0)),b.nzHeight>b.nzWidth&&(b.newvaluewidth<=maxwidthtnewvalue?scrcontinue=!0:(scrcontinue=!1,b.fullheight=!0,b.fullwidth=!0))),"inner"!=b.options.zoomType&&(scrcontinue=!0),scrcontinue&&(b.zoomLock=0,b.changeZoom=!0,b.options.zoomWindowHeight/b.heightRatio<=b.nzHeight&&(b.currentZoomLevel=b.newvalueheight,"lens"!=b.options.zoomType&&"inner"!=b.options.zoomType&&(b.changeBgSize=!0,b.zoomLens.css({height:String(b.options.zoomWindowHeight/b.heightRatio)+"px"})),"lens"!=b.options.zoomType&&"inner"!=b.options.zoomType||(b.changeBgSize=!0)),b.options.zoomWindowWidth/b.widthRatio<=b.nzWidth&&("inner"!=b.options.zoomType&&b.newvaluewidth>b.newvalueheight&&(b.currentZoomLevel=b.newvaluewidth),"lens"!=b.options.zoomType&&"inner"!=b.options.zoomType&&(b.changeBgSize=!0,b.zoomLens.css({width:String(b.options.zoomWindowWidth/b.widthRatio)+"px"})),"lens"!=b.options.zoomType&&"inner"!=b.options.zoomType||(b.changeBgSize=!0)),"inner"==b.options.zoomType&&(b.changeBgSize=!0,b.nzWidth>b.nzHeight&&(b.currentZoomLevel=b.newvaluewidth),b.nzHeight>b.nzWidth&&(b.currentZoomLevel=b.newvaluewidth))),b.setPosition(b.currentLoc)},closeAll:function(){self.zoomWindow&&self.zoomWindow.hide(),self.zoomLens&&self.zoomLens.hide(),self.zoomTint&&self.zoomTint.hide()},changeState:function(a){var b=this;"enable"==a&&(b.options.zoomEnabled=!0),"disable"==a&&(b.options.zoomEnabled=!1)}};a.fn.elevateZoom=function(b){return this.each(function(){var c=Object.create(e);c.init(b,this),a.data(this,"elevateZoom",c)})},a.fn.elevateZoom.options={zoomActivation:"hover",zoomEnabled:!0,preloading:1,zoomLevel:1,scrollZoom:!1,scrollZoomIncrement:.1,minZoomLevel:!1,maxZoomLevel:!1,easing:!1,easingAmount:12,lensSize:200,zoomWindowWidth:400,zoomWindowHeight:400,zoomWindowOffetx:0,zoomWindowOffety:0,zoomWindowPosition:1,zoomWindowBgColour:"#fff",lensFadeIn:!1,lensFadeOut:!1,debug:!1,zoomWindowFadeIn:!1,zoomWindowFadeOut:!1,zoomWindowAlwaysShow:!1,zoomTintFadeIn:!1,zoomTintFadeOut:!1,borderSize:4,showLens:!0,borderColour:"#888",lensBorderSize:1,lensBorderColour:"#000",lensShape:"square",zoomType:"window",containLensZoom:!1,lensColour:"white",lensOpacity:.4,lenszoom:!1,tint:!1,tintColour:"#333",tintOpacity:.4,gallery:!1,galleryActiveClass:"zoomGalleryActive",imageCrossfade:!1,constrainType:!1,constrainSize:!1,loadingIcon:!1,cursor:"default",responsive:!0,onComplete:a.noop,onDestroy:function(){},onZoomedImageLoaded:function(){},onImageSwap:a.noop,onImageSwapComplete:a.noop}}(jQuery,window,document);;