/**
 * Pokésprite - Pokémon icon database and spritesheet generator <https://github.com/msikma/pokesprite>
 * Copyright © 2018, Michiel Sikma
 */

import iconList from './util/iconList'
import generateSprite from './util/sprite'

/**
 * This is run right after parsing the user's command line arguments.
 * We check what type of URL the user passed and call the appropriate script.
 * This scrapes the page, prints info, and downloads the files.
 *
 * All command line arguments are passed here.
 */
export const run = async (args) => {
  const { rootDir, dir_icons, dir_pkmn, dir_data, list } = args
  const icons = iconList(rootDir, dir_icons, dir_pkmn, dir_data, list)
  const sprite = await generateSprite(icons)
  console.log(sprite);
  return process.exit(0)
}
