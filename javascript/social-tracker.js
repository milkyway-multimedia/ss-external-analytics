!function(e){var t=function(t,n,o,i,r,w){function a(e,t){if(!e)return"";var n=new RegExp("[\\?&#]"+t+"=([^&#]*)"),o=n.exec(e);return null!==o?decodeURI(o[1]):""}function d(){o&&"undefined"!=typeof o||window.hasOwnProperty("FB")&&window.FB.hasOwnProperty("Event")&&window.FB.Event.hasOwnProperty("subscribe")&&(o=window.FB)}function c(){i&&"undefined"!=typeof i||window.hasOwnProperty("twttr")&&window.twttr.hasOwnProperty("events")&&(i=window.twttr)}function s(){r&&"undefined"!=typeof r||window.hasOwnProperty("addthis")&&(r=window.addthis)}function u(){w&&"undefined"!=typeof w||window.hasOwnProperty("EA")&&window.EA.hasOwnProperty("event")&&(w=window.EA.event)}var f=e.social||{},l=[];return null==n&&"function"==typeof require&&(n=require("jquery")),!w&&e.event?w=e.event:w&&"function"==typeof require&&(w=require("../events.js")(t?t.social||t:{},n)),f.fb=function(e,t){if(l.indexOf("fb:"+e+":"+t)===-1){try{d(),o&&(l.push("fb:"+e+":"+t),o.Event.subscribe(e,function(e){n(window).trigger("ea::social-event",[{socialNetwork:"facebook",socialAction:t,socialTarget:e}])}))}catch(i){}return f}},f.twitter=function(e,t){if(l.indexOf("twitter:"+e+":"+t)===-1){try{c(),i&&(l.push("twitter:"+e+":"+t),i.ready(function(o){o.events.bind(e,function(e){if(e){var o;e.target&&"IFRAME"==e.target.nodeName?o=a(e.target.src,"url"):e.data&&e.data.screen_name&&(o=e.data.screen_name),e.data&&e.data.screen_name&&(t=t+" (@"+e.data.screen_name+")"),n(window).trigger("ea::social-event",[{socialNetwork:"twitter",socialAction:t,socialTarget:o}])}})}))}catch(o){}return f}},f.addthis=function(e){if(l.indexOf("addthis:"+e)===-1){try{if(s(),r){l.push("addthis:"+e);var t=function(t){var o=t.hasOwnProperty("hash")?t.hash:window.location.href;n(window).trigger("ea::social-event",[{socialNetwork:t.service,socialAction:e,socialTarget:o}])};r.addEventListener("addthis.menu.share",t),r.addEventListener("addthis.user.clickback",t)}}catch(o){}return f}},f.init=function(){f.fb("edge.create","like"),f.fb("edge.remove","unlike"),f.fb("message.send","share"),f.fb("comment.create","comment"),f.fb("comment.remove","deleted comment"),f.twitter("tweet","tweet"),f.twitter("retweet","retweet"),f.twitter("follow","follow"),f.twitter("unfollow","unfollow"),f.twitter("favorite","favourite"),f.twitter("unfavorite","unfavourite"),f.addthis("addthis:share"),n(window).on("[data-pin-do]",function(){var e=n(this);n(window).trigger("ea::social-event",[{socialNetwork:"pinterest",socialAction:e.data("pinDo"),socialTarget:window.location.href}])})},f.init(),n(window).on("mwm::loaded:js",f.init),n(window).on("ea::social-event",function(e,t){return u(),!w||(t||(t=e.detail[0]),t.hitType="social",void w(t))}),f};"object"==typeof module&&"object"==typeof module.exports&&e===module.exports?module.exports=t:(window.EA=e||window.EA,window.EA.social=t(null,window.jQuery||window.Zepto||window.Sprint||(window.mwm&&mwm.jquery?mwm.jquery:null),window.FB||null,window.twttr||null,window.addthis||null,window.EA.event?EA.event:null))}("object"==typeof module&&"object"==typeof module.exports?module.exports:window.EA||{});