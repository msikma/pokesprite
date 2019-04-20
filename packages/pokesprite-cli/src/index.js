/** PokéSprite - pokesprite-cli <https://github.com/msikma/pokesprite>
  * © MIT license */

//import PokeSprite from 'pokesprite-core'
import PokeSprite from 'pokesprite-core/src/index'
import cliToArgs from './args'

export const run = async ({ output, ...settings }) => {
  PokeSprite.generateSprite(output, cliToArgs(settings))
}
