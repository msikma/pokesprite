[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)

## PokéSprite

This is a collection of the box sprites of every Pokémon from the main game series, and the icons for every bag item. Also included are custom shiny versions of the box sprites that are not available in-game.

Included as part of the project is a script that generates an efficient image sprite for displaying them on a website.

This project is structured as a monorepo, with the icons (and the data needed to use them) present in the `pokesprite-res` package.

<p align="center">
  <img src="https://raw.github.com/msikma/pokesprite/master/resources/wiki/pokesprite-banner.png" alt="PokéSprite icon example" />
</p>

### Using the spritesheet

The easiest way to use and display these sprites is to include the CSS file from the `pokesprite-res` package and then add `<span>` or `<div>` tags with the appropriate class name. The following classes are available:

| Class                      | Description                |
|:---------------------------|:---------------------------|
| pkmn-**[name]**, pkmn-l-**[name]** | Displays a Pokémon by name; use pkmn-l for larger LPLE sprites |
| pkmn-**[dex nr]**, pkmn-l-**[dex nr]** | Displays a Pokémon by dex number (e.g. "001"); as above |
| color-regular, color-shiny | Toggles the regular color or shiny variant |
| dir-left, dir-right        | Direction the sprite faces (some are asymmetrical, e.g. Zangoose) |
| gender-male, gender-female | Toggles the gender (in case of gender differences, e.g. Meowstic) |
| form-**[name]**            | Displays a specific form variant (e.g. **defense** for Deoxys, etc.) |

Only lowercase ASCII characters are used for Pokémon names; e.g. Flabébé becomes **pkmn-flabebe**. Only English names are supported. Additionally, the larger sprites from *Let's Go Pikachu/Eevee* are available by using the **pkmn-l** prefix, e.g. **pkmn-l-meltan**.

The item sprites are displayed by referring to them by their directory name and file name.

**Here are some examples:**

| HTML                       | Result                     |
|:---------------------------|:--------------------------:|
| `<span class="pkspr pkmn-pikachu"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/regular/pikachu.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-bulbasaur color-shiny"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/shiny/bulbasaur.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-deoxys form-defense"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/regular/deoxys-defense.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-flabebe form-blue"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/regular/flabebe-blue.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-clauncher color-shiny dir-right"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/shiny/right/clauncher.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-charizard color-shiny form-mega-y"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/shiny/charizard-mega-y.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-unown form-d"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/regular/unown-d.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr pkmn-pyroar gender-female"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/pokemon/regular/female/pyroar.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr berry-oran"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/berry/oran.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr fossil-helix"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/fossil/helix.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr gem-bug"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/gem/bug.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr medicine-potion"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/medicine/potion.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr mega-stone-charizardite-y"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/mega-stone/charizardite-y.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr ball-dive"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/ball/dive.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr tm-ice"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/tm/ice.png" alt="PokéSprite icon example" /> |
| `<span class="pkspr body-style-bipedal-tailed"></span>` | <img src="https://raw.githubusercontent.com/msikma/pokesprite/master/icons/body-style/bipedal-tailed.png" alt="PokéSprite icon example" /> |

When using a `<span>` tag, the icons are displayed as `inline-block`. Use `<div>` to display them as `block`.

All Pokémon sprites are 40x30 and all item icons are 32x32. They're encoded as optimized 24-bit PNGs.

A complete list of icons is available on the [icon overview page](http://msikma.github.io/pokesprite/).

### Icon data

Developers who want to use these sprites programmatically might want to look at the **pkmn.json** and **item-export.json** files; the former contains a list of all Pokémon and their associated icons, and the latter links all icons in the repo to their internal IDs used in-game.

### License

The source icons are © Nintendo/Creatures Inc./GAME FREAK Inc.

Everything else, and usage of the programming code, is governed by the [MIT license](http://opensource.org/licenses/MIT).
