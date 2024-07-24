function showCustomAlert(message, type = 'success') {
    // Hapus alert yang sudah ada
    $('.custom-alert').remove();
    
    // Buat elemen alert baru
    var alertElement = $('<div class="custom-alert ' + type + '">' + message + '</div>');
    
    // Tambahkan ke body
    $('body').append(alertElement);
    
    // Tampilkan alert
    setTimeout(function() {
        alertElement.addClass('show');
    }, 100);
    
    // Hilangkan alert setelah 3 detik
    setTimeout(function() {
        alertElement.removeClass('show');
        setTimeout(function() {
            alertElement.remove();
        }, 300);
    }, 3000);
}