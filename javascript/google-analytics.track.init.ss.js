var EA = window.EA || {GA : {}};

if(!EA.hasOwnProperty('GA'))
    EA.GA = {};

EA.GA.trackers = (function (_{$Var}) {
    (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;e=o.createElement(i);r=o.getElementsByTagName(i)[0];e.src='//www.google-analytics.com/analytics.js';r.parentNode.insertBefore(e,r)}(window,document,'script','$Var'));

    _{$Var}['$Var'] = {
        variable:       '$Var',
        initCallbacks:  {},
        hitCallback:    function(){},
        sendEvent:      function(data) {
                {$Var}('send', data);
        }
    };

    return _{$Var};
}(EA.GA.trackers || {}));

EA.GA.trackers = (function (_{$Var}) {
    <% if $Attributes %>
        $Attributes
    <% else_if $TrackingId %>
        $Var('create', '$TrackingId');
        $Var('send', 'pageview', {
            'hitCallback': function() {
                _{$Var}['{$Var}'].hitCallback();
            }
        });
    <% end_if %>

    {$Var}(function() {
        _{$Var}['{$Var}'].trackers = {$Var}.getAll();

        if(_{$Var}['{$Var}'].hasOwnProperty('initCallbacks')) {
            for(var callback in _{$Var}['{$Var}'].initCallbacks) {
                if(_{$Var}['{$Var}'].initCallbacks.hasOwnProperty(callback)) {
                    _{$Var}['{$Var}'].initCallbacks[callback].apply({$Var}, [_{$Var}['{$Var}'].trackers]);
                }
            }
        }

        <% if $SessionLink %>
            if(window.JSON) {
                var xhr, response;

                if (window.XMLHttpRequest)
                    xhr = new XMLHttpRequest();
                else
                    xhr = new ActiveXObject('Microsoft.XMLHTTP');

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        response = JSON.parse(xhr.responseText);
                        _{$Var}['{$Var}'].site_start = response.data.site_start;
                        _{$Var}['{$Var}'].page_start = response.data.page_start;
                    }
                }

                xhr.open('GET', '$SessionLink', true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send();
            }
        <% end_if %>
    });

    return _{$Var};
}(EA.GA.trackers || {}));