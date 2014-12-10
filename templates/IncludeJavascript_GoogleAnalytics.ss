var gaVars = gaVars || [];

gaVars['{$Var}'] = {
    variable:       '{$Var}',
    hitCallback:    function(){},
    sendEvent:      function(data) {
        {$Var}('send', data);
    }
};

(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;e=o.createElement(i);r=o.getElementsByTagName(i)[0];e.src='//www.google-analytics.com/analytics.js';r.parentNode.insertBefore(e,r)}(window,document,'script','$Var'));

<% if $Attributes %>
$Attributes
<% else_if $TrackingId %>
    {$Var}('create', '$TrackingId');
    {$Var}('send', 'pageview', {
        'hitCallback': function() {
            gaVars[{$Var}].hitCallback();
        }
    });
<% end_if %>

{$Var}(function() {
    window.gaVars['{$Var}'].trackers = {$Var}.getAll();

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
                window.gaVars['{$Var}'].site_start = response.data.site_start;
                window.gaVars['{$Var}'].page_start = response.data.page_start;
            }
        }

        xhr.open('GET', '$SessionLink', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
    }
    <% end_if %>
});