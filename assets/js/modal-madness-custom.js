jQuery(document).ready(function($) {
  if ($('.open-modal-btn').length && $.magnificPopup) {
    $('.open-modal-btn').magnificPopup({
      type: 'inline',
      midClick: true
    });
  } else {
    console.warn('Modal button or Magnific Popup not available.');
  }
}); 