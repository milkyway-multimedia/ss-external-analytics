!function(e,r,a){var n=r.configuration||{},t={referrer:"referrer",dataSource:"dataSource",expId:"experimentId",expVar:"experimentVariant",campaignName:"campaignName",campaignSource:"campaignSource",campaignMedium:"campaignMedium",campaignKeyword:"campaignKeyword",campaignContent:"campaignContent",campaignId:"campaignId",screenResolution:"screenResolution",viewportSize:"windowSize",screenColors:"screenColours",encoding:"encoding",language:"language",javaEnabled:"javaEnabled",flashVersion:"flashVersion",appName:"applicationName",appId:"applicationId",appVersion:"applicationVersion",appInstallerId:"applicationInstallerId",location:"pageLocation",hostname:"domain",documentPath:"pagePath",title:"pageTitle"};e.hasOwnProperty("dataField")?e.dataField.hasOwnProperty("onRender")||(e.dataField.onRender={}):e.dataField={onRender:{}},e.dataField.onRender.GA=function(r,i,o){if(o||(o=[]),!e.hasOwnProperty("GA")||!e.GA.hasOwnProperty("trackers")||-1!==o.indexOf("EA.GA"))return{};var s,c,d,l={},w=a.extend({},t,n.dataField||{});for(s in e.GA.trackers)if(-1===o.indexOf("EA.GA."+s)&&e.GA.trackers.hasOwnProperty(s)&&e.GA.trackers[s].hasOwnProperty("trackers")&&e.GA.trackers[s].trackers.length)for(c=0;c<e.GA.trackers[s].trackers.length;c++)if(-1===o.indexOf("EA.GA."+s+"."+c))for(d in w)w.hasOwnProperty(d)&&e.GA.trackers[s].trackers[c].get(d)&&(1===e.GA.trackers[s].trackers.length?l[s+"["+t[d]+"]"]=e.GA.trackers[s].trackers[c].get(d):l[s+"["+c+"]["+t[d]+"]"]=e.GA.trackers[s].trackers[c].get(d));return l},r.hasOwnProperty("initCallbacks")||(r.initCallbacks={}),r.initCallbacks.dataField=function(){e.dataField.hasOwnProperty("render")&&e.dataField.render()}}(window.EA||{},window.EA?EA.GA||{}:{},window.jQuery||window.Zepto||window.Sprint||(window.mwm&&mwm.jquery?mwm.jquery:null));var EA=window.EA||{GA:{}};EA.GA=function(e,r){function a(e){!function(e,r,a,n,t,i){e.GoogleAnalyticsObject=n,e[n]||(e[n]=function(){(e[n].q=e[n].q||[]).push(arguments)}),e[n].l=+new Date,t=r.createElement(a),i=r.getElementsByTagName(a)[0],t.src="http://www.google-analytics.com/analytics.js",i.parentNode.insertBefore(t,i)}(window,document,"script",e)}var n=e.GA||{trackers:{}},t=n.configuration||{},i=function(e,r,a){a.hasOwnProperty("arguments")?(a.arguments[0]!=r&&a.arguments.unshift(r),window[e].apply(this,a.arguments)):a.constructor===Array?(a[0]!=r&&a.unshift(r),window[e].apply(this,a)):window[e](r,a)},o={onLoad:function(a){return function(){e.hasOwnProperty("eventTriggers")||(e.eventTriggers={}),e.eventTriggers[a]=e.GA.trackers[a].sendEvent,e.GA.trackers[a].trackers=window[a].getAll(),r(window).trigger("ea::loaded:google-analytics",[e.GA.trackers[a],a]);var n;if(e.GA.hasOwnProperty("initCallbacks"))for(n in e.GA.initCallbacks)e.GA.initCallbacks.hasOwnProperty(n)&&e.GA.initCallbacks[n].call(e.GA.trackers[a],a,e.GA.trackers[a].trackers,i);if(e.GA.trackers[a].hasOwnProperty("initCallbacks"))for(n in e.GA.trackers[a].initCallbacks)e.GA.trackers[a].initCallbacks.hasOwnProperty(n)&&e.GA.trackers[a].initCallbacks[n].call(e.GA.trackers[a],e.GA.trackers[a].trackers,i)}}};e.hasOwnProperty("GA")||(e.GA={}),e.GA.hasOwnProperty("trackers")||(e.GA.trackers={}),n.configure=function(e){o=r.extend(!0,{},o,e)},n.addNewTracker=function(r,t,s){a(r),e.GA.trackers.hasOwnProperty(r)||(e.GA.trackers[r]={}),e.GA.trackers[r].variable=r,e.GA.trackers[r].sendEvent=function(e){e.hasOwnProperty("hitType")||(e.hitType="event"),i(r,"send",e)},t&&n.loadAttributesForVariable(r,t),s||(s=o.onLoad(r)),window[r](s)},n.loadAttributesForVariable=function(e,r){for(var a in r)if(r.hasOwnProperty(a))if(r[a].constructor===Array)for(var n=0;n<r[a].length;n++)i(e,a,r[a][n]);else i(e,a,r)};for(var s in t)if(t.hasOwnProperty(s)){var c=t[s].attributes||{},d={};t[s].tracking_id&&(d.create=[{arguments:[t[s].tracking_id,"auto"]}]),t[s].pageview&&(d.send=["pageview"]),n.addNewTracker(s,r.extend({},d,c))}return n}(window.EA||{},window.jQuery||window.Zepto||window.Sprint||(window.mwm&&mwm.jquery?mwm.jquery:null));