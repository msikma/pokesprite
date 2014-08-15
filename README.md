PokéSprite – Image Sprite Generator
===================================

This simple script generates a *complete image sprite* of all Pokémon in the National Pokédex, along with the icons for every single item, and a *SCSS file* to make them usable. This sprite can then be used to efficiently display these icons on a website.

![alt tag](https://raw.github.com/msikma/pokesprite/resources/wiki/pokesprite-banner.png)

Raison d'être
-------------

###### Why put all these icons in one big image? Isn't it more efficient to keep them in separate files and then only use the ones you need?

When loading an HTML page, the main factor that determines how long it takes is the amount of connections that need to be opened. For each file you include (CSS files, Javascript files, image files—anything), a new connection has to be made to request that file. If the file itself is small, it won't take long to download, but the act of opening the connection and getting the green light to begin transmitting the file's data takes time as well.

For that reason, it's recommended to minimize the amount of files included on a webpage. In the case of images, we can do this by putting them together in one single image, and then having multiple elements refer to different parts of that same image. This way, instead of requesting tons and tons of different images, we only need to request one. Minimizing connections is overwhelmingly the largest factor in reducing load times for websites.

Aside from that, it's useful to have one central location for all these files.

Usage guide
-----------

Displaying the sprites is a matter of adding an empty `<span>` or `<div>` element with the appropriate `class` attribute set. The base class is *`pkspr`*; following the base class, you can add a number of classes that specify which icon is to be displayed.

Here are some examples:

    <span class="pkspr pkmn-pikachu"></span>
    <span class="pkspr pkmn-bulbasaur color-shiny"></span>
    <span class="pkspr pkmn-deoxys form-defense"></span>
    <span class="pkspr pkmn-clauncher color-shiny dir-right"></span>
    <span class="pkspr pkmn-charizard form-mega-y"></span>
    <span class="pkspr pkmn-unown form-d"></span>
    <span class="pkspr pkmn-pyroar gender-female"></span>

To clarify, the following classes can be used:

* *`pkmn-(<name of Pokémon>)`* – Pokémon name*
* *`color-(regular|shiny)`* – shiny or regular icon
* *`dir-(left|right)`* – direction the icon faces (some Pokémon, such as Roselia, have a different icon when facing right—by default, those that do not have a separate icon will be flipped using the CSS `transform` attribute)
* *`gender-(male|female)`* – gender of the icon (in case of gender differences, such as Meowstic)
* *`form-(<name of form>)`* – form of the icon (e.g. `defense` for Deoxys, `a` or `exclamation` for Unown, `orange` for Flabébé, etc.)

<sub>* Note: a simplified name without special characters is used. See the icon overview for a full list of supported names.</sub>

The tag name used is also important: if a `<span>` is used, the icon is displayed as an `inline-block`. If a `<div>` is used, it's a `block`.

### Item icons

The item icons have been organized in a set of collections. To display an icon, first the collection name must be used, followed by the item itself. Some names are modified; for example, `oran-berry` is already in the `berry` collection, so it was renamed to just `oran`. Some HTML examples follow:

    <span class="pkspr berry-oran"></span>
    <span class="pkspr body-style-bipedal-tailed"></span>
    <span class="pkspr fossil-helix"></span>
    <span class="pkspr gem-bug"></span>
    <span class="pkspr medicine-potion"></span>
    <span class="pkspr mega-stone-charizardite-y"></span>
    <span class="pkspr pokeball-dive"></span>
    <span class="pkspr tm-ice"></span>

There are many different icons that can be displayed. See the [icon overview page](#) for a complete overview.

Compiling SCSS to CSS
---------------------

PokéSprite does not generate CSS—it only generates SCSS (which can't directly be used in a website). You'll have to compile the CSS yourself using [SASS](https://github.com/sass/sass). See the SASS manual for a more complete usage guide.

Once you have SASS installed, the CSS file can be compiled using the following terminal command (assuming that you're in the project's root directory):

    sass --style compressed output/pokesprite.scss output/pokesprite.css

The generated SCSS is currently not SassC compatible. This is planned for a later release.

Localization
------------

Currently, the script has English and Japanese (and romanized* Japanese) Pokémon names in its data file. Due to the way that the data file is set up right now, both form names and item names are all in English right now. The game is also translated in French, German, Italian, Spanish and Korean—contributions are welcome.

<sub>* Note: these are the official romanizations, rather than direct transliterations.</sub>

The script itself is in English. However, it's got a simple internationalization function, so it's possible to translate it to other languages.

Credits
-------

The Pokémon box icons were ripped by *Zhorken* from [Project Pokémon](http://projectpokemon.org/). It's unknown who ripped or tagged the other icons—if you did, please let me know so you can be credited.

The icons were further organized and cleaned up by *Dada78641*, who also wrote the initial version of this script and made all the initial shiny versions of the Pokémon icons. For a list of contributors, see the commit log.

Notes
-----

We've attempted to make the icon package complete and consistent. In the few cases where icons fit multiple categories, we've made duplicates. (Such as *King's Rock*, which can be found under *evo-item* and *hold-item*.)

Icons of unknown origin:

* `icons/key-item/lost-item-mimejr.png`

Unofficial icons (see FAQ):

* `icons/hm/fighting.png`

Frequently Asked Questions
--------------------------

###### Q: There's not supposed to be a Fighting type HM in Generation VI. Why is there an icon of it?

A: Previous generations did have Rock Smash as an HM rather than a TM. But rather than use an old icon, we've custom made an icon in Generation VI style for consistency purposes, as all other icons are in that same style.

###### Q: Where did the shiny Pokémon box icons come from?

A: They were custom made. No Pokémon game has ever had official ones.

###### Q: Were the icons modified in any way?

A: All icons were, if necessary, padded to the Generation VI icon size of 30x30 (up from 24x24) for consistency purposes.

###### Q: Are the shiny Pokémon icons just palette swaps?

A: Most are, but some aren't. The goal was to make icons that have the same visual quality as the originals, except with shiny colors. Due to the way pixel art works, that meant pure recolors were sometimes impossible. For example, any Pokémon that has its colors changed from something dark to something bright usually needed extra shades of gray.

Occasionally, the design itself also necessitated changes to specific areas rather than simply all pixels with those colors. In short, a good deal of them, particularly in later generations, were more work than just palette swapping.

In any case, we've attempted to maintain the basic visual style.

###### Q: Why does this system use JS to make the images show up instead of CSS?

CSS wasn't efficient enough because of the huge amount of rules. IE9 in particular can't process more than 1024 rules per CSS file, and even in decent browsers it wasn't very efficient either. So now the CSS contains the most basic attributes and the sizes for each icon set. The coordinates of each icon are set through JS. The disadvantage is that this makes the system unusable for people who browse without JS enabled. However, these days that is a [very tiny fraction](https://gds.blog.gov.uk/2013/10/21/how-many-people-are-missing-out-on-javascript-enhancement/). (And, although it'd be nice to support that fraction too, it just doesn't seem like there's a feasible way to do so.)

License
-------

The source icons are (C) Nintendo/Creatures Inc./GAME FREAK Inc.

Everything else, and usage of the programming code, is governed by the [MIT license](http://opensource.org/licenses/MIT).
