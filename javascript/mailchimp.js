var EA=window.EA||{MC:{}};EA.MC=function(r,t){function a(r){var a=document.createElement("script");a.async=!0,a.defer=!0,a.src=("https:"==document.location.protocol?"https://s3.amazonaws.com/downloads.mailchimp.com":"http://downloads.mailchimp.com")+"/js/goal.min.js";var e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e),r&&t(a).on("load",r)}function e(r){return"string"==typeof r||r instanceof String?r:r.hasOwnProperty("longAction")?r.longAction:r.hasOwnProperty("title")?r.title:r.hasOwnProperty("label")?r.label:r.hasOwnProperty("name")?r.name:t.param(r)}var n=r.MC||{trackers:{}},o=n.configuration||{};r.hasOwnProperty("MC")||(r.MC={}),r.MC.hasOwnProperty("trackers")||(r.MC.trackers={}),n.addNewTracker=function(o,i){window.$mcGoal=i,r.MC.trackers.hasOwnProperty(o)||(r.MC.trackers[o]={}),r.MC.trackers[o].variable=o,r.MC.trackers[o].configuration=i,r.MC.trackers[o].sendEvent=function(r){var t=window.$mcGoal||{};window.$mcGoal=i,window.$mcGoal.processEvent(e(r)),window.$mcGoal=t},a(function(){i.attributes&&n.loadAttributesForVariable(o,i.attributes),t(window).trigger("ea::loaded:mailchimp-goals",[r.MC.trackers[o],o]),r.hasOwnProperty("eventTriggers")||(r.eventTriggers={}),r.eventTriggers[o]=r.MC.trackers[o].sendEvent;var a;if(r.MC.hasOwnProperty("initCallbacks"))for(a in r.MC.initCallbacks)r.MC.initCallbacks.hasOwnProperty(a)&&r.MC.initCallbacks[a].call(r.MC.trackers[o],o,r.MC.trackers[o].sendEvent);if(r.MC.trackers[o].hasOwnProperty("initCallbacks"))for(a in r.MC.trackers[o].initCallbacks)r.MC.trackers[o].initCallbacks.hasOwnProperty(a)&&r.MC.trackers[o].initCallbacks[a].call(r.MC.trackers[o],r.MC.trackers[o].sendEvent)})},n.loadAttributesForVariable=function(r,t){for(var a in t)if(t.hasOwnProperty(a))if(t[a].constructor===Array)for(var n=0;n<t[a].length;n++)window.$mcGoal.processEvent(e(t[a][n]));else window.$mcGoal.processEvent(e(attributess[a]))};for(var i in o)o.hasOwnProperty(i)&&n.addNewTracker(i,o);return n}(window.EA||{},window.jQuery||window.Zepto||window.Sprint||(window.mwm&&mwm.jquery?mwm.jquery:null));