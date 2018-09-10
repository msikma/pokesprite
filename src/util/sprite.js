/**
 * Pokésprite - Pokémon icon database and spritesheet generator <https://github.com/msikma/pokesprite>
 * Copyright © 2018, Michiel Sikma
 */

import Spritesmith from 'spritesmith'

/**
 * Generates an image sprite from a list of icons.
 */
const generateSprite = icons => new Promise((resolve, reject) => {
  Spritesmith.run({ src: icons, padding: 1 }, (err, result) => {
    if (err) return reject(err)
    return resolve(result)
  });
})

export default generateSprite