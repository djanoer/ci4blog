function showCustomAlert(message, type = 'success') {
    // Hapus alert yang sudah ada
    $('.custom-alert').remove();
    
    // Buat elemen alert baru
    var alertElement = $('<div class="custom-alert ' + type + '">' +
        '<span class="message">' + message + '</span>' +
        '<button class="close-btn" onclick="hideAlert(this)">×</button>' +
    '</div>');
    
    // Tambahkan ke body
    $('body').append(alertElement);
    
    // Tampilkan alert
    setTimeout(function() {
        alertElement.addClass('show');
    }, 100);
    
    // Hilangkan alert setelah 3 detik
    setTimeout(function() {
        hideAlert(alertElement.find('.close-btn')[0]);
    }, 3000);
}

function hideAlert(button) {
    const alert = $(button).closest('.custom-alert');
    alert.removeClass('show').addClass('hide');
    setTimeout(() => alert.remove(), 500); // Hapus dari DOM setelah animasi selesai
}
