// pokesprite-images <https://github.com/msikma/pokesprite>
// © MIT license

// This file mostly exists so that the module's path can be determined
// by using require.resolve(), which fails for modules without entry point.

const path = require('path')

module.exports = {
  baseDir: path.resolve(`${__dirname}`),
  inventoryDirs: ['items', 'items-outline'],
  pokemonDirs: ['pokemon-gen7x', 'pokemon-gen8'],
  miscDirs: ['misc']
}
