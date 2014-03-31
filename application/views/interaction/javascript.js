var imp = imp || {};
imp.interaction = imp.interaction || {};

(function(){
    var util = imp.interaction;

    util.setup = function() {
        util.attachListeners();
    };
    
    util.changeSubList = function () {
        var typeId = $("input[name='log-types-choice']:radio:checked").val();
        var subTypes = imp.interaction.types[typeId].sub_types;

        var subList = $('#logSubtypesDropdown');
        subList.empty();

        var opt = '';
        jQuery.each(subTypes, function(i,e){
            opt = $('<option/>', {'value' : e.id, 'text' : e.name}).appendTo(subList);
            if (i === 0) {
                opt.attr('selected', 'selected');
                $('.log-subtypes .ui-btn-text span').html(e.name);
            }
        });
    };

    util.logInteraction = function () {
        var familyId = parseInt($('#logFamilyId').attr('data-family-id'));
        var familyName = $('#logFamilyId').val();
        var dateInteraction = $('#logDate').val();
        var duration = parseInt($('#logMinutes').val());
        var typeId = parseInt($('#logSubtypesDropdown').val());
        var desc = $('#logDesc').val();

        var monthfield = dateInteraction.split("-")[1];
        var dayfield   = dateInteraction.split("-")[2];
        var yearfield  = dateInteraction.split("-")[0];

        if (monthfield && monthfield.length === 1) {
            monthfield = '0' + monthfield;
        }
        if (dayfield && dayfield.length === 1) {
            dayfield = '0' + dayfield;
        }

        var datePadded = yearfield + '-' + monthfield + '-' + dayfield;

        var errMsg = '';
        var error = false;

        if (familyId < 1 || isNaN(familyId)) {
            error = true;
            errMsg = 'Please enter a valid family';
        } else if (dateInteraction == '') {
            error = true;
            errMsg = 'Please enter a valid date';
        } else if (Date.parse(datePadded) > Date.parse(imp.interaction.today) || Date.parse(datePadded) < Date.parse('2000-01-01')) {
            error = true;
            errMsg = 'Please enter a date on/before today';
        } else if (!(util.validateDate(datePadded))) {
            error = true;
            errMsg = 'Please enter date in format YYYY-MM-DD';
        } else if (duration == '' || duration < 1 || isNaN(duration)) {
            error = true;
            errMsg = 'Please enter a valid duration';
        } else if (typeId < 1  || isNaN(typeId)) {
            error = true;
            errMsg = 'Please enter a valid type';
        } else if (desc == '') {
            error = true;
            errMsg = 'Please enter a description';
        } else if (desc.length > 5000) {
            error = true;
            errMsg = 'Description is too long';
        }

        if (error) {
            util.alertPopup(errMsg, 'errorPopup');
            return;
        }

        $.ajax({
            url: '/interaction/addnew',
            type: 'POST',
            dataType: 'json',
            data: {
                familyid: familyId,
                familyname: familyName,
                dateinteraction: datePadded,
                duration: duration,
                typeid: typeId,
                desc: desc
            },
            success: function(data) {
                $.mobile.loading('hide');
                window.location = '/home/index';
                /*util.resetForm();
                util.alertPopup('Thanks for logging your interaction', 'successPopup');*/
            }
        });
        $.mobile.loading( 'show', {
            text: '',
            textVisible: false,
            theme: 'z',
            html: ""
        });
    };

    util.alertPopup = function (msg, popupId) {
        $('#' + popupId + ' p').html(msg);
        $('#' + popupId + 'Lnk').click();
    };

    util.resetForm = function () {
        $('#logDate').val('');
        $('#logMinutes').val('');
        $('#logDesc').val('');
        $('#logTypesChoice1').prop('checked',true);
        $('#logTypesChoice1').click();
        $("input[name='log-types-choice']").checkboxradio('refresh');
    };

    util.validateDate = function(date) {
        var validformat=/^\d{4}\-\d{2}\-\d{2}$/; //Basic check for format validity

        if (!validformat.test(date)) {
            return false;
        } else { //Detailed check for valid date ranges
            var monthfield=date.split("-")[1];
            var dayfield=date.split("-")[2];
            var yearfield=date.split("-")[0];
            var dayobj = new Date(yearfield, monthfield-1, dayfield);
            if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {
                return false;
            } else {
                return true;
            }
        }
    };

    util.attachListeners = function () {
        $("input[name='log-types-choice']").click(util.changeSubList);

        $('#logSubmitButton').click(util.logInteraction);
        
        // Listen for when the student name is clicked
        $('.family-container #logFamilyId').click(function(evt) {
            // Overlay the search container
            if (!$('.family-container .search-container').is(':visible')) {
                // Empty out the search field
                $('.family-container .search-container input[data-type="search"]').val('');
                
                // Empty out any previous results
                $('.family-container .search-listview').empty();
                
                // Show everything
                $('.family-container .search-container').show();
                
                // Force focus on the search field
                $('.family-container .search-container input[data-type="search"]').focus();
            }
        });
        
        // Hide the search box when the search field loses forcus
        $('.family-container .search-container input[data-type="search"]').blur(function(evt) {
            if ($(this).val() == '') {
                $('.family-container .search-container').hide();
            }
        });
        
        // Listen for when the user starts typing in a student name to search for
        $('.search-listview').on('listviewbeforefilter', function(e, data) {
            var $ul = $(this),
                $input = $(data.input),
                value = $input.val(),
                html = '';
            $ul.html('');
            if (value && value.length > 1) {
                $ul.html('<li><div class="ui-loader"><span class="ui-icon ui-icon-loading"></span></div></li>');
                $ul.listview('refresh');
                $.ajax({
                    url: '/home/getfamilies',
                    type: 'POST',
                    data: {
                        q: value
                    },
                    dataType: 'json'
                }).done(function(data) {
                    var families = data.families,
                        regex = new RegExp('(' + value.toLowerCase() + ')', 'i');
                    
                    // Clear the result list
                    $ul.empty();
                    
                    // Insert each result into the list
                    $.each(families, function(i, family) {
                        var li = $('<li>'),
                            familyLink = $('<a>', {
                                'data-family-id': family.familyid,
                                'data-family-name': family.familyname
                            }).html('<img src="/assets/img/family_42x42.png" class="mentor-profile-img"></i>' + family.familyname.replace(regex, "<strong>$1</strong>") + '<br /><span class="additional-details"><span class="role">Student</span> | ' + family.housename + ' House</span>').appendTo(li);
                        // When selecting an option, set the house/family for the user
                        familyLink.click(function(evt) {
                            var familyId = $(this).attr('data-family-id'),
                                familyName = $(this).attr('data-family-name');
                            
                            // Set the text of the input to the family name
                            $('#logFamilyId').val(familyName);
                            
                            // Update the family ID data attribute on the input
                            $('#logFamilyId').attr('data-family-id', familyId);
                            
                            // Hide the search container
                            $('.family-container .search-container').hide();
                        });
                        
                        $ul.append(li);
                    });
                    
                    // Refresh the list
                    $ul.listview('refresh');
                    $ul.trigger('updatelayout');
                });
            }
        });

        $('textarea[maxlength]').keyup(function(){
            var max = parseInt($(this).attr('maxlength'));
            if($(this).val().length > max){
                $(this).val($(this).val().substr(0, $(this).attr('maxlength')));
            }
            $(this).parent().find('.chars-remaining').html('You have ' + (max - $(this).val().length) + ' characters remaining');
        });

        $('#logTimingsContainer input[type="date"]').click(function(){
            $('#logTimingsContainer a.ui-input-clear').addClass('ui-input-clear-hidden');
            $(this).val('');
        });
    };

}());
