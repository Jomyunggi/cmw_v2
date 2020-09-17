$(function() {

    $('#side-menu').metisMenu();

});

$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});

 /* popup */
function openPopup(sUrl, nWidth, nHeight, sOption){ 
    var tmp = '';
    tmp += 'width=' + nWidth + ',height=' + nHeight + ',top=' + (screen.availHeight - nHeight-50)/2 + ', left=' + (screen.availWidth - nWidth-10)/2 + ' ,';
    tmp += 'status=no, menubar=no, scrollbars=no, resizable=no, toolbar=no';
    window.open(sUrl, '', tmp);
}