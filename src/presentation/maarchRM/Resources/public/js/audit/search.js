
/*$('#contain').ready(function() {
    $("#event").append($("#event option").remove().sort(function(a, b) {
        var at = $(a).text(), bt = $(b).text();
        return (at > bt)?1:((at < bt)?-1:0);
    }));

     var option = $('<option/>').val('').text($('#selectAnEvent_text').html()).prependTo($('#event'));

     $('#event').val('');
});
$('select').find('option').removeClass('hidden');
*/

EventSearchForm = {
    form : $('#eventSearchForm'),
    result : $('#resultSearch'),

    search: function() {
        var parameters = this.serialize();
        ajax($(this), {
            type: 'GET',
            url: '/Events',
            data: parameters,
            dataType: 'html',
            contentType: 'application/json',
            async: true,
            headers: { 'X-Laabs-Max-Count': 100 },
            success: function (response) {
                EventSearchForm.result.empty().append(response);
            }
        });
    },

    serialize : function() {
        serializedObject = {
            fromDate: $('#fromDate').val(),
            toDate: $('#toDate').val(),
            status: this.form.find('[name="status"]:checked').val(),
            event: $('#eventName').data('event-path'),
            accountId: $.trim($('#accountTypeahead').val()),
            term: $('#term').val(),
        };
        
        if ($('#accountTypeahead').data('accountid')) {
            serializedObject.accountId = $('#accountTypeahead').data('accountid') ;
        }

        return serializedObject;
    },

    clear: function() {
        this.form.find('input[type=text]').val('');
        this.form.find('select').prop('selectedIndex', 0);
        $('#eventName').data('event-path', "");
        $('#accountTypeahead').data('accountid', "");
        this.form.find('.wordingAll').click();
        this.form.find('.status :first').click();

        this.result.empty().html('');
    }
}

$('#app_maarchRM_main').keypress(function (e) {
    if (e.which != 13) {
        return;
    }
    // Prevent form submit
    e.preventDefault();
    $("#search").click();
});

$('#search').on('click', function () {
    EventSearchForm.search();
});

$("#reset").on("click", function () {
    EventSearchForm.clear();
});

$("#eventName").on("keyup", function () {
    if (!$(this).val()) {
        $("#eventName").val("");
        $("#eventName").data('event-path', "");
    }
});

$("#accountTypeahead").on("keyup", function () {
    if (!$(this).val()) {
        $('#accountTypeahead').val('');
        $('#accountTypeahead').data('accountid', "");
    }
});

// checkBox button
$("#eventSearchForm").on('click', '.status', function () {
    $('#eventSearchForm').find('.status').removeClass('btn-info active').addClass('btn-default');
    $(this).addClass('btn-info active').removeClass('btn-default');
});

  
//Account typeahead  

var users = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('displayName'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: {url: '/user/todisplay', ttl: '0'},
    limit: 500
});
users.initialize();

var services = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('displayName'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: {url: '/service/todisplay', ttl: '0'},
    limit: 100
});
services.initialize();

$('#accountTypeahead').typeahead(
    {
        hint: true,
        highlight: true,
        minLength: 2
    },
    {
        name: 'users',
        displayKey: function (account) {
            return account.displayName;
        },
        templates: {
            empty: function () {
                return "<span class='well well-sm'>"+$('#noAccountFound').text()+"</span>";
            },
            suggestion: function (account) {
                var name = account.displayName;
                if (account.accountType == 'service') {
                    var icone = "<span class='fa fa-laptop' >&nbsp;</span>";
                } else {
                    var icone = "<span class='fa fa-user' >&nbsp;</span>";
                }
                var display =
                    "<span>"
                    + icone
                    + "<span style='font-family:Helvetica, sans-serif;'>"
                    + name
                    + "</span></span><br>";
                return display;
            }
        },
        source: function (query, cb) {
            var listSuggestion = "";
            services.search(query, function (suggestions) {
                listSuggestion = suggestions;
            });
            users.search(query, function (suggestions) {
                $.extend(listSuggestion, suggestions);
            });
            cb(listSuggestion);
        },
        skipCache: true
    }
).on('typeahead:selected', function ($event, suggestion, source) {
    $("#accountTypeahead").data('accountid', suggestion.accountId);
});

$("#accountTypeahead").on('keypress', function () {
    $("#accountTypeahead").data('accountid', "");
});

var events = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('label'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    local: $('#eventName').data('eventlist')
});
events.initialize();

$('#eventName').typeahead(
    {
        hint: true,
        highlight: true,
        minLength: 2
    },
    {
        name: 'event',
        displayKey: function (event) {
            return event.label;
        },
        templates: {
            suggestion: function (event) {
                var name = event.label;
                var display =
                    "<span>"
                    + name
                    + "</span><br>";
                return display;
            }
        },
        source: function (query, process) {
            eventList = [];
            events.search(query, function (suggestions) {
                eventList = suggestions;
            });
            process(eventList);
        },
        limit: 10,
        skipCache: true
    }
).on('typeahead:selected', function ($event, suggestion, source) {
    $("#eventName").data('event-path', suggestion.path);
});

$("#eventName").on('keypress', function () {
    $("#eventName").data('event-path', "");
});
        