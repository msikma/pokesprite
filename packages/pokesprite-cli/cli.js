/** PokéSprite - pokesprite-cli <https://github.com/msikma/pokesprite>
  * © MIT license */

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
const regArguments = [
  ['--exclude-forms', 'Excludes alternate forms.', false],
  ['--exclude-icon-sets', 'Excludes all icon sets (other than the Pokémon icons).', false],
  ['--exclude-pkmn', 'Excludes all Pokémon icons.', false],
  ['--exclude-regular', 'Excludes regular (non-shiny) icons.', false],
  ['--exclude-shiny', 'Excludes shiny icons.', false],
  ['--exclude-special', 'Excludes special icons (egg, and "unknown Pokémon").', false],
  ['--icon-sets', 'List of icon sets to include in the image.', '*', null, 'list'],
  ['--no-minify', 'Skips the minification and optimization step.', false],
  ['--no-padding', 'Don\'t add a 1px padding around all images.', false],
  ['--pkmn-lang', 'Sets the language of Pokémon names to use for the output.', 'eng', ['eng', 'jpn', 'jpn_ro']],
  ['--right-face', 'Whether to include right-facing icons. 0: Include none. 1: only unique icons are included (default). 2: all Pokémon get right-facing icons, possibly generated from their regular icon.', '1', ['0', '1', '2']],
  ['--more-help', 'Show advanced configuration arguments.', false],
  ['task', 'Action to perform.', 'build', ['build', 'html', 'md']]
]
// Advanced arguments, visible only if you pass --more-help.
const advArguments = [
  ['--dir-data', 'Icon data directory.', './data/'],
  ['--dir-icons', 'Icons directory.', './icons/'],
  ['--dir-output', 'Output directory.', './output/'],
  ['--dir-pkmn', 'Pokémon icons directory, relative to the icons directory.', 'pokemon/'],
  ['--file-extensions', 'Permitted file extensions for images to be included.', 'png'],
  ['--file-output-html', 'HTML icon overview output filename.', 'overview.html'],
  ['--file-output-img', 'Final output image filename.', 'pkmn.png'],
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

// Add our regular arguments (and advanced arguments if needed). Force --help on advanced.
regArguments.map(addArgument)
advanced && advArguments.map(addArgument)
advanced && process.argv.push('-h')

const cliArgs = parser.parseArgs()
const defaults = advArguments.reduce((def, arg) => ({ ...def, [arg[0].replace(/-/g, '_').replace(/^_+/, '')]: arg[2] }), {})
const sets = cliArgs.list.split(',')
const args = { ...defaults, ...cliArgs, rootDir: path.resolve(__dirname, '..'), icon_sets: sets[0] === '*' ? '*' : sets }

// Fire up the main application.
require('babel-polyfill')

if (process.env.MSIKMA_USE_SRC === '1') {
  require('babel-register')({ 'presets': ['env'], 'plugins': ['transform-class-properties', 'transform-object-rest-spread'] })
  require('./src').run(args)
}
else {
  require('./dist').run(args)
}
