function dataFactory() {
  var max_ct = 1000,
      min_ct = 700,
      max    = 500,
      min    = 100;

  var kimbo = {
    tot_contatti: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    tot_chiamate: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    richiamare:   Math.floor(Math.random() * (max - min)) + min,
    confermati:   Math.floor(Math.random() * (max - min)) + min,
    rifiuti:      Math.floor(Math.random() * (max - min)) + min
  };

  kimbo['ctr_contatti'] = Math.round((kimbo.confermati / kimbo.tot_contatti) * 100 * 100) / 100;
  kimbo['ctr_chiamate'] = Math.round((kimbo.confermati / kimbo.tot_chiamate) * 100 * 100) / 100;

  var wind = {
    tot_contatti: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    tot_chiamate: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    richiamare:   Math.floor(Math.random() * (max - min)) + min,
    confermati:   Math.floor(Math.random() * (max - min)) + min,
    rifiuti:      Math.floor(Math.random() * (max - min)) + min
  };

  wind['ctr_contatti'] = Math.round((wind.confermati / wind.tot_contatti) * 100 * 100) / 100;
  wind['ctr_chiamate'] = Math.round((wind.confermati / wind.tot_chiamate) * 100 * 100) / 100;

  var vodafone = {
    tot_contatti: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    tot_chiamate: Math.floor(Math.random() * (max_ct - min_ct)) + min_ct,
    richiamare:   Math.floor(Math.random() * (max - min)) + min,
    confermati:   Math.floor(Math.random() * (max - min)) + min,
    rifiuti:      Math.floor(Math.random() * (max - min)) + min
  };

  vodafone['ctr_contatti'] = Math.round((vodafone.confermati / vodafone.tot_contatti) * 100 * 100) / 100;
  vodafone['ctr_chiamate'] = Math.round((vodafone.confermati / vodafone.tot_chiamate) * 100 * 100) / 100;

  return {
    kimbo: kimbo,
    wind: wind,
    vodafone: vodafone
  };
}

var data = dataFactory();

$('.dropdown-menu li').click(function(evt) {
  evt.preventDefault();

  var selectedCampaign = $(this).text().toLowerCase();
  $('.totale-contatti').text(data[selectedCampaign].tot_contatti);
  $('.da-richiamare').text(data[selectedCampaign].richiamare);
  $('.contatti-confermati').text(data[selectedCampaign].confermati);
  $('.non-interessati').text(data[selectedCampaign].rifiuti);
  $('.totale-chiamate').text(data[selectedCampaign].tot_chiamate);
  $('.ctr_contatti').text(data[selectedCampaign].ctr_contatti + "%");
  $('.ctr_chiamate').text(data[selectedCampaign].ctr_chiamate + "%");
});
