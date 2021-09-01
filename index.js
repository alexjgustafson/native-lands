jQuery(document).ready(function($){
  $('[data-ajgnls-search]').submit(function(event){
    event.preventDefault();
    var blockId = $(this).data().ajgnlsSearch;
    var address = $(`[data-ajgnls-query=${blockId}]`).val();
    if(!address) return;

    // Remove the search field and add in-progress spinner
    $('.wp-block-ajgnl-ajg-native-lands-search__search').hide();
    $('.wp-block-ajgnl-ajg-native-lands-search__intro').after('<img class="ajgnl-spinner" src="' + ajgnl_ajax.spinner_url + '" width="40" height="40"/>');


    var data = {
      'action': 'native_land_search',
      'address': address,
    };
    jQuery.post(ajgnl_ajax.ajax_url, data, function(response) {
      // Once we have response, remove any remaining intro content
      $('.ajgnl-spinner').remove();
      $('.wp-block-ajgnl-ajg-native-lands-search__intro').hide();

      // If error, show the error message
      if(!response.success){
        $('.wp-block-ajgnl-ajg-native-lands-search__error').show();
        return;
      }

      var data = JSON.parse(response.data);

      // If no results
      if(!data.length){
        $('.wp-block-ajgnl-ajg-native-lands-search__no-results').show();
        return;
      }

      // If results
      $.each(data, function(index, territory){
        const name = territory.properties['Name'];
        const url = territory.properties['description'];
        $('.wp-block-ajgnl-ajg-native-lands-search__list').append('<li><a href="'+url+'" target="_blank" rel="noopener">'+name+'</a></li>');
      });
      $('.wp-block-ajgnl-ajg-native-lands-search__success').show();
    });
  });
});
