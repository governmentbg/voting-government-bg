/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('bootstrap-table');
require('bootstrap-datepicker');
require('nanoscroller');

$('#vote_organisations').dblclick(function() {
    let selected = $('#vote_organisations option:selected').clone();
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    addRowNumber(selected);
    checkVoteSize();

    // load nanoscroller
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$('#votefor').dblclick(function() {
    $('#votefor option:selected').remove().appendTo($('#vote_organisations')).find('span').remove();
    reorderRowNumbers();
    checkVoteSize();

    // load nanoscroller
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$('#js-add-org').click(function() {
    let selected = $('#vote_organisations option:selected').clone();
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    addRowNumber(selected);
    checkVoteSize();

    // load nanoscroller
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$('#js-remove-org').click(function() {
    $('#votefor option:selected').remove().appendTo($('#vote_organisations')).find('span').remove();
    reorderRowNumbers();
    checkVoteSize();

    // load nanoscroller
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$(document).ready(function() {
    checkVoteSize();
    reorderRowNumbers();
});

function checkVoteSize() {
    let maxVotes = $('#js-voteform').data('max-votes');
    if ($('#votefor option').length == maxVotes) {
        $('#vote_organisations').attr('disabled', true);
    } else {
        $('#vote_organisations').attr('disabled', false);
    }

    if ($('#votefor option').length < 1) {
        $('#votebtn').attr('disabled', true);
    } else {
        $('#votebtn').attr('disabled', false);
    }
}

/**
 * Add row number to organisations list.
 */
function addRowNumber(selectedOrgs) {
    selectedOrgs.each(function(i, org) {
        let orgInList = $('#votefor option[value="'+ $(org).attr('value') +'"]');
        let index = orgInList.index() + 1;
        $(org).find('span').remove();
        $('#votefor option[value="' + $(org).attr('value') +'"]').html('<span>'+ index +' - </span>' + $(org).text());
    });
}

/**
 * Reorder row numbers. After that there are only consecutive numbers.
 * @returns {void}
 */
function reorderRowNumbers() {
    $('#votefor option').each(function(i, org) {
        $(org).find('span').remove();
        $(org).html('<span>'+ ($(org).index() + 1) +' - </span>' + $(org).text());
    });
}

$(document).ready(function() {
    var lastSelect = null;

    $('#vote_organisations').on('change', function(event) {

      if ($(this).val().length > ($('#js-voteform').data('max-votes') - $('#votefor option').length)) {

        $(this).val(lastSelect);
      } else {
        lastSelect = $(this).val();
      }
    });
  });

// Disable enter key submit of vote form to honor js rules
$(document).on("keydown", "#js-voteform", function(event) {
    return event.key != "Enter";
});

$('#filter_org').on('keyup', function() {
    var input = document.getElementById('filter_org').value.toLowerCase();
    var output = document.getElementById('vote_organisations').options;

    for (var i = 0; i < output.length; i++) {
        if (output[i].text.toLowerCase().indexOf(input) < 0) {
            output[i].style.display = 'none';
            output[i].setAttribute('style', 'display:none');
        } else {
            output[i].style.display = '';
            output[i].setAttribute('style', 'display:block');
        }
    }
});

$('.js-drop-filter').on('change', function() {
    $('.js-drop-filter').closest('form').submit();
});

$('.js-search').on('keydown', function(e) {
    if (e.which == 13) {
        $('.js-search').closest('form').submit();
    }
});

$('[name="is_candidate"].checkbox-ams').on('change', function() {
    if ($(this).is(':checked')) {
        $('.for_org_candidates').removeClass('d-none');
    } else {
        $('.for_org_candidates').addClass('d-none');
    }
});

$(document).on('click', '.js-file-upl', function (e) {
    e.preventDefault();

    var input = $(this).parents('.file-input-container').find('.js-file-input');

    input.trigger('click');
});

$(document).on('change', '.js-file-input', function (e) {
    let $el = $(this);

    if ($el.val() != '') {
        $el.prev('span').text($el.val().split('\\').pop());
    }
});

$(document).on('click', '.js-plus-file-upl', function (e) {
    e.preventDefault();

    let $container = $(this).parents('.multiple-input-container');
    var html = $container.find('.file-input-container').last().prop('outerHTML');

    html = $(html);
    html.find('span').text('Select file');

    $('.add-file-input').before(html);
});

$('.js-showTerms').on('click', function() {
    $('#info').modal('show');
});

var $th1 = $('.tableFixHead').find('thead tr:first-child th');
var $th2 = $('.tableFixHead').find('thead tr:nth-child(2) th');

$(document).ready(function () {
    $th1.css('transform', 'translateY(0px)');
    if ($th2.length > 0) {
        $th2.css('transform', 'translateY(-1px)');
    }
});

$('.tableFixHead').on('scroll', function() {
    $th1.css('transform', 'translateY('+ this.scrollTop + 'px)');
    $th1.css('background-color', '#3e7ea9');
    if ($th2.length > 0) {
        $th2.css('transform', 'translateY('+ (this.scrollTop - 1) + 'px)');
        $th2.css('background-color', '#3e7ea9');
    }
});

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {
    if ($('.flash-message').length) {
        $('.flash-message').fadeOut(10000);
    }
});

$('.js-focusout-submit').on('focusout', function() {
    $(this).closest('form').submit();
});

$('.js-change-submit').on('change', function() {
    $(this).closest('form').submit();
});

$('#votebtn').on('click', function() {
    $('#info').modal('show');
});

$('#votebtn').click(function(ev) {
    var selected = $('#votefor > option');

    $('#info').find('.modal-body').text('');
    selected.each(function(index, value) {
        $('#info').find('.modal-body').append('<div>' + value.innerHTML + '<\div>');
    })

    selected.each(function() {
        $(this).attr('selected', true);
    });

    $('#info').modal({ show: true });

    $('#dataConfirmOK').off('click').on('click', function() {
        // On submit add select to all items in votefor select
        // in order to submit them all (even not selected from prev vote)
        selected.each(function() {
            $(this).prop('selected', true);
        });

        $('#info').closest('form').submit();
    });

    return false;
});

// voting tour update - send vote invites confirmation
$('form.change-tour').submit(function(e) {
    e.preventDefault();
    let oldValue = $('form.change-tour [name="status"]').data('old-status');
    let status = $('form.change-tour [name="status"]').val();
    let modal = $('#confirmEmailSending');
    modal.find('.modal-body [type="checkbox"][name="send_emails"]').hide();
    modal.find('.modal-body .checkbox-container label').hide();

    if ((status == 3 || status == 5 || status == 4) && status != oldValue) { //status - voting
        modal.modal();

        if (status == 3) {
            modal.find('.modal-title').text($('#translations').data('voting'));
            modal.find('.modal-body .text').text($('#translations').data('status-change-confirm'));
            modal.find('.modal-body .checkbox-container label').show();
        }

        if (status == 4) {
            modal.find('.modal-title').text($('#translations').data('ranking'));
            modal.find('.modal-body .text').text($('#translations').data('confirm-ranking'));
            modal.find('.modal-body .checkbox-container label').show();
        }

        if (status == 5) {
            modal.find('.modal-title').text($('#translations').data('ballotage'));
            modal.find('.modal-body .text').text($('#translations').data('status-change-confirm'));
            modal.find('.modal-body .checkbox-container label').show();
        }

        modal.find('input[name="send_emails"]').change(function() {
            if ($(this).prop('checked')) {
                modal.find('.confirm').attr('disabled', false);
            } else {
                modal.find('.confirm').attr('disabled', true);
            }
        });
    } else if (status == 1 && status != oldValue) {
        modal.modal();
        modal.find('.modal-title').text($('#translations').data('opened-reg'));
        modal.find('.modal-body .text').text($('#translations').data('confirm'));
        modal.find('.confirm').attr('disabled', false);
    } else if (status == 2 && status != oldValue) {
        modal.modal();
        modal.find('.modal-title').text($('#translations').data('closed-reg'));
        modal.find('.modal-body .text').text($('#translations').data('confirm'));
        modal.find('.confirm').attr('disabled', false);
    } else if (status == 6 && status != oldValue) {
        modal.modal();
        modal.find('.modal-title').text($('#translations').data('finished'));
        modal.find('.modal-body .text').text($('#translations').data('confirm'));
        modal.find('.confirm').attr('disabled', false);
    } else {
        $(this).unbind('submit').submit();
    }
});

$('#confirmEmailSending .confirm').click(function() {
    $('form.change-tour').unbind('submit').submit();
});

$(document).on('click', '.additional-info', function() {
    $(this).closest('tr').siblings('tr').css('font-weight', 'normal');
    $(this).closest('tr').css('font-weight', 'bold');

    $.ajax({
        type: 'POST',
        url: '/api/organisation/getData',
        data: {
            org_id: $(this).data('org-additional-id')
        },
        success: function(result) {
            $('#additional_header').text(result.data.name);
            $('#additional_name').text(result.data.name);
            $('#additional_eik').text(result.data.eik);
            $('#additional_address').text(result.data.address);
            $('#additional_representative').text(result.data.representative);
            $('#additional_reg_date').text(result.data.created_at);
            $('.hidetable').css('visibility', 'visible');
        },
        fail: function(result) {
            $('.hidetable').css('visibility', 'hidden');
        }
    });
});

$(function() {
    $('#reg_date_from, #period_from').click(function() {
        $('[name="reg_date_from"], [name="filters[date_from]"], [name="period_from"]').datepicker('show');
    });

    $('#reg_date_to, #period_to').click(function() {
        $('[name="reg_date_to"], [name="filters[date_to]"], [name="period_to"]').datepicker('show');
    });
});

$('input[required], textarea[required]').on('invalid', function() {
    let message = $(this).attr('title');
    if (message) {
        this.setCustomValidity(message);
    }
    let inset = $(this).is('input') ? '' : 'inset ';
    $(this).css('box-shadow', inset +'0 0 1.5px 1px red');
});

$('input[required], textarea[required]').on('input', function() {
    this.setCustomValidity('');
    $(this).css('box-shadow', 'none');
});

$('input[required], textarea[required]').on('focusout', function() {
    if ($(this).val() == '') {
        let inset = $(this).is('input') ? '' : 'inset ';
        $(this).css('box-shadow', inset +'0 0 1.5px 1px red');
    } else {
        $(this).css('box-shadow', 'none');
    }
});

$(function() {
    $('#registerOrg input[name="eik"]').on('focusout', function() {
        if ($.isNumeric(eik = $('#registerOrg input[name="eik"]').val())) {
            $.ajax({
                type: 'POST',
                url: 'predefinedData',
                data: $('#registerOrg input[name="eik"], #registerOrg input[name="_token"]').serialize(),
                success: function(result) {
                    result = JSON.parse(result);
                    if (jQuery.type(result.data) !== 'undefined' && !jQuery.isEmptyObject(result.data)) {
                        result.data['address'] = result.data.fullAddress;
                        ['name', 'address', 'representative', 'phone', 'email'].forEach(function(field) {
                            var input = $('#registerOrg input[name="'+ field +'"]');
                            input.val(result.data[field]);
                            if (input.val() != '') {
                                input[0].setCustomValidity('');
                                input.css('box-shadow', 'none');
                            }
                        });
                    }
                }
            });
        }
    });
});

var clicks = 0;

$('.ams-dropdown').click(function() {
    if (clicks == 0) {
        $(this).parent().find('.caret').toggleClass('rotateCaret');
    } else {
        $(this).parent().find('.caret').toggleClass('rotateCaretBack');
    }

    ++clicks;
});

// On lost focus remove classes and counter so next entry will have correct logic
$('.ams-dropdown').on('blur', function() {
    $(this).parent().find('.caret').removeClass('rotateCaretBack');
    $(this).parent().find('.caret').removeClass('rotateCaret');
    clicks = 0;
});

var initialPage = 2;

$('.js-org-table').on('scroll', function() {
    if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        var lastEntryNumber = parseInt($('.js-orgs tr:last-child > td:first-child').text());

        $.ajax({
            type: 'GET',
            url: $('.js-orgs').data('ajax-url'),
            data: {
                'page' : initialPage,
                'consecNum' : lastEntryNumber
            },
            success: function(result) {
                $('.js-orgs').append(
                    result
                );

                initialPage += 1;
            },
            async: false
        });
    }
});

// General initialization of nanoscroll for project
$('.nano').nanoScroller({ sliderMaxHeight: 100 });

if ($('.public-table').length > 0) {
    var theadHeight = $('.tableFixHead').find('thead').innerHeight();
    if ($th2.length > 0) {
        $('.public-table .nano-pane').css('margin-top', (theadHeight - 1) +'px');
        $('.public-table .nano-pane .nano-slider').css('margin-top', '-1px');
    } else {
        $('.public-table .nano-pane').css('margin-top', theadHeight +'px');
        $('.public-table .nano-pane .nano-slider').css('margin-top', '-2px');
    }
}

// Fixes a bug where nanoscroller is not initialized on front page
$(document).ready(function() {
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

// Fixes a bug where nanoscroll is not initialized for textareas
// while inputting text
$('.txt-area-height').on('keyup', function() {
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$('#orgList').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;

    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

$('form.edit-org').submit(function(e) {
    if ($('.edit-status').data('old-status') != 6 && $('.edit-status').val() == 6) {
        e.preventDefault();
        let modalDeclass = $('#confirmOrgDeclass');
        modalDeclass.modal();
    }
});

$('#confirmOrgDeclass .confirm').click(function() {
    $('form.edit-org').unbind('submit').submit();
});

$('#showRegs').click(function() {
    $('.regs-tables').show();
    $(this).hide();
    $('.cross-close').show();
});

$('.cross-close').click(() => {
    $('.regs-tables').hide();
    $('.cross-close').hide();
    $('#showRegs').show();
});

$('.reg-exp').on('focusout', () => {
    if ($('.reg-exp')[0].value.match(/[a-zA-Z]/)) {
        $('.reg-exp').css('border', '3px solid #3e7ea9');
        $('.warning').removeClass('alert-info');
        $('.warning').addClass('alert-warning');
        $('.warning').css('font-weight', 'bold');
    } else {
        $('.warning').removeClass('alert-warning');
        $('.warning').addClass('alert-info');
        $('.reg-exp').css('border', '1px solid #3e7ea9');
        $('.warning').css('font-weight', 'normal');
    }
});
