/**
 * Pokésprite - Pokémon icon database and spritesheet generator <https://github.com/msikma/pokesprite>
 * Copyright © 2018, Michiel Sikma
 */

/**
 * This is run right after parsing the user's command line arguments.
 * We check what type of URL the user passed and call the appropriate script.
 * This scrapes the page, prints info, and downloads the files.
 *
 * All command line arguments are passed here.
 */
export const run = async (args) => {
  console.log(args);
  return process.exit(0)
}
