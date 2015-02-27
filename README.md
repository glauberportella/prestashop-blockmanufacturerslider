#Module Block Manufacturer Slider

##Description

Displays a carousel of manufacturer brands. The brand/logo is added normally via Prestashop Manufacturer admin interface.

##Requirements

- Prestashop 1.6+
- This module needs a Bootstrap Prestashop Theme (default v1.6 theme shipped with Prestashop download is ready for it)

##Installation

- Clone repo in your Prestashop `modules` folder
- Access Prestashop Module Admin and install the module

##Usage

The module creates a new Prestashop Hook called `displayHomeManufacturerCarousel`

To activate the block create a custom theme based on a Prestashop Bootstrap Theme (i.e. the default 1.6+ theme) and add the hook call where you would to show the carousel.

For example, edit theme index.tpl `/themes/default-bootstrap/index.html` to include the hook on home top content:

    {hook h='displayHomeManufacturerCarousel'}
    
    {if isset($HOOK_HOME_TAB_CONTENT) && $HOOK_HOME_TAB_CONTENT|trim}
    {if isset($HOOK_HOME_TAB) && $HOOK_HOME_TAB|trim}
        <ul id="home-page-tabs" class="nav nav-tabs clearfix">
            {$HOOK_HOME_TAB}
        </ul>
    {/if}
    <div class="tab-content">{$HOOK_HOME_TAB_CONTENT}</div>
    {/if}
    {if isset($HOOK_HOME) && $HOOK_HOME|trim}
        <div class="clearfix">{$HOOK_HOME}</div>
    {/if}

##TODO

- Admin configuration for slide width (logo width)
- Configuration of min number of slides
- Configuration of max slides
- Configuration of slide margins
- Configuration for max number of manufacturer logo to show

PS.: Read slide as logo.
