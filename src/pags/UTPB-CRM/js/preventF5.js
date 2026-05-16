// Disable key press
function disableKeyPressing(e) {

    // keycodes table https://css-tricks.com/snippets/javascript/javascript-keycodes/
    var conditions = [
        // Diable F5
        (e.which || e.keyCode) == 116,
        // Diable Ctrl+R
        e.ctrlKey && (e.which === 82)
    ]

    if ( $.each(conditions, function(key, val) { val + ' || ' }) ) {
        e.preventDefault();
    }
}

// F5 is pressed
$(document).on('keydown', function(e) {
    // F5 is pressed
    if((e.which || e.keyCode) == 116) {
        disableKeyPressing(e);
        console.log('F5 is diabled now');
    }

    // Ctrl+R
    if (e.ctrlKey && (e.which === 82)) {
        disableKeyPressing(e);
        console.log('Ctrl+R is pressed and refresh is diabled now');
    }
});

// More simple way to do this, but it ignites on any keypress
// $(document).on('keydown', disableKeyPressing);