"use strict";!function(e,t,o){function i(e){e.preventDefault()}if(t.touch){var n=e(o),a="touchstart",s="touchmove",c="touchend";e.event.special.tap={options:{threshold:{time:750,distance:{x:10,y:10}}},setup:function(){var t=this,o=e(t);o.on(a,function(i){function n(){clearTimeout(p),o.off(s,r).off(c,a)}function a(o){n(),i.target===o.target&&(o.type="tap",e.event.dispatch.call(t,o))}function r(e){var t=e.originalEvent.touches?e.originalEvent.touches[0]:e;d={delta:{x:Math.abs(u.coords.x-t.pageX),y:Math.abs(u.coords.y-t.pageY)}},(d.delta.x>h.threshold.distance.x||d.delta.y>h.threshold.distance.y)&&n()}var d,p,h=e.event.special.tap.options,l=i.originalEvent.touches?i.originalEvent.touches[0]:i,u={coords:{x:l.pageX,y:l.pageY}};p=setTimeout(n,h.threshold.time),o.on(s,r).on(c,a)})}},e.event.special.swipe={options:{threshold:{distance:{x:10,y:10}},momentum:{time:250,distance:{x:75,y:30}}},setup:function(){var t=this,o=e(t);o.on(a,function(a){function r(){o.off(s,p).off(c,d),n.off(s,i)}function d(o){if(r(),h){var i={coords:h.coords,delta:{x:Math.abs(f.coords.x-h.coords.x),y:Math.abs(f.coords.y-h.coords.y)},time:(new Date).getTime()};if(i.delta.x>l.threshold.distance.x){var n="swipe"+h.direction;Math.abs(f.time-i.time)<l.momentum.time&&i.delta.x>l.momentum.distance.x&&(n="quick"+n),o.type=n,o.delta=i.delta,o.direction=h.direction,e.event.dispatch.call(t,o)}}}function p(o){var a=o.originalEvent.touches?o.originalEvent.touches[0]:o;h={coords:{x:a.pageX,y:a.pageY},delta:{x:Math.abs(f.coords.x-a.pageX),y:Math.abs(f.coords.y-a.pageY)},direction:f.coords.x>a.pageX?"left":"right"},h.delta.x>l.threshold.distance.x&&(o.type="swipe",o.delta=h.delta,o.direction=h.direction,e.event.dispatch.call(t,o),n.on(s,i))}var h,l=e.event.special.swipe.options,u=a.originalEvent.touches?a.originalEvent.touches[0]:a,f={time:(new Date).getTime(),coords:{x:u.pageX,y:u.pageY}};o.on(s,p).on(c,d)})}},e.each({swipeleft:"swipe",swiperight:"swipe",quickswipeleft:"swipe",quickswiperight:"swipe"},function(t,o){e.event.special[t]={setup:function(){e(this).on(o,e.noop)}}})}}(jQuery,Modernizr,document);