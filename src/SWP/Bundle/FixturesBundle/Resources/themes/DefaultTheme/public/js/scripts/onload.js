﻿window.sticky_menu = {
    active: false,

    init: function (){
        $(document).on("scroll", function() {
            var scroolTop = $(document).scrollTop();
            if( scroolTop > 153 && !sticky_menu.active){
                sticky_menu.activate();
            }else if(scroolTop < 153 && sticky_menu.active){
                sticky_menu.deactivate();
            }
      });
    },

    activate: function (){
        $(".menu_wrap").addClass("menu_fixed");
        sticky_menu.active=true;
    },

    deactivate: function (){
        $(".menu_wrap").removeClass("menu_fixed");
        sticky_menu.active=false;

    }

}

/* User login, registration, profile edit */

$('#loginform').submit(function(event) {                    
    // Stop form from submitting normally
    event.preventDefault();
                    
    var $inputs = $('#loginform :input');
    var values = {};
    var userName = "",
        passWord = "";
    $inputs.each(function() {
        values[this.name] = $(this).val();
    });

    var apiKey;

    // obtain user token
    if ( typeof(username) !== "undefined" ) {
        var data = {'auth': {'username': values["_username"], 'password': values["_password"]}},
        url = "/app_dev.php/api/v1/auth/";

        // Send the data using post
        var postingAuth = $.post( url, data ); 

        // Put the user values in editable form
        postingAuth.done(function( data ) {
            var content = $( data ); 
            apiKey = content["0"]["token"]["api_key"];
            user = content["0"]["user"];
            document.getElementById("_usernameP").setAttribute("value", user["username"]);
            document.getElementById("_emailP").setAttribute("value", user["email"]);
            if(user["firstName"]){
                document.getElementById("_fnameP").setAttribute("value", user["firstName"]);
            };
            if(user["lastName"]){
                document.getElementById("_lnameP").setAttribute("value", user["lastName"]);
            };
            var nodeTxtAr = document.createTextNode( user["about"] );
            if(user["about"]){
                element = document.getElementById("_aboutP");
                element.appendChild(nodeTxtAr);
            };
        });

        // If posting is unsuccessful, then:
        postingAuth.fail(function(xhr) {
            var content = $.parseJSON( xhr.responseText );    
            console.log( content );
        }); 

        $('#editProfile').submit(function(event) {
            // Stop form from submitting normally
            event.preventDefault();

            // Get updated values from the form:
            var $form = $( this ),
            username = $form.find( "input[name='_username']" ).val(),
            email = $form.find( "input[name='_email']" ).val(),
            firstName = $form.find( "input[name='_fname']" ).val(),
            lastName = $form.find( "input[name='_lname']" ).val(),
            about = $form.find( "textarea[name='_about']" ).val(),
            password = $form.find( "input[name='_password']" ).val(),
            password2 = $form.find( "input[name='_password2']" ).val();
                            
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'Authorization' : apiKey
                },
                url : "/app_dev.php/api/v1/users/profile/" + user["id"],
                type : 'PATCH',
                dataType : 'json',
                data : JSON.stringify({'user_profile': { 'email': email, 'username' : username, 'firstName' : firstName, 'lastName' : lastName, 'about' : about,  'plainPassword' : { 'first' : password, 'second' : password2 }}}),
                success : function(response, textStatus, jqXhr) {
                    var head = document.createElement("h5");
                    var nodeH5 = document.createTextNode( "Your user profile was successfuly saved." );
                    head.appendChild(nodeH5);
                    var element = document.getElementById("editProfileResult");
                    element.appendChild(head);
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    var para = document.createElement("p");
                    var head = document.createElement("h5");
                    var nodeH5 = document.createTextNode( "Error occured:" );
                    var nodeP = document.createTextNode( textStatus +  errorThrown ); 
                    para.appendChild(nodeP);
                    head.appendChild(nodeH5);
                    var element = document.getElementById("editProfileResult");
                    element.appendChild(head);
                    element.appendChild(para);      
                }
            });
        }); 
    }                       
    };                  
});

// Will use these to remove unnecessary HTML nodes from modal
    var formLogin = document.getElementById("loginform"),
    loginHeader = document.getElementById("loginHeader"),
    parentOfHead = document.getElementById("childHolder"),
    formRegister = document.getElementById("registerForm"),
    parentDiv = document.getElementById( "formsHolder" );

    // Attach a submit handler to the form
    $( "#registerForm" ).submit(function( event ) {

    // Stop form from submitting normally
    event.preventDefault();

    // Get some values from elements on the page:
    var $form = $( this ),
    email = $form.find( "input[name='_email']" ).val(),
    username = $form.find( "input[name='_username']" ).val(),
    password = $form.find( "input[name='_password']" ).val(),
    password2 = $form.find( "input[name='_password2']" ).val(),
    url = $form.attr( "action" );
    data = {'user_registration': { 'email': email, 'username' : username, 'plainPassword' : { 'first' : password, 'second' : password2 }}};

    // Send the data using post
    var postingReg = $.post( url, data );

    // Put the results in a div
    postingReg.done(function( data ) {
        var content = $( data );
        parentDiv.removeChild( formLogin );
        parentDiv.removeChild( formRegister );
        Array.prototype.slice.call(document.getElementsByTagName('h4')).forEach(function(item) {
        item.remove();
    });       
    $( "#registrationResult" ).empty().append( content );
}); 

// If posting is unsuccessful, then:
posting.fail(function(xhr) {
    var content = $.parseJSON( xhr.responseText );
    var para = document.createElement("p");
    var head = document.createElement("h5");
    var nodeH5 = document.createTextNode( "Error code " + content.code + ":" );
    var nodeP = document.createTextNode( content.message );
    para.appendChild(nodeP);
    head.appendChild(nodeH5);
    parentDiv.removeChild( formLogin );
    parentOfHead.removeChild( loginHeader );    
    var element = document.getElementById("registrationResult");
    element.appendChild(head);
    element.appendChild(para);
});
}); 

/* jQuery
``````````````````````````````````````````````````````````````````````````` */
window.onload = function() {
    $('#masonry_container').masonry({
        columnWidth: function(containerWidth) {
            return containerWidth / 4;
        }
    });


};

$(document).ready(function() {




    sticky_menu.init();

    /* mobile menu handlers */
    $("#mobilemenuopen").click(function (e){
        e.preventDefault();

        $("#mobile_lang").css("display","none");
        $("#mobile_sections").css("display","block");
        var menu = $("#mobilemenu");
        menu.hasClass('open') ? menu.removeClass('open') : menu.addClass('open');

    });

    $("#mobilelangopen").click(function (e){
        e.preventDefault();

        $("#mobile_lang").css("display","block");
        $("#mobile_sections").css("display","none");
        var menu = $("#mobilemenu");
        menu.hasClass('open') ? menu.removeClass('open') : menu.addClass('open');

    });

    /* swipe galleries */

    if (typeof galleryLinksContainer !== 'undefined' && galleryLinksContainer.length > 0) {

        var galleries = [];

        var carouselOptions = {
            startSlideshow: false
        };



        $.each(galleryLinksContainer, function(i, item) {


            var gallery = blueimp.Gallery(item, {
                container: '#blueimp-image-carousel_' + i,
                carousel: true,
                startSlideshow: false,

                onslide: function(index, slide) {

                    var galleryContainer = this.options.container;

                    var caption = this.list[index].title;



                    $(galleryContainer).parent().find(".slide-caption").html(caption);




                }
            });

            galleries.push(gallery);

        }); // each end



        // fullscreen gallery
        var fullscreen_gallery;


        $(".fullscreenButton").bind("click", function(e) {
            e.preventDefault();
            var gallery_index = $(this).attr('data-gallery');

            var galleryImage_index = galleries[gallery_index].getIndex();

            fullscreen_gallery = blueimp.Gallery(galleryLinksOriginalContainer[gallery_index], {
                container: '#blueimp_fullscreen',
                carousel: true,
                startSlideshow: false,
                closeOnSlideClick: true,
                closeOnEscape: true,
                closeOnSwipeUpOrDown: true,
                disableScroll: true,
                enableKeyboardNavigation: true,
                index: galleryImage_index,

                onslide: function(index, slide) {

                    var galleryContainer = this.options.container;

                    $(galleryContainer).find(".caption").html(this.list[index].title);



                }

            });
        });


    }



    $('#poll-button').click(function() {
        $.post($('form[name=debate]').attr("action"), $('form[name=debate]').serialize(), function(data) {
            $('#polldiv').html(data);
        });
        return false;
    });


    $('.tabs_box').each(function(index, el) {
        $(el).hsTabs({
            'headers': $(el).find('.tabs_menu li'),
            'contents': $(el).find('.tabs_contents .tabs_content')
        });
    });


    $('.people_box').hsTabs({
        'headers': $('.people_box .tabs_menu li'),
        'contents': $('.people_box .tabs_contents .tab_content')
    });

    $('.people_box').hsSlide({
        'elements_wrapper': $('.people_box .people_wrapper'),
        'elements_container': $('.people_box .people'),
        'elements': $('.people_box .people .span3'),
        'per_row': 4,
        'arrow_left': $('.people_box .navigation').find('.arrow_left'),
        'arrow_right': $('.people_box .navigation').find('.arrow_right'),
        'navigation': $('.people_box .navigation').find('.dot')
    });

    $('.contact_boxes .box').setAllToMaxHeight();

    $('#login_popup').hsPopup({
        'open_popup_link': $('.open_login_popup'),
        'close_popup_link': $('#login_popup .close')
    });

    $('#user_profile').hsPopup({
        'open_popup_link': $('.open_user_profile'),
        'close_popup_link': $('#user_profile .close')
    });    


    var frm = $('#loginform');
    frm.submit(function() {
        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function(data) {
                if (data == "ERROR") {
                    frm.find("input[type=email], input[type=password]").css("border", "1px solid red");
                } else {
                    // frm.css("display", "none");
                    // $(".logininfo").css("display", "block");
                    // $('#login_popup').fadeOut();
                    // $("#cover").fadeOut();
                    // $("#registerButtonFront").css("display", "none");
                    // $(".open_login_popup").css("display", "none");
                    document.location.reload();

                }
            }
        });

        return false;
    });

    $('#outer_side_menu').scrollToFixed();
    $('#top_menu').hsSubMenu();
});

(function($) {

    /* ------------- */
    /* hsSubMenu */
    /* ------------- */

    $.fn.hsSubMenu = function(options) {

        var settings = {

        };

        $.extend(settings, options);

        var menu = $(this);
        var hide_menu = false;

        menu.find('.menu > li').each(function(index, el) {

            if ($(el).find('ul').length > 0) {
                if (!$(el).hasClass('current')) {
                    $(el).hover(
                        function() {
                            clearTimeout(hide_menu);
                            menu.find('.menu > li ul').css('display', 'none');
                            $(el).find('ul').css('display', 'block');

                            //menu.find('.menu > li ul').slideUp();
                            //$(el).find('ul').slideDown();

                            if (!menu.hasClass('open_submenu')) {
                                //menu.animate.({'height': 60});
                                menu.css({
                                    'height': 60
                                });
                            }
                        },
                        function() {
                            hide_menu = setTimeout(function() {
                                $(el).find('ul').css('display', 'none');
                                //$(el).find('ul').slideUp();

                                if (!menu.hasClass('open_submenu')) {
                                    //menu.animate.({'height': 44});
                                    menu.css({
                                        'height': 44
                                    });
                                }
                            }, 500);

                        }
                        );


}
}
});
},

/* ------------- */
/* hsPopup */
/* ------------- */

$.fn.hsPopup = function(options) {

    var settings = {

    };

    $.extend(settings, options);

    var popup = $(this);
    var cover = $('#cover');

    settings['open_popup_link'].click(function(event) {
        event.preventDefault();

        open_popup($(this));
    });

    cover.click(function(event) {
        event.preventDefault();

        close_popup($(this));
    });

    settings['close_popup_link'].click(function(event) {
        event.preventDefault();

        close_popup($(this));
    });


    function open_popup() {
        popup.fadeIn();
        cover.fadeIn();
    }

    function close_popup() {
        popup.fadeOut();
        cover.fadeOut();
    }
},

/* ------------- */
/* hs_slide */
/* ------------- */

$.fn.hsSlide = function(options) {

    var settings = {
        'per_row': 1
    };

    $.extend(settings, options);

    var current_page = 0;

    var main = $(this);
    var elements_wrapper = settings['elements_wrapper'];
    var elements_container = settings['elements_container'];
    var elements = settings['elements'];

    var nr_pages = Math.ceil(elements.length / settings['per_row']);

    calculate_dimensions();

    scroll_to(0);

    attach();

    function attach() {

        if (settings['arrow_left']) {
            settings['arrow_left'].click(function(event) {
                event.preventDefault();

                previous();
            });
        }

        if (settings['arrow_right']) {
            settings['arrow_right'].click(function(event) {
                event.preventDefault();

                next();
            });
        }

        if (settings['navigation']) {
            settings['navigation'].click(function(event) {
                event.preventDefault();

                current_page = $(this).index() - 1;

                scroll_to(current_page);
            });
        }

        $(window).resize(function() {
            calculate_dimensions();
        });
    }

    function calculate_dimensions() {
            //elements.width(elements_container.width());
            var whole_width = 0;
            elements.each(function(index, el) {
                whole_width += $(el).width() + parseInt($(el).css('margin-left')) + parseInt($(el).css('margin-right'));
            });

            //element_width = elements.eq(0).width();

            //elements_container.height(elements.getMaxHeight());
            //elements_container.width(elements.length * element_width);
            elements_container.width(whole_width);

        }

        function next() {
            if (current_page == nr_pages - 1) {
                current_page = 0;
            } else {
                current_page += 1;
            }

            scroll_to(current_page);
        }

        function previous() {
            if (current_page == 0) {
                current_page = nr_pages - 1;
            } else {
                current_page -= 1;
            }

            scroll_to(current_page);
        }

        function scroll_to(current_page) {
            //console.log(current_page);
            if (settings['navigation']) {
                settings['navigation'].removeClass('current');
                settings['navigation'].eq(current_page).addClass('current');
            }

            var elements_scolling_index = settings['per_row'] * current_page;

            if (elements_scolling_index > elements.length - 1) {
                elements_scolling_index = elements.length - 1;
            }

            elements_wrapper.scrollTo(elements.eq(elements_scolling_index), 500, {
                axis: 'x'
            });
        }

    },

    /* ------------- */
    /* hsTabs */
    /* ------------- */

    $.fn.hsTabs = function(options) {

        var settings = {
            'headers': null,
            'contents': null
        }

        $.extend(settings, options);

        var main = $(this);

        attach();
        change_content(0);

        function attach() {
            settings['headers'].click(function(event) {
                event.preventDefault();

                change_content($(this).index());
            });
        }

        function change_content(index) {
            settings['headers'].removeClass('current');
            settings['headers'].eq(index).addClass('current');

            settings['contents'].css('display', 'none');
            settings['contents'].eq(index).css('display', 'block');
        }

    },

    $.fn.getMaxHeight = function() {
        return (Math.max.apply(this, $.map(this, function(e) {
            return $(e).height()
        })));
    };

    $.fn.setAllToMaxHeight = function() {
        return this.height(Math.max.apply(this, $.map(this, function(e) {
            return $(e).height()
        })));
    };
})(jQuery);
