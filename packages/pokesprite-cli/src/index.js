/** PokéSprite - pokesprite-cli <https://github.com/msikma/pokesprite>
  * © MIT license */

import PokeSprite from 'pokesprite-core'

export const run = async (args) => {
  console.log('cli args', args);
  PokeSprite.generateSprite(args)
}
