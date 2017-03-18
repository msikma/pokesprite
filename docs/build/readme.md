PokéSprite
==========

This is the Node module version of [PokéSprite](https://github.com/msikma/pokesprite).

See [the documentation](http://msikma.github.io/pokesprite/) for a full icon overview.

Usage
-----

TODO

```js
var PkSpr = require('pokesprite').PkSpr;
var icon = PkSpr.decorate({slug: "pikachu"}); // see docs for more attributes

console.log(icon);

/*
{ request: { slug: 'pikachu' },
  attributes: 
   { type: 'pkmn',
     slug: 'pikachu',
     color: null,
     form: null,
     gender: null,
     dir: null },
  exactMatch: true,
  found: true,
  data: { coords: { x: 1, y: 63 }, props: { flipped: false } },
  size: { w: 40, h: 30 } }
*/
```

With this information, you can construct a DOM node that displays the icon. `coords` is the x and y starting positions of the icon in the image, so you should set `background-position` to *minus* those values. If `flipped` is `true`, you should display the node horizontally mirrored (this is true if `dir` is set to `right` and the icon does not have a unique right-facing sprite).

If an icon could not be found, `found` will be `false`. If an icon was found, but it isn't precisely the one you requested, `exactMatch` will be `false`. For example, this happens if you request `gender: 'female'` for a Pokémon that doesn't have a separate icon.

### ES6

To import in ES6:

```js
import { PkSpr } from 'pokesprite';
```

### AMD

It should also work with AMD syntax, but I haven't tested this.

License
-------

The source icons are © Nintendo/Creatures Inc./GAME FREAK Inc.

Everything else, and usage of the programming code, is governed by the [MIT license](http://opensource.org/licenses/MIT).