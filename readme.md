[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)

# PokéSprite

This is a collection of the box sprites of every Pokémon from the main game series, and the icons for every collectable and bag item. Also included are custom shiny versions of the box sprites that are not available in-game.

Some examples of the custom shiny sprites:

<p align="center">
  <img src="https://raw.github.com/msikma/pokesprite/master/resources/wiki/pokesprite-banner.png" alt="PokéSprite icon example" />
</p>

These icons can be used as individual files, or combined into a single image and CSS file for efficiently displaying them on a website.

## Icons and metadata

The icons included in this project can be found in the `/icons/` directory. They are separated by type (e.g. **"berry"**, **"evo-item"**, **"valuable-item"**, etc.), and the Pokémon sprites can be found in `/icons/pokemon/` and `/icons/pokemon-gen8/`.

The Pokémon sprite files use a simplified version of their English name, e.g. `mr-mime.png` for *Mr. Mime*. To programmatically process the files (e.g. to easily link some piece of data, like the dex number, to a sprite file), the data in `/data/dex.json` can be used.

Up to and including *Pokémon Sun/Moon* (Generation 7), all Pokémon sprites had a size of **40×30**—starting with *Pokémon Let's Go* (Generation 8), they are **68×56**. Items are all **32×32**.

Previous generations of games (Generation 1–2 and Generation 3–4) had their own collections of sprites, but these are not included in this database.

## Data files

Developers who want to use these sprites programmatically might want to look at the `/data/dex.json` and `/data/item-export.json` files; the former contains a list of all Pokémon and their associated icons, and the latter links all icons in the repo to their internal IDs used in-game.

Each item in the `dex.json` file contains the following data (example):

```js
{
  idx: 658,
  slug: {
    eng: "greninja",
    jpn: "gekkouga"
  },
  icons: {
    _: {
      "has_right": true
    },
    ash: {
      "has_right": true
    },
    "battle-bond": {
      "is_alias_of": "ash"
    }
  },
  name: {
    eng: "Greninja",
    jpn: "ゲッコウガ",
    jpn_ro: "Gekkouga"
  }
}
```

The `icons` item contains a list of all icon types that are related to a Pokémon. It always contains at least a value `_` (underscore), which means the regular form or default icon. Each form object can contain the following details:

| Key | Meaning |
|:----|:--------|
| `is_alias_of` | This form uses the icon of another form |
| `is_unofficial_icon` | The original (non-shiny) icon has been edited in some way (e.g. *Pumpkaboo* and *Gourgeist*) |
| `is_unofficial_legacy_icon` | As above, but only for the smaller legacy 40×30 icons |
| `has_right` | A unique right-facing icon is available (e.g. *Roselia*) |
| `has_female` | A unique female icon is available (e.g. *Unfezant*) |

The `name` field also contains `jpn_ro`, which refers to the official romanization of a Pokémon's name.

## License

The source icons are © Nintendo/Creatures Inc./GAME FREAK Inc.

Everything else, and usage of the programming code, is governed by the [MIT license](http://opensource.org/licenses/MIT).
