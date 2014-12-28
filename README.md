PokéSprite – Image Sprite Generator
===================================

This simple script generates a *complete image sprite* of all Pokémon in the National Pokédex, along with the icons for every single in-game item. It also generates *SCSS and JS files* which can then be used to efficiently display the icons from the sprite on a website.

<p align="center">
  <img src="https://raw.github.com/msikma/pokesprite/master/resources/wiki/pokesprite-banner.png" alt="PokéSprite icon example" />
</p>

Raison d'être
-------------

###### Why put all these icons in one big image? Isn't it more efficient to keep them in separate files and then only use the ones you need?

When loading an HTML page, the main factor that determines how long it takes is the amount of connections that need to be opened. For each file you include (CSS files, Javascript files, image files—anything), a new connection has to be made to request that file. If the file itself is small, it won't take long to download, but the act of opening the connection and getting the green light to begin transmitting the file's data takes time as well.

For that reason, it's recommended to minimize the amount of files included on a webpage. In the case of images, we can do this by putting them together in one single image, and then having multiple elements refer to different parts of that same image. This way, instead of requesting tons and tons of different images, we only need to request one. Minimizing connections is overwhelmingly the largest factor in reducing load times for websites.

Aside from that, it's useful to have one central location for all these icons.

Usage guide
-----------

Displaying the sprites is a matter of adding an empty `<span>` or `<div>` element with the appropriate `class` attribute set. The base class is `pkspr`. Following the base class, you can add a number of classes that specify which icon is to be displayed.

Here are some examples:

```html
<span class="pkspr pkmn-pikachu"></span>
<span class="pkspr pkmn-bulbasaur color-shiny"></span>
<span class="pkspr pkmn-deoxys form-defense"></span>
<span class="pkspr pkmn-clauncher color-shiny dir-right"></span>
<span class="pkspr pkmn-charizard form-mega-y"></span>
<span class="pkspr pkmn-unown form-d"></span>
<span class="pkspr pkmn-pyroar gender-female"></span>
```

To clarify, the following classes can be used:

* <code>pkmn-<strong>name</strong></code> – Pokémon name* or Pokédex number
* <code>color-regular</code>, <code>color-shiny</code> – shiny or regular icon
* <code>dir-left</code>, <code>dir-right</code> – direction the icon faces (some Pokémon, such as Roselia, have a different icon when facing right—by default, those that do not have a separate icon will be flipped using the CSS `transform` attribute)
* <code>gender-male</code>, <code>gender-female</code> – gender of the icon (in case of gender differences, such as Meowstic)
* <code>form-<strong>name</strong></code> – form of the Pokémon (e.g. `defense` for Deoxys, `a` or `exclamation` for Unown, `orange` for Flabébé, etc.)

*\*Note: for Pokémon names, simplified versions without special characters are used, e.g. "flabebe" rather than "Flabébé". See the [icon overview page](https://github.com/msikma/pokesprite/wiki/Overview) for a full list of supported names.*

You can select which Pokémon to display using its index number too, e.g. `<span class="pkspr pkmn-004"></span>` for Charmander.

The tag name used is also important: if a `<span>` is used, the icon is displayed as an `inline-block`. If a `<div>` is used, it's a `block`.

### Item icons

The item icons have been organized in a set of collections. To display an icon, first the collection name must be used, followed by the item itself. For example, an Oran Berry is named `oran` and is in the `berry` collection, so the full class name would be `pkspr berry-oran`. Some more HTML examples follow:

```html
<span class="pkspr berry-oran"></span>
<span class="pkspr body-style-bipedal-tailed"></span>
<span class="pkspr fossil-helix"></span>
<span class="pkspr gem-bug"></span>
<span class="pkspr medicine-potion"></span>
<span class="pkspr mega-stone-charizardite-y"></span>
<span class="pkspr pokeball-dive"></span>
<span class="pkspr tm-ice"></span>
```

There are many different icons that can be displayed. See the [icon overview page](https://github.com/msikma/pokesprite/wiki/Overview) for a complete overview.

Compiling the sprite
--------------------

Running the script to generate a sprite image with default settings is a simple matter of running the program.

```
./pokesprite.php
```

This will generate a full sprite sheet with regular icons, shiny icons, right-facing icons (where a unique icon exists), and all other icon sets. It also generates SCSS and JS files and an overview HTML page for previewing your build. Everything is saved to the `output/` directory.

### Compiling SCSS to CSS

PokéSprite does not generate CSS—it only generates SCSS (which can't directly be used in a website). You'll have to compile the CSS yourself using [SASS](https://github.com/sass/sass). See the SASS manual for a more complete usage guide.

Once you have SASS installed, the CSS file can be compiled using the following terminal command:

```
sass --style compressed output/pokesprite.scss output/pokesprite.css
```

The generated SCSS is currently not SassC compatible. This is planned for a later release.

### Compiling the JS using the Closure Compiler

The JS file can be optimized with the Closure Compiler. The easiest way is to use the [Closure Compiler Service](http://closure-compiler.appspot.com/home). Make sure to set the optimization level to *advanced*.

In case you have a local binary, the following command can be used:

```
java -jar closure-compiler.jar --compilation_level ADVANCED_OPTIMIZATIONS \
  --js output/pokesprite.js --js_output_file output/pokesprite.min.js \
  --charset UTF-8
```

More information
----------------

See the [wiki](https://github.com/msikma/pokesprite/wiki) for more general information about the project and its development. There's also a [frequently asked questions](https://github.com/msikma/pokesprite/wiki/FAQ-and-other-notes) page and a [full icon overview](https://github.com/msikma/pokesprite/wiki/Overview).

Using in other projects
-----------------------

Feel free to use anything from this project in your own work. There's no need to give credit, especially if it's not convenient (e.g. in a blog or forum post). Of course, adding a link to the project is appreciated, but you don't need to.

If you use this project or some of the icons, I'd love to see how they look. You can email me on [mike@letsdeliver.com](mailto:mike@letsdeliver.com) or message me on [/u/dada_](http://www.reddit.com/user/dada_/).

Credits
-------

The Pokémon box icons were ripped by *Zhorken*. All other icons were ripped by *Kaphotics*, who also ripped the new ORAS sprites. Both can be found at [Project Pokémon](http://projectpokemon.org/).

The icons were then reorganized and cleaned up by *Dada78641*, who also wrote the script and created all the shiny versions of the Pokémon icons. For a full list of project contributors, see the commit log.

License
-------

The source icons are (C) Nintendo/Creatures Inc./GAME FREAK Inc.

Everything else, and usage of the programming code, is governed by the [MIT license](http://opensource.org/licenses/MIT).
