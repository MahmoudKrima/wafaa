$( ".menu-off" ).click(function() {
    $( this ).removeClass( "menu-off" );
    $( this ).addClass( "menu-on" );
    $('.floating-button-menu-close').addClass('menu-on');
});
$('.floating-button-menu-close').click(function(){
    $( this ).addClass( "menu-off" );
    $( this ).removeClass( "menu-on" );
    $('.floating-button-menu').toggleClass('menu-on');
});
