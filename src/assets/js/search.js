var sitename = 'LDI Careers';
// var site = 'http://localhost:8888/ldi/dist/';
// var site = 'http://jobs.libertydiversified.com.php56-17.dfw3-1.websitetestlink.com/';
var site = 'http://jobs.libertydiversified.com/';
$(document).ready(function () {

    // Load Results on Page Load
    search();
    selects();

    // ----------------------------------------------------------
    // A short snippet for detecting versions of IE in JavaScript
    // without resorting to user-agent sniffing
    // ----------------------------------------------------------
    // If you're not in IE (or IE version is less than 5) then:
    //     ie === undefined
    // If you're in IE (>=5) then you can determine which version:
    //     ie === 7; // IE7
    // Thus, to detect IE:
    //     if (ie) {}
    // And to detect the version:
    //     ie === 6 // IE6
    //     ie > 7 // IE8, IE9 ...
    //     ie < 9 // Anything less than IE9
    // ----------------------------------------------------------

    // UPDATE: Now using Live NodeList idea from @jdalton
 
    var ie = (function(){

        var undef,
            v = 3,
            div = document.createElement('div'), 
            all = div.getElementsByTagName('i');
 
        while ( 
            div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
                all[0]
            );

        return v > 4 ? v : undef;

    }());

    if(ie < 10){
        $('body').addClass('ie9'); 
    }
    
    $(document).on("click", ".content-window a", function() {
	    var newURL = updateURLParameter(window.location.href, 'location', $(this + ' h3').html());
	    update_url(newURL);
	});
	

    $(document).on("click", ".submit-search", function() {
        var keywords = $('input[name=keywords]').val(),
            // zip = $('input[name=zip]').val(),
            category = $('select[name=category]').val(),
            location = $('select[name=location]').val(),
            group = $('select[name=group]').val();

        var newURL = updateURLParameter(window.location.href, 'spage', 1);
        if(keywords != '') {
            newURL = updateURLParameter(newURL, 'keywords', keywords);
        } else {
            newURL = updateURLParameter(newURL, 'keywords', '');
        }
        /*
        if(zip != '') {
            newURL = updateURLParameter(newURL, 'zip', zip);
        } else {
            newURL = updateURLParameter(newURL, 'zip', '');
        }
        */
        if(category != '') {
            newURL = updateURLParameter(newURL, 'category', category);
        } else {
            newURL = updateURLParameter(newURL, 'category', '');
        }
        if(location != '') {
            newURL = updateURLParameter(newURL, 'location', location);
        } else {
            newURL = updateURLParameter(newURL, 'location', '');
        }

        update_url(newURL);
    });

    $(document).on("click", ".pagination a", function() {
        var href = $(this).attr('data-href');
        var newURL = updateURLParameter(window.location.href, 'spage', href);
        update_url(newURL);
    });

});

$(document).on("click", ".o", function() {
    var href = $(this).attr('data-href');

    var newURL = updateURLParameter(window.location.href, 'o', href);
    update_url(newURL);
});

/**
 * http://stackoverflow.com/a/10997390/11236
 */
function updateURLParameter(url, param, paramVal){
	
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) { 
        tempArray = additionalURL.split("&");
        console.log(tempArray);
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var rows_txt = temp + "" + param + "=" + encodeURIComponent(paramVal);
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return decodeURIComponent(sParameterName[1]);
        }
    }
}
function map_search(location, type){

    if($('body').hasClass('ie9')){
        window.location.href = "search-map.php";
    } else {
        History.pushState({"html": "search", "pageTitle": "Search"}, "", '/search.php');
    }
	
    var newURL = updateURLParameter('', type, location);
    
    console.log(type);
      if(type === 'city') {
        $('html, body').animate({
        scrollTop: $("#postings" ).offset().top
    }, 500);
    }
    update_map_url(newURL);
			
    return false;
}

function update_map_url(newURL){
    if($('body').hasClass('ie9')){
        window.location.href = newURL;
    } else {
        History.pushState({"html": "search", "pageTitle": "Search"}, "", newURL);
    }
    
    search();
}

function update_url(newURL){
    if($('body').hasClass('ie9')){
        window.location.href = newURL;
    } else {
        History.pushState({"html": "search", "pageTitle": "Search – "+sitename}, "Search – "+sitename, newURL);
    }
    search();
}

function search(){
    $('#postings').fadeOut().promise().done(function () {
        $('#postings').fadeIn().html('<div class="vac loader"><div class="va"><div class="text-center"><img src="http://careers.ulta.com/wp-content/themes/ulta/assets/images/global/ajax-loader.gif" /></div></div></div>');
    });
    // Change location name on search page:
    if (GetURLParameter('location')) {
       	$('#current-location').fadeOut().promise().done(function () {
		    $('#current-location').fadeIn().html(GetURLParameter('location'));
		});
    } else {
	    if ($('#current-location').html() != 'United States') {
	    	$('#current-location').fadeOut().promise().done(function () {
			    $('#current-location').fadeIn().html('United States');
			});
		}
    }
    
    $.ajax({
        type: "GET",
        url: site+'assets/ajax/ajax-search.php',
        data: {
            'ajax': '1',
            'keywords': GetURLParameter('keywords'),
            //'zip': GetURLParameter('zip'),
            'spage': GetURLParameter('spage'),
            'category': GetURLParameter('category'),
            'location': GetURLParameter('location'),
            'o': GetURLParameter('o')
        },
        dataType: "text",
        success: function (data) {
            console.log(data);
            $('#postings').fadeOut().promise().done(function () {
                var val = jQuery.parseJSON(data);
                $('#postings').html(val.postings).fadeIn();
                $('.holder--pagination').html(val.pagination).fadeIn();
               
            });
        }
    });

}

function reset(){
    $('#postings').fadeOut().promise().done(function () {
        $('#postings').html('');
        $('.holder--pagination').html('');
        $('.sub-title').html('');
        $('.holder--pre-table-header').html('');
    });

    if($('body').hasClass('ie9')){
        window.location.href = "index.php";
    } else {
        History.pushState({"html": "search", "pageTitle": "Search"}, "", 'http://shakerdev.com/marquette/');
        
    }
    search();
}

			
function search_default(){
    $('#postings').fadeOut().promise().done(function () {
        $('#postings').fadeIn().html('<div class="vac loader"><div class="va"><div class="text-center"><img src="ajax-loader.gif" /></div></div></div>');
    });
    $.ajax({
        type: "GET",
        url: site+'/api/ajax-default.php',
        data: {
            'ajax': '1'
        },
        dataType: "text",
        success: function (data) {
            $('#postings').fadeOut().promise().done(function () {
                var val = jQuery.parseJSON(data);
                $('#postings').html(val.postings).fadeIn();
            });
        }
    });

}

function selects() {
    $.ajax({
        type: "GET",
        url: site+'assets/ajax/selects.php',
        data: {
            'category': GetURLParameter('category'),
            'location': GetURLParameter('location'),
        },
        dataType: "text",
        success: function (data) {
            var val = jQuery.parseJSON(data);
            $('#location').html(val.locations);
            $('#category').html(val.category);
console.log(GetURLParameter('keywords'));
            $('input[name=keywords]').val(GetURLParameter('keywords'));
            $('input[name=zip]').val(GetURLParameter('zip'));
        }
    });
}