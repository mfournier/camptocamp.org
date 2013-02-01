function hide_parkings_unrelated_fields()
{
    var value = $('public_transportation_rating').options[$('public_transportation_rating').selectedIndex].value;
    if(value != '3' && value != '0')
    {
        $('tp_types').show();
        $('tp_desc').show();
    }
    else
    {
        $('tp_types').hide();
        $('tp_desc').hide();
    }
    
    value = $('snow_clearance_rating').options[$('snow_clearance_rating').selectedIndex].value;
    if(value != '4' && value != '0')
    {
        $('snow_desc').show();
    }
    else
    {
        $('snow_desc').hide();
    }
}

document.observe('dom:loaded', hide_parkings_unrelated_fields);
