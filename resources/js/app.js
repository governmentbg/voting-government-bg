
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
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    checkVoteSize();
});

$('#votefor').dblclick(function() {
    $('#votefor option:selected').remove().appendTo($('#vote_organisations'));
    checkVoteSize();
});

$('#js-add-org').click(function() {
    $('#vote_organisations option:selected').remove().appendTo($('#votefor'));
    checkVoteSize();
});

$('#js-remove-org').click(function() {
    $('#votefor option:selected').remove().appendTo($('#vote_organisations'));
    checkVoteSize();
});

$(document).ready(function() {
    checkVoteSize();
});

function checkVoteSize() {
    if ($('#votefor option').length == 14) {
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

$(document).ready(function() {
    var lastSelect = null;

    $('#vote_organisations').on('change', function(event) {

      if ($(this).val().length > (14 - $('#votefor option').length)) {

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

var $th = $('.tableFixHead').find('thead th')
$('.tableFixHead').on('scroll', function() {
    $th.css('transform', 'translateY('+ this.scrollTop + 'px)');
    $th.css('background-color', '#3e7ea9');
});

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {
    if ($('.flash-message').length) {
        $('.flash-message').fadeOut(10000);
    }
});

$('#refresh').click(function() {
    $.ajax({
       type: 'GET',
       url: 'refreshcaptcha',
       success: function(data) {
          $('.captcha span').html(data);
       }
    });
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

//voting tour update - send vote invites confirmation
$('form.change-tour').submit(function(e) {
    e.preventDefault();
    let oldValue = $('form.change-tour [name="status"]').data('old-status');
    let status = $('form.change-tour [name="status"]').val();

    if ((status == 3 || status == 5) && status != oldValue) { //status - voting
        let modal = $('#confirmEmailSending');
        if(status == 3){
            $('.modal-title.voting', modal).removeClass('d-none');
            $('.modal-title.ballotage', modal).addClass('d-none');
        }

        if(status == 5){
            $('.modal-title.voting', modal).addClass('d-none');
            $('.modal-title.ballotage', modal).removeClass('d-none');
        }

        modal.modal();

        modal.find('input[name="send_emails"]').change(function() {
            if ($(this).prop('checked')) {
                modal.find('.confirm').attr('disabled', false);
            } else {
                modal.find('.confirm').attr('disabled', true);
            }
        });
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
            $('#additional_representative').text(result.data.name);
            $('#additional_reg_date').text(result.data.created_at);
            $('.hidetable').css('visibility', 'visible');
        },
        fail: function(result) {
            $('.hidetable').css('visibility', 'hidden');
        }
    });
});

$(function() {
    $('#reg_date_from').click(function() {
        $('[name="reg_date_from"], [name="filters[date_from]"]').datepicker('show');
    });

    $('#reg_date_to').click(function() {
        $('[name="reg_date_to"], [name="filters[date_to]"]').datepicker('show');
    });
});

$('input[required]').on('invalid', function() {
    let message = $(this).attr('title');
    if (message) {
        this.setCustomValidity(message);
    }
});

$('input[required]').on('input', function() {
    this.setCustomValidity('');
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
                        $('#registerOrg input[name="name"]').val(result.data.name);
                        $('#registerOrg input[name="address"]').val(result.data.fullAddress);
                        $('#registerOrg input[name="representative"]').val(result.data.representative);
                        $('#registerOrg input[name="phone"]').val(result.data.phone);
                        $('#registerOrg input[name="email"]').val(result.data.email);
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
        var lastEntryNumber = parseInt($('.js-orgs tr:last-child>td:first-child').text());

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
            }
        });
    }
});

// General initialization of nanoscroll for project
$('.nano').nanoScroller({ sliderMaxHeight: 100 });

// Fixes a bug where nanoscroller is not initialized on front page
$(document).ready(function() {
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

// Fixes a bug where nanoscroll is not initialized for textareas
// while inputting text
$('.txt-area-height').on('keyup', function() {
    $('.nano').nanoScroller({ sliderMaxHeight: 100 });
});

$(window).on('resize load', function () {
    if ($(window).width() < 991) {
        $('.js-var').addClass('table');
        $('.js-var').addClass('table-responsive');
    } else {
        $('.js-var').removeClass('table');
        $('.js-var').removeClass('table-responsive');
    }
});
