## Ribbon notes

Ribbon descriptions have been taken from [Bulbapedia's *List of Ribbons in the games* page](https://bulbapedia.bulbagarden.net/wiki/List_of_Ribbons_in_the_games). They were requested in [issue #8](https://github.com/msikma/pokesprite/issues/8) and merged with [PR #59](https://github.com/msikma/pokesprite/pull/59).

Each item in the [`misc.json`](https://github.com/msikma/pokesprite/blob/master/data/misc.json) data file has the following structure:

```js
{
  "name": {
    "eng": "Training Ribbon",
    "jpn": "しゅぎょうリボン",
    "jpn_ro": "Training Ribbon"
  },
  "origin_gen": 6,
  "description": {
    "eng": "A Ribbon that can be given to a Pokémon that has overcome rigorous trials and training.",
    "from_gen": 7
  },
  "files": {
    "gen-6": "ribbon/training-ribbon.png",
    "gen-8": "ribbon/gen8/training-ribbon.png"
  }
},
```

A brief description of what each item is:

| Key | Meaning |
|:----|:--------|
| `name` | Official names in various languages |
| `origin_gen` | The generation in which this ribbon was originally introduced |
| `description` | A description of the ribbon from a specific generation |
| `files` | Object containing paths to the sprites from various generations |

Note that `files` may have multiple entries, such as the *Training Ribbon* which has an image for Gen 6 and Gen 8.

### Size and gamma changes

In Gen 3, ribbon sprites were originally 32×32. From Gen 4 they became 40×40 and stayed that way until Gen 8 introduced new high res ribbon images. All old ribbons have been padded to 40×40 to make them easier to use and build interfaces with. If the original icons are required, all `gen-3` files can be losslessly cut back down to 32×32.

All ribbon sprites for Gen 4 and below had a very slightly different gamma curve, making them slightly brighter than the sprites for newer games. The following adjustment was made:

```fish
for n in *.*
  set x (magick identify -verbose "$n")
  set z1 (echo $x | grep -i "484850")
  set z2 (echo $x | grep -i "4a4a52")
  
  # Check if colors associated with the other gamma curve are present
  if begin test -n "$z1"; or test -n "$z2"; end
    # If so, adjust the gamma curve
    magick convert -level 8%,100%,1.0 -define png:exclude-chunk=gAMA "$n" -depth 32 PNG32:"$n"
  end
end

# single file
# convert -level 8%,100%,1.0 "test.png"

# set images to 72dpi
# for n in *.*
#   convert -density 72x72 -units PixelsPerInch -define png:exclude-chunk=gAMA "$n" -depth 32 PNG32:"$n"
# end
```

### Other notes

Some ribbons have the same name and are only distinguished by filename, e.g. `battle-memory-ribbon-gold.png` and `battle-memory-ribbon.png` are both called the *Battle Memory Ribbon*. This might be changed someday.