## Gen 7 modifications

The icons in `icons-gen7x` are from Gen 7 and have been modified for consistency with Gen 8. The following changes were applied:

### Size changes

In Gen 7, icons are 40×30. In Gen 8, they're 68×56 with the old sprites' center point moved up 1 px from the bottom. So to properly change their canvas size, they need to be anchored to the bottom and then moved up 1 px.

### Contrast changes

In Gen 8, the Pokémon sprites (*not* the item icons) have been modified to have slightly greater contrast. The edges are darker (originally `#202020`, now `#000000`) and other grayscales have been darkened as well, while non-gray colors have been left alone.

The following modifications were applied:

* `#202020` → `#000000`
* `#696969` → `#595959`
* `#424242` → `#353535`
* `#404040` → `#343434`
* `#606058` → `#4d4d46`
* `#3c3c3c` → `#303030`
* `#464646` → `#363636`
* `#525252` → `#414141`
* `#62625a` → `#434343`
* `#333333` → `#3f3f3f`

Most of these colors are only used by the shiny sprites.

Note: when converting, make sure to prevent ImageMagick from adding a `gAMA` block, as it does by default. This causes the images to be displayed slightly differently despite having identical color values.

Replacing a color can be done with the following ImageMagick command (e.g. `#202020` → `#000000` in this example):

```sh
magick convert -fill "#000000" -opaque "#202020" -define png:exclude-chunk=gAMA $i -depth 32 PNG32:$i
```

### LPLE minor updates

The following minor updates were part of LPLE:

* Zubat, Golbat icons: inside of its mouth changed from `#202020` (outline color) to `#414141`
* Poliwhirl icon: highlight on its left eye changed from `#414141` to `#838373` (same color as on its belly)
* Venonat icon: fixed a transparent pixel error
* Wartortle icon: properly closed the outline on its head

These updates have been backported to the 40×30 icons.

## Gen 8 modifications

The Gen 8 sprites seem to come in 2× upscaled format now, 136×112 instead of 68×56. For downscaling them:

```sh
magick convert -interpolate Nearest -filter point -resize 50% -define png:exclude-chunk=gAMA $i -depth 32 PNG32:$i
```