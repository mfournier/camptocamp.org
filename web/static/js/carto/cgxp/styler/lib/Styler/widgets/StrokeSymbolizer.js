/**
 * Copyright (c) 2008 The Open Planning Project
 */

/**
 * @include GeoExt/widgets/tips/SliderTip.js
 */

Ext.namespace("Styler");

Styler.StrokeSymbolizer = Ext.extend(Ext.FormPanel, {

    /**
     * Property: symbolizer
     * {Object} A symbolizer object that will be used to fill in form values.
     *     This object will be modified when values change.  Clone first if
     *     you do not want your symbolizer modified.
     */
    symbolizer: null,

    /**
     * Property: defaultSymbolizer
     * {Object} Default symbolizer properties to be used where none provided.
     */
    defaultSymbolizer: null,

    /**
     * Property: dashStyles
     * {Array(Array)} A list of [value, name] pairs for stroke dash styles.
     *     The first item in each list is the value and the second is the
     *     display name.  Default is [["solid", "solid"], ["dash", "dash"],
     *     ["dot", "dot"]].
     */
    dashStyles: [["solid", OpenLayers.i18n("Solid")],
                 ["4 4", OpenLayers.i18n("Dash")],
                 ["2 4", OpenLayers.i18n("Dot")]],

    border: false,

    initComponent: function() {

        if(!this.symbolizer) {
            this.symbolizer = {};
        }
        Ext.applyIf(this.symbolizer, this.defaultSymbolizer);

        this.items = [{
            xtype: "fieldset",
            title: OpenLayers.i18n("Border"),
            autoHeight: true,
            defaults: {
                width: 100 // TODO: move to css
            },
            items: [{
                xtype: "combo",
                name: "style",
                fieldLabel: OpenLayers.i18n("Style"),
                store: new Ext.data.SimpleStore({
                    data: [["solid", OpenLayers.i18n("Solid")],
                           ["4 4", OpenLayers.i18n("Dash")],
                           ["2 4", OpenLayers.i18n("Dot")]],
                    fields: ["value", "display"]
                }),
                displayField: "display",
                valueField: "value",
                value: this.symbolizer.strokeDashstyle ?
                    this.symbolizer.strokeDashstyle : "solid",
                mode: "local",
                allowBlank: true,
                triggerAction: "all",
                editable: false,
                listeners: {
                    select: function(combo, record) {
                        var v = (record.get("value") === "solid") ? undefined : record.get("value");
                        this.symbolizer.strokeDashstyle = v;
                        this.fireEvent("change", this.symbolizer);
                    },
                    scope: this
                }
            }, {
                xtype: "colorpickerfield",
                name: "color",
                fieldLabel: OpenLayers.i18n("Color"),
                value: this.symbolizer.strokeColor,
                listeners: {
                    valid: function(field) {
                        this.symbolizer.strokeColor = field.getValue();
                        this.fireEvent("change", this.symbolizer);
                    },
                    scope: this
                }
            }, {
                xtype: "textfield",
                name: "width",
                fieldLabel: OpenLayers.i18n("Width"),
                value: this.symbolizer.strokeWidth,
                listeners: {
                    change: function(field, value) {
                        this.symbolizer.strokeWidth = value;
                        this.symbolizer.stroke = (value > 0 ? true : false);
                        this.fireEvent("change", this.symbolizer);
                    },
                    scope: this
                }
            }, {
                xtype: "slider",
                name: "opacity",
                fieldLabel: OpenLayers.i18n("Opacity"),
                value: (this.symbolizer.strokeOpacity === null) ? 100 : this.symbolizer.strokeOpacity * 100,
                isFormField: true,
                listeners: {
                    changecomplete: function(slider, value) {
                        this.symbolizer.strokeOpacity = value / 100;
                        this.fireEvent("change", this.symbolizer);
                    },
                    scope: this
                },
                plugins: [
                    new GeoExt.SliderTip({
                        getText: function(thumb) {
                            return thumb.value + "%";
                        }
                    })
                ]
            }],
            listeners: {
                "collapse": function() {
                    this.symbolizer.stroke = false;
                    this.fireEvent("change", this.symbolizer);
                },
                "expand": function() {
                    this.symbolizer.stroke = true;
                    this.fireEvent("change", this.symbolizer);
                },
                scope: this
            }
        }];

        this.addEvents(
            /**
             * Event: change
             * Fires before any field blurs if the field value has changed.
             *
             * Listener arguments:
             * symbolizer - {Object} A symbolizer with stroke related properties
             *     updated.
             */
            "change"
        );

        Styler.StrokeSymbolizer.superclass.initComponent.call(this);

    }


});

Ext.reg('gx_strokesymbolizer', Styler.StrokeSymbolizer);
