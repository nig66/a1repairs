"use strict";var _monoLang={langObj:{},resellerHash:$("html").attr("data-rHash"),langCode:$("html").attr("lang"),releaseTimestamp:isNaN(parseInt("1535627606076"))?(new Date).getTime():parseInt("1535627606076"),init:function(){var t=this;$.get("/assets/js/lang/"+this.resellerHash+"/"+this.langCode.toLowerCase()+".json?"+this.releaseTimestamp+"&mch",function(e){t.langObj=e})},get:function(t){var e=t.replace(/#/g,"");return this.langObj[e]?this.langObj[e]:t},parseString:function(t){var e=this;return t.replace(/##[^#]*##/g,function(t){return e.get(t)})}};_monoLang.init();