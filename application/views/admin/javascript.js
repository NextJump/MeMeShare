var imp = imp || {};
imp.admin = imp.admin || {};
imp.admin.index = imp.admin.index || {};

(function() {
    var a = imp.admin.index;

    a.attachListeners = function () {
        // Export Interactions Data Button listener
        $('#exportButton').click(function(event){
            if ($('#startDate').val() == '' || $('#endDate').val() == '') {
                $('#dateErrorPopup p').html('Please enter valid dates');
                $('#dateErrorPopupLnk').click();
                return;
            }
            event.preventDefault();
            var startDate = encodeURIComponent($('#startDate').val());
            var endDate = encodeURIComponent($('#endDate').val());
            document.location.href = '/admin/exportdata/start/' + startDate + '/end/' + endDate;
        });

        // Export Family Data Button listener
        $('#exportFamilyButton').click(function(event){
            event.preventDefault();
            document.location.href = '/admin/exportfamilydata';
        });

        // Export Mentor Data Button listener
        $('#exportMentorButton').click(function(event){
            event.preventDefault();
            document.location.href = '/admin/exportmentordata';
        });

        // Export House Data Button listener
        $('#exportHouseButton').click(function(event){
            event.preventDefault();
            document.location.href = '/admin/exporthousedata';
        });

        // Add New Family Button listener
        $('#addFamilyButton').click(function() {
            $('.family-dialog').popup('open');
        });

        // Listen for when the house input is focused
        $('.add-family-container #adminFamilyHouse').focus(function(evt) {
            // Overlay the search container
            if (!$('.add-family-container .admin-search-container').is(':visible')) {
                // Empty out the search field
                $('.add-family-container .admin-search-container input[data-type="search"]').val('');
                
                // Empty out any previous results
                $('.add-family-container .admin-search-listview').empty();
                
                // Show everything
                $('.add-family-container .admin-search-container').show();
                
                // Force focus on the search field
                $('.add-family-container .admin-search-container input[data-type="search"]').focus();

                $('.add-family-container #adminFamilyHouse').hide();
            }
        });
        
        // Hide the search box when the search field loses forcus
        $('.add-family-container .admin-search-container input[data-type="search"]').blur(function(evt) {
            if ($(this).val() == '') {
                $('.add-family-container .admin-search-container').hide();
                $('.add-family-container #adminFamilyHouse').show();
            }
        });

        // Listen for when the user starts typing in a student name to search for
        $('.admin-search-listview').on('listviewbeforefilter', function(e, data) {
            var $ul = $(this),
                $input = $(data.input),
                value = $input.val(),
                html = '';
            $ul.html('');
            if (value && value.length > 1) {
                $ul.html('<li><div class="ui-loader"><span class="ui-icon ui-icon-loading"></span></div></li>');
                $ul.listview('refresh');
                $.ajax({
                    url: '/admin/gethouses',
                    type: 'POST',
                    data: {
                        q: value
                    },
                    dataType: 'json'
                }).done(function(data) {
                    var houses = data.houses,
                        regex = new RegExp('(' + value.toLowerCase() + ')', 'i');
                    
                    // Clear the result list
                    $ul.empty();
                    
                    // Insert each result into the list
                    $.each(houses, function(i, house) {
                        var li = $('<li>'),
                            houseLink = $('<a>', {
                                'data-house-id': house.houseid,
                                'data-house-name': house.housename
                            }).html(house.housename.replace(regex, "<strong>$1</strong>")).appendTo(li);
                        
                        // When selecting an option, set the house for the family
                        houseLink.click(function(evt) {
                            var houseId = $(this).attr('data-house-id'),
                                houseName = $(this).attr('data-house-name');
                            
                            // Set the text of the input to the house name
                            $('#adminFamilyHouse').val(houseName);
                            
                            // Update the family ID data attribute on the input
                            $('#adminFamilyHouse').attr('data-house-id', houseId);
                            
                            // Hide the search container
                            $('.add-family-container .admin-search-container').hide();

                            $('.add-family-container #adminFamilyHouse').show();
                        });
                        
                        $ul.append(li);
                    });
                    
                    // Refresh the list
                    $ul.listview('refresh');
                    $ul.trigger('updatelayout');
                });
            }
        });

        $('#addFamilySubmitBtn').click(function(){
            var familyfname     = $('#adminFamilyStudentFname').val(),
                familylname     = $('#adminFamilyStudentLname').val(),
                familyemail     = $('#adminFamilyEmail').val(),
                familycohort    = $('#adminFamilyCohort').val(),
                familyhouseId   = parseInt($('#adminFamilyHouse').attr('data-house-id')),
                familyhouseName = $('#adminFamilyHouse').val();

            if ((familyfname == '') || (familylname == '') || (familyemail == '') || !(a.validateEmail(familyemail)) || (familycohort == '') || isNaN(familyhouseId) || (familyhouseId < 1)) {
                $('.add-family-container .add-family-error').html('Missing/Invalid Info');
                return;
            }

            $.ajax({
                url: '/admin/addnewfamily',
                type: 'POST',
                data: {
                    fname: familyfname,
                    lname: familylname,
                    email: familyemail,
                    cohort: familycohort,
                    houseid: familyhouseId
                },
                dataType: 'json'
            }).done(function(data) {
                $.mobile.loading('hide');
                $('.family-dialog').popup('close');
                $('#adminFamilyStudentFname').val('');
                $('#adminFamilyStudentLname').val('');
                $('#adminFamilyEmail').val('');
                $('#adminFamilyCohort').val('');
                $('#adminFamilyHouse').attr('data-house-id', '');
                $('#adminFamilyHouse').val('');
                $('.add-family-container .add-family-error').html('');

                var table  = $('#admin-family-container .admin-family-list'),
                    tr     = $('<tr>', {'class':'admin-family-item'}).appendTo(table),
                    tdname = $('<td>').appendTo(tr),
                    name  = $('<h3>', {'class':'family-name', 'text':familyfname+' '+familylname}).appendTo(tdname),
                    tdemail = $('<td>').appendTo(tr),
                    emailHouse = $('<span>', {'class':'family-email'}).html('<b>'+familyemail+'</b>').appendTo(tdemail),
                    tdhouse = $('<td>').appendTo(tr),
                    house  = $('<span>', {'class':'family-house'}).html('<b>'+familyhouseName+'</b>').appendTo(tdhouse);
            });
            $.mobile.loading( 'show', {
                text: '',
                textVisible: false,
                theme: 'z',
                html: ""
            });
        });

        // Activate family select on clicking family name in mentor list
        $('.mentor-family').click(function(){
            $('.mentor-family-select').hide();
            $('.mentor-family').show();
            var target = $(this).attr('id');
            var id = target.split('-')[2];
            $(this).hide();
            $('#mentor-family-select-'+id).show();
        });

        // Mentor family select box listener
        $('.mentor-family-select').change(function(){
            var target = $(this).attr('id');
            var mentorid = target.split('-')[3];

            var familyid = parseInt($(this).val());
            var familyname = $(this).find("option:selected").text();
            var housename = $(this).find("option:selected").attr('data-house-name');

            if (isNaN(familyid) || (familyid < 1) || isNaN(mentorid) || (mentorid < 1)) {
                return;
            }

            $.ajax({
                url: '/admin/updatementorfamily',
                type: 'POST',
                data: {
                    mentorid: mentorid,
                    familyid: familyid
                },
                dataType: 'json'
            }).done(function(data) {
                $.mobile.loading('hide');

                $('#mentor-family-'+mentorid+' a').html(familyname);
                $('#mentor-house-'+mentorid+' b').html(housename);

                $('#mentor-family-'+mentorid).show();
                $('#mentor-family-select-'+mentorid).hide();
            });
            $.mobile.loading( 'show', {
                text: '',
                textVisible: false,
                theme: 'z',
                html: ""
            });
        });

        // Activate isadmin select on clicking isadmin state in mentor list
        $('.mentor-is-admin').click(function(){
            $('.mentor-is-admin-select').hide();
            $('.mentor-is-admin').show();
            var target = $(this).attr('id');
            var id = target.split('-')[3];
            $(this).hide();
            $('#mentor-is-admin-select-'+id).show();
        });

        // Mentor isadmin select box listener
        $('.mentor-is-admin-select').change(function(){
            var target = $(this).attr('id');
            var mentorid = target.split('-')[4];

            var isadmin = parseInt($(this).val());

            if (isNaN(mentorid) || (mentorid < 1) || ((isadmin !== 1) && (isadmin !== 0))) {
                return;
            }

            $.ajax({
                url: '/admin/updatementoradmin',
                type: 'POST',
                data: {
                    mentorid: mentorid,
                    isadmin: isadmin
                },
                dataType: 'json'
            }).done(function(data) {
                $.mobile.loading('hide');

                var adminText = (isadmin === 1) ? 'Yes' : 'No';
                $('#mentor-is-admin-'+mentorid+' a').html(adminText);

                $('#mentor-is-admin-'+mentorid).show();
                $('#mentor-is-admin-select-'+mentorid).hide();
            });
            $.mobile.loading( 'show', {
                text: '',
                textVisible: false,
                theme: 'z',
                html: ""
            });
        });
    };

    a.validateEmail = function (email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };

    a.setup = function () {
        a.attachListeners();
    }

}());

$(document).on('pageinit', function(evt) {
    imp.admin.index.setup();
});