var button = '#settings-updateProviders';

$(button).on('click', function() {

  var button = this;
  var spinner = '#settings-updateProvidersSpinner';

  $(this).addClass('disabled').html('Updating providers');
  $(spinner).removeClass('hidden');

  Craft.postActionRequest('email-validator/providers/ajax', null, function(response) {
    location.reload();
  });
});
