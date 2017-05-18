// Hide Header on on scroll down
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var navbarHeight = 50;

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 250);

function hasScrolled() {
    var st = $(this).scrollTop();
    
    // Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        $('body').removeClass('nav-down').addClass('nav-up').removeClass('nav-top');
    } else {
        // Scroll Up
        if(st + $(window).height() < $(document).height()) {
            $('body').removeClass('nav-up').addClass('nav-down').removeClass('nav-top');
        }
        if ($(this).scrollTop() < navbarHeight) {
            $('body').removeClass('nav-up').removeClass('nav-down').addClass('nav-top');
        }
    }
    
    lastScrollTop = st;
}
$( document ).ready(function() {
    // Dialog trigger
    $('[data-a11y-dialog-show]').click(function(e) {
        e.preventDefault();
        dialogId = $(this).attr('data-a11y-dialog-show');
        dialogEl = document.getElementById(dialogId);
        dialog = new A11yDialog(dialogEl);
        dialog.show();
   });
});
