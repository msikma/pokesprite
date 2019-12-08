## Gen 7 modifications

The icons in `icons-gen7x` are from Gen 7 and have been modified for consistency with Gen 8. It features the old Mega Evolution icons (rather than the Gen 8 ones that are the Pokémon's regular icon with an added Mega Evolution badge), and the following changes:

### Size changes

In Gen 7, icons are 40×30. In Gen 8, they're 68×56 with the old sprites' center point moved up 1 px from the bottom. So to properly change their canvas size, they need to be anchored to the bottom and then moved up 1 px. See [the resize script](scripts/to_gen8_size.fish).

### Contrast changes

In Gen 8, the Pokémon sprites (*not* the item icons) have been modified to have slightly greater contrast. The edges are darker (originally `#202020`, now `#000000`) and other grayscales have been darkened as well, while non-gray colors have been left alone.

The following modifications were applied:

* `#202020` → `#000000`
* `#696969` → `#595959`
* `#424242` → `#343434`
* `#3c3c3c` → `#303030`
* `#464646` → `#363636`
* `#525252` → `#414141`
* `#62625a` → `#424242`
* `#333333` → `#404040`

Note: when converting, make sure to prevent ImageMagick from adding a `gAMA` block, as it does by default. This causes the images to be displayed slightly differently despite having identical color values.

### LPLE minor updates

The following minor updates were part of LPLE:

* Zubat, Golbat icons: inside of its mouth changed from `#202020` (outline color) to `#414141`
* Poliwhirl icon: highlight on its left eye changed from `#414141` to `#838373` (same color as on its belly)
* Venonat icon: fixed a transparent pixel error
* Wartortle icon: properly closed the outline on its head

These updates have been backported to the 40×30 icons.

## Icon sets

The icons in this repository have been split up into three sets of Pokémon box sprites and item icons:

<table>
<tr><th>Set name</th><th colspan="2">Size (box, items)</th><th>Description</th></tr>
<tr><td>icons</td><td>32×32</td><td>40×30</td><td>Original icons from Gen 7 (core series; up to Pokémon Ultra Sun/Ultra Moon). These are mostly legacy sprites that won't be updated (much) anymore.</td></tr>
<tr><td>icons‑gen7x</td><td>32×32</td><td>68×56</td><td>Original icons from Gen 7, modified to be more consistent with Gen 8 sprites by changing the size and contrast (see below).</td></tr>
<tr><td>icons‑gen8</td><td>32×32</td><td>68×56</td><td>Original icons from Gen 8.</td></tr>
</table>

## Icon notes

Most icons have been taken from the game verbatim (except of course the shiny variants, which are all original), but some icons have been edited slightly. A couple of unofficial icons are included for convenience.

<table>
<tr><th colspan="3">Icon file</th><th>U<sup>†</sup></th><th>Description</th></tr>
<tr><td>icons</td><td>/pokemon/mega.png</td><td><center><img src="icons/pokemon/mega.png?raw=true" /></center></td><td><center>✓</center></td><td>Based on the Mega Evolution badge from Gen 8 icons.</td></tr>
<tr><td>icons</td><td>/pokemon/regular/meltan.png</td><td><center><img src="icons/pokemon/regular/meltan.png?raw=true" /></center></td><td><center>✓</center></td><td>The Meltan icon from Gen 8, cropped down to 40×30.</td></tr>
<tr><td>icons</td><td>/pokemon/regular/melmetal.png</td><td><center><img src="icons/pokemon/regular/melmetal.png?raw=true" /></center></td><td><center>✓</center></td><td>The Melmetal icon from Gen 8, edited to fit in 40×30.</td></tr>
<tr><td>icons</td><td>/pokemon/unknown.png</td><td><center><img src="icons/pokemon/unknown.png?raw=true" /></center></td><td><center></center></td><td>Displayed while busy loading a box sprite or for glitch Pokémon.</td></tr>
</table>

†: Unofficial icon
