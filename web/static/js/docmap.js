/**
 * Builds document-embedded map using c2corg maps API.
 */

Ext.namespace("c2corg");

c2corg.embeddedMap = (function() {

    // mapLang, objectsToShow, layersList and initCenter are global variables retrieved from template
    
    var features = [];
    var map_already_loaded = false;
    if (objectsToShow)
    {
        var wkt_parser = new OpenLayers.Format.WKT();
        for (var i = 0, len = objectsToShow.length; i < len; i++) {
            var obj = objectsToShow[i];
            if (!obj.wkt) continue;

            // TODO: use simplified WKT?
            var f = wkt_parser.read(obj.wkt);

            f.fid = obj.id;
            f.attributes = {type: obj.type};
            if (obj.type == "routes" || obj.type == "outings" || obj.type == "areas" || obj.type == "maps") {
                f.style = {
                    strokeColor: "yellow",
                    strokeWidth: 2,
                    fillColor: "yellow",
                    fillOpacity: 0.1
                };
            } else {
                f.style = {
                    pointRadius: 10,
                    externalGraphic: c2corg.config.staticBaseUrl + '/static/images/modules/' + obj.type + '_mini.png'
                    // FIXME: ${type} syntax seems not to work
                };
            }
            features.push(f);
        }
    }

    var api = new c2corg.API({lang: mapLang});
    var init_lon, init_lat, init_zoom;
    
    // Creating map fails with IE if no coords is submitted,
    // so we use first object center coords 
    // even if map is then recentered using features extent.
    if (initCenter.length > 0)
    {
        init_lon = initCenter[0];
        init_lat = initCenter[1];
        init_zoom = initCenter[2];
    }
    else if (features.length > 0)
    {
        mapCenter = features[0].geometry.getBounds().getCenterLonLat();
        mapCenter.transform(api.epsg900913, api.epsg4326);
        init_lon = mapCenter.lon;
        init_lat = mapCenter.lat;
        init_zoom = 12;
    } else {
        init_lon = 7;
        init_lat = 45.5;
        init_zoom = 6;
    }
    api.createMap({
        easting: init_lon,
        northing: init_lat,
        zoom: init_zoom
    });
    
    if (features.length > 0)
    {
        var drawingLayer = api.getDrawingLayer();
        drawingLayer.addFeatures(features);
        
        var extent;
        if (features.length > 1 || !(features[0].geometry instanceof OpenLayers.Geometry.Point)) {
            //api.map.zoomToExtent(drawingLayer.getDataExtent());
            extent = drawingLayer.getDataExtent();
            // FIXME see init() function below
        }
    }

    var initialCenter = api.map.getCenter();
    var initialZoom = api.map.getZoom();
    
    var bbar = api.createBbar({id: 'emb_layer_select'});
    
    var addPermalinkButton = function() {
        var permalinkDiv = document.createElement("div");
        permalinkDiv.id = 'permalink' + api.apiId;
        Ext.getBody().appendChild(permalinkDiv);
        api.addPermalinkControl();
        bbar.add(new Ext.Action({
            text: OpenLayers.i18n('Expand map'),
            handler: function() {
                window.open(Ext.get('permalink' + api.apiId).dom.value);
            }
        }));
    };
    
    var addResetButton = function() {
        bbar.add(new Ext.Action({
            text: OpenLayers.i18n('Recenter'),
            handler: function() {
                var center = initialCenter.clone();
                api.map.setCenter(center.transform(api.epsg900913, api.map.getProjectionObject()));
            }
        }));
    };
    
    var mappanel = Ext.apply(api.createMapPanel(), {
        id: 'mappanel',
        margins: '0 0 0 0',
        region: 'center',
        border: false,
        bbar: bbar
    });
    
    var treeOptions = (layersList.length > 0) ? {layers: layersList} : {};
    treeOptions.id = 'c2c_layers';
    var layertree = Ext.apply(api.createLayerTree(treeOptions), {
        region: 'west',
        width: 250,
        border: false,
        collapsible: true,
        collapseMode: 'mini',
        split: true
    });
    
    return {
        init: function() {
            // do not init if already loaded or if section is closed
            if (!$('map_container_section_container').visible()
                || map_already_loaded == true) {
              return;
            }

            // hide loading message
            Ext.removeNode(Ext.getDom('mapLoading'));
            
            new Ext.Panel({
                applyTo: 'map',
                layout: 'border',
                border: false,
                cls: 'embeddedMap',
                items: [ mappanel, layertree ]
            });
            
            // FIXME: done here else the zoom level is incorrect (changed when creating panel?)
            if (extent) {
                api.map.zoomToExtent(extent);
                initialZoom = api.map.getZoom();
            }
            
            addResetButton();
            addPermalinkButton();
            map_already_loaded = true;
        }
    };
})();

if (typeof(c2corgloadMapAsync) != 'undefined' && c2corgloadMapAsync) {
  c2corg.embeddedMap.init();
} else {
  Ext.onReady(c2corg.embeddedMap.init);
}
