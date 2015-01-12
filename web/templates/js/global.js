var Cl = window.Cl || {};

(function ($) {
    'use strict';

    Cl.Map = new Class({

        map: null,
        places: null,

        initialize: function (places) {
            this.places = places;

            this._events();
        },

        _events: function () {
            var that = this;

            google.maps.event.addDomListener(window, 'load', function () {
                that._generateMap();
            });
        },

        _generateMap: function () {
            var mapOptions = {
                center: { lat: -34.397, lng: 150.644},
                zoom: 8
            };
            this.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            this._generateMarkers();
        },

        _generateMarkers: function () {
            var that = this;

            var LatLngList = [];
            /*var image = {
                url: this.markerIcon,
                size: new google.maps.Size(29, 46),
                origin: new google.maps.Point(0,0),
                anchor: new google.maps.Point(15, 46)
            };*/
            for (var i = 0, len = this.places.length; i < len; i++){
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(this.places[i][0], this.places[i][1]),
                    /*icon: image,**/
                    map: this.map
                    /*url: $link.attr('href')*/
                });

                LatLngList.push(new google.maps.LatLng(this.places[i][0], this.places[i][1]));
            }
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
                //  And increase the bounds to take this point
                bounds.extend(LatLngList[i]);
            }
            //  Fit these bounds to the map

            this.map.fitBounds(bounds);
            var listener = google.maps.event.addListener(this.map, "idle", function() {
                that.map.panBy(0, 0);
                google.maps.event.removeListener(listener);
            });
        }
    });

})(jQuery);