/** PokéSprite - pokesprite-res <https://github.com/msikma/pokesprite>
  * © MIT license */

const path = require('path')

const pokedex = require('./data/pkmn.json')
const itemIcons = require('./data/item-icons.json')
const exportData = require('./data/item-export.json')

module.exports = {
  data: {
    pokedex,
    itemIcons,
    exportData
  },
  iconDir: path.resolve(`${__dirname}/icons/`)
}
