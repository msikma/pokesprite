/** PokéSprite - pokesprite-cli <https://github.com/msikma/pokesprite>
  * © MIT license */

const path = require('path')
const { ArgumentParser } = require('argparse')
const addLongHelp = require('argparse-longhelp')

const pkgData = require('./package.json')

const parser = new ArgumentParser({
  version: pkgData.version,
  addHelp: true,
  description: `${pkgData.description}.`,
  epilog: `The source icons are © Nintendo/Creatures Inc./GAME FREAK Inc. -  -  -
Everything else, and the programming code, is governed by the MIT license.`
})
addLongHelp(parser, `asdf`, true)

// Regular arguments.
let regArguments = [
  ['--exclude-pkmn', 'Exclude all Pokémon icons.', false],
  ['--exclude-regular', 'Exclude regular (non-shiny) icons.', false],
  ['--exclude-shiny', 'Exclude shiny icons.', false],
  ['--exclude-forms', 'Exclude alternate forms.', false],
  ['--exclude-gender', 'Exclude gender differences (pass "male" or "female").', 'neither', ['male', 'female', 'neither']],
  ['--exclude-items', 'Exclude all item sets.', false],
  ['--exclude-special', 'Exclude special icons (egg, and "unknown Pokémon").', false],
  ['--right-face', 'Whether to include right-facing icons. 0: Include none. 1: only unique icons are included (default). 2: all Pokémon get right-facing icons.', '1', ['0', '1', '2']],
  ['--item-sets', 'List of item sets to include in the image.', '*', null, 'list'],
  ['--no-minify', 'Skip the minification and optimization step.', false],
  ['--no-padding', 'Don\'t add a 1px padding around all images.', false],
  ['--more-help', 'Show additional file output arguments.', false],
  ['output', 'Directory to save the output to.']
]
// Advanced arguments, visible only if you pass --more-help.
const advArguments = [
  ['--generate-html', 'Whether to generate HTML icon overview.', true],
  ['--generate-css', 'Whether to generate CSS.', true],
  ['--generate-img', 'Whether to generate sprite image.', true],
  ['--generate-md', 'Whether to generate Markdown icon overview', true],
  ['--generate-scss', 'Whether to generate SCSS.', true],
  ['--file-output-html', 'HTML icon overview output filename.', 'overview.html'],
  ['--file-output-css', 'CSS output filename.', 'pkmn.css'],
  ['--file-output-img', 'Sprite image output filename.', 'pkmn.png'],
  ['--file-output-md', 'Markdown icon overview output filename.', 'overview.md'],
  ['--file-output-scss', 'SCSS output filename.', 'pkmn.scss']
]

// Whether to display advanced help.
const advanced = process.argv.filter(arg => arg === '--more-help').length > 0;

// Adds an argument to the ArgumentParser object.
const addArgument = arg =>
  parser.addArgument(arg[0], {
    help: arg[1],
    defaultValue: arg[2],
    ...(arg[2] === true || arg[2] === false ? { action: 'storeTrue' } : {}),
    ...(arg[3] ? { choices: arg[3] } : {}),
    ...(arg[4] ? { dest: arg[4] } : {})
  })

// Add our regular arguments (and advanced arguments if needed).
// Display more help if --more-help was passed (and remove --more-help from regular arguments).
advanced && (regArguments = regArguments.filter(arg => arg[0] !== '--more-help'))
regArguments.map(addArgument)
advanced && advArguments.map(addArgument)
advanced && process.argv.push('-h')

const cliArgs = parser.parseArgs()
const defaults = advArguments.reduce((def, arg) => ({ ...def, [arg[0].replace(/-/g, '_').replace(/^_+/, '')]: arg[2] }), {})
const sets = cliArgs.list.split(',')
const args = { ...defaults, ...cliArgs, icon_sets: sets[0] === '*' ? '*' : sets, output: `${path.resolve(cliArgs.output)}/` }

// Fire up the main application.
require('babel-polyfill')

if (process.env.MSIKMA_USE_SRC === '1') {
  require('babel-register')({ 'presets': ['env'], 'plugins': ['transform-class-properties', 'transform-object-rest-spread'] })
  require('./src').run(args)
}
else {
  require('./dist').run(args)
}
