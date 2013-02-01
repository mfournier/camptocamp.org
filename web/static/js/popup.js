var delay = 4000;

var start_frame = 0;

function fadeInOut(frame, start_frame, end_frame, delay, lis) {
    return (function() {
        popup = $('popup_contentDiv');
        lis = popup.down('.popup_slideimages').getElementsByTagName('li');
        Effect.Fade(lis[frame]);
        if (frame == end_frame) { frame = start_frame; } else { frame++; }
        lisAppear = lis[frame];
        setTimeout("Effect.Appear(lisAppear);", 0);
        setTimeout(fadeInOut(frame, start_frame, end_frame, delay), delay + 1850);
    });
}

function start_slideshow(start_frame, end_frame, delay, lis) {
    setTimeout(fadeInOut(start_frame, start_frame, end_frame, delay, lis), delay);
}

function handlePopupRoutes(up)
{
    var ctrl_div = popup.getElementsById('size_ctrl');
    
    if (ctrl_div)
    {
        var desc_div = popup.getElementsByClassName('popup_desc')[0];
        var routes_title = $('routes_title');
        var routes_div = $('routes_section_container');
        var close_div = $('close_popup_routes');
        var open_div = $('open_popup_routes');
        var close_status = close_div.visible();
        var open_status = open_div.visible();
        var old_level = 0;
        var level = 0;
        
        if (!close_status && open_status)
        {
            old_level = 0;
        }
        else if (close_status && open_status)
        {
            old_level = 1;
        }
        else if (close_status && !open_status)
        {
            old_level = 2;
        }
        
        if (up)
        {
            level = Math.min(2, old_level + 1);
        }
        else
        {
            level = Math.max(0, old_level - 1);
        }
        
        
        if (old_level === 1 && level === 0)
        {
            close_div.hide();
            desc_div.removeClassName('popup_iti');
            routes_title.hide();
            routes_div.hide();
        }
        else if (old_level === 0 && level === 1)
        {
            close_div.show();
            desc_div.addClassName('popup_iti');
            routes_title.show();
            routes_div.show();
        }
        else if (old_level === 1 && level === 2)
        {
            open_div.hide();
            desc_div.hide();
            routes_div.addClassName('full');
        }
        else if (old_level === 2 && level === 1)
        {
            open_div.show();
            desc_div.show();
            routes_div.removeClassName('full');
        }
    }
}

function openPopupRoutes()
{
    handlePopupRoutes(1);
}

function closePopupRoutes()
{
    handlePopupRoutes(0);
}

function init_slideshow()
{
    /* image slideshow */
    var popup = $('popup_contentDiv');
    var lis = popup.down('.popup_slideimages');
    if (lis)
    {
        lis = lis.getElementsByTagName('li');
        var end_frame = lis.length -1;
        start_slideshow(start_frame, end_frame, delay, lis);
    }
}

function init_popup() {
    
    var popup = $('popup_contentDiv');
    
    /* button for showing / hiding routes list */
    var close_routes = $('close_popup_routes');
    if (close_routes)
    {
        close_routes.observe('click', closePopupRoutes);
    }
    
    var open_routes = $('open_popup_routes');
    if (open_routes)
    {
        open_routes.observe('click', openPopupRoutes);
    }

    /* toggling images */
/*    var toggle_images = $('toggle_images');*/
    var toggle_images = popup.down('.popup_desc');
    if (toggle_images)
    {
        toggle_images.observe('click', togglePopupImages);
    }
    
    /* handle routes display TODO */
    var routes_section = $('routes_section_container');
    if (routes_section)
    {
        routes_section = routes_section.getElementsByClassName('title2');
        if (routes_section.length > 0)
        {
            routes_section.each(function(t)
            {
                t.observe('click', handleRoutes);
            });
        }
    }
}

function togglepopupImages()
{
    var popup = $('popup_contentDiv');
    var desc_div = popup.getElementsByClassName('popup_desc')[0];
    var desc_class = $w(desc_div.className);
    var full = desc_class.include('full');
    
    if (!full)
    {
        desc_div.addClassName('full');
        if (desc_class.include('popup_iti'))
        {
            handlePopupRoutes(0);
        }
    }
    else
    {
        desc_div.removeClassName('full');
    }
}


//Event.observe(window, 'load', init_popup, false);
