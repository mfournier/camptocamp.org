/**
 * @requires config.js
 * @requires OpenLayers/Control/Navigation.js
 * @requires OpenLayers/Control/KeyboardDefaults.js
 * @requires OpenLayers/Control/PanZoomBar.js
 * @requires OpenLayers/Control/ArgParser.js
 * @requires OpenLayers/Control/Attribution.js
 * @requires OpenLayers/Control/ScaleLine.js
 * @requires OpenLayers/Control/OverviewMap.js
 * @requires OpenLayers/Control/NavigationHistory.js
 */

Ext.namespace("c2corg");

c2corg.base = {

    initialExtent: [376681.67533691, 5282104.4018836, 1504280.7164429, 6107624.3072486],
    restrictedExtent: [-20037508.34, -20037508.34, 20037508.34, 20037508.34],

    events: new Ext.util.Observable(),

    ignOptions: {
        displayInLayerSwitcher: false,
        visibility: false,
        transitionEffect: "resize",
        url: "http://gpp3-wxs.ign.fr/" + c2corg.config.ignKey + "/wmts",
        matrixSet: "PM",
        style: "normal",
        numZoomLevels: 19,
        attribution: '&copy;IGN <a href="http://www.geoportail.fr/" target="_blank">' +
                     '<img src="http://api.ign.fr/geoportail/api/js/2.0.0beta/theme/geoportal/img/logo_gp.gif">' +
                     '</a> <a href="http://www.geoportail.gouv.fr/depot/api/cgu/licAPI_CGUF.pdf" ' +
                     'alt="TOS" title="TOS" target="_blank">Terms of Service</a>'
    },

    // layer sources
    sources: {
        "olsource": {
            ptype: "gxp_olsource"
        },
        "osm": {
            ptype: "cgxp_osmsource"
        },
        "google": {
            ptype: "cgxp_googlesource"
        }
    },

    init: function (lang) {
        // Ext global settings
        Ext.BLANK_IMAGE_URL = "/static/js/carto/cgxp/ext/Ext/resources/images/default/s.gif";
        Ext.QuickTips.init();
    
        // OpenLayers global settings
        OpenLayers.Number.thousandsSeparator = ' ';
        OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
        OpenLayers.DOTS_PER_INCH = 72;
        OpenLayers.ImgPath = "/static/js/carto/cgxp/core/src/theme/img/ol/";
    
        lang = lang || 'fr';
        OpenLayers.Lang.setCode(lang);
        // GeoExt global settings
        GeoExt.Lang.set(lang);
    },

    getControls: function (options) {
        options = Ext.applyIf(options || {}, {
            zoomWheelEnabled: true
        });
        return [
            new OpenLayers.Control.Navigation({
                zoomWheelEnabled: options.zoomWheelEnabled
            }),
            new OpenLayers.Control.KeyboardDefaults(),
            new OpenLayers.Control.PanZoomBar({panIcons: false}),
            new OpenLayers.Control.ArgParser(),
            new OpenLayers.Control.Attribution(),
            new OpenLayers.Control.ScaleLine({
                geodesic: true,
                bottomInUnits: false,
                bottomOutUnits: false
            }),
            new OpenLayers.Control.OverviewMap({
                size: new OpenLayers.Size(180, 120),
                mapOptions: {
                    theme: null
                },
                minRatio: 16,
                maxRatio: 32,
                layers: [new OpenLayers.Layer.OSM("OSM", [
                        'http://a.tile.openstreetmap.org/${z}/${x}/${y}.png',
                        'http://b.tile.openstreetmap.org/${z}/${x}/${y}.png',
                        'http://c.tile.openstreetmap.org/${z}/${x}/${y}.png'
                    ],{
                        buffer: 0,
                        transitionEffect: 'resize'
                    }
                )]
            })
        ];
    }
};

c2corg.base.basemaps = [{
    source: "osm",
    name: "mapnik",
    group: 'background',
    ref: 'osm'
},
{
    source: "google",
    name: "TERRAIN",
    group: 'background',
    ref: "google_terrain"
},
{
    source: "google",
    name: "HYBRID",
    group: 'background',
    ref: "google_hybrid"
},{
    source: "olsource",
    type: "OpenLayers.Layer.WMTS",
    group: 'background',
    args: [Ext.applyIf({
        name: OpenLayers.i18n("IGN - cartes"),
        layer: "GEOGRAPHICALGRIDSYSTEMS.MAPS",
        ref: 'ign_maps',
        group : 'background'
    }, c2corg.base.ignOptions)]
},{
    source: "olsource",
    type: "OpenLayers.Layer.WMTS",
    group: 'background',
    args: [Ext.applyIf({
        name: OpenLayers.i18n("IGN - Orthos"),
        layer: "ORTHOIMAGERY.ORTHOPHOTOS",
        numZoomLevels: 20,
        ref: 'ign_ortho',
        group : 'background'
    }, c2corg.base.ignOptions)]
}];

c2corg.base.getMap = function(options) {
    options = Ext.applyIf(options || {}, {
        controls: {}
    });
    return {
        xtype: 'cgxp_mappanel',
        extent: c2corg.base.initialExtent,
        maxExtent: c2corg.base.restrictedExtent,
        restrictedExtent: c2corg.base.restrictedExtent,
        stateId: "map",
        projection: new OpenLayers.Projection("EPSG:900913"),
        units: "m",
        controls: c2corg.base.getControls(options.controls),
        layers: c2corg.base.basemaps,
        items: []
    }
};
