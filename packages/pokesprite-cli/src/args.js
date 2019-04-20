/** PokéSprite - pokesprite-cli <https://github.com/msikma/pokesprite>
  * © MIT license */

/** Convers command-line arguments into a settings object for PokéSprite. */
const cliToArgs = args => {
  // Output paths for all targets.
  const outPaths = {
    css: args.file_output_css,
    html: args.file_output_html,
    img: args.file_output_img,
    md: args.file_output_md,
    scss: args.file_output_scss
  }
  // Which targets we'll generate.
  const out = {
    css: args.generate_css,
    html: args.generate_html,
    img: args.generate_img,
    md: args.generate_md,
    scss: args.generate_scss
  }
  return {
    icons: {
      pokemon: {
        include: !args.exclude_pkmn,
        forms: !args.exclude_forms,
        regular: !args.exclude_regular,
        shiny: !args.exclude_shiny,
        genders: args.exclude_gender === 'neither' ? 'both' : args.exclude_gender,
        flipped: args.right_face !== '0',
        generateFlipped: args.right_face === '2'
      },
      items: {
        include: !args.exclude_icon_sets,
        sets: args.icon_sets
      },
      others: {
        include: !args.exclude_special
      },
    },
    minify: !args.no_minify,
    padding: !args.no_padding,
    generate: Object.keys(out).reduce((files, f) => ({ ...files, ...(out[f] ? { [f]: outPaths[f] } : {}) }), {})
  }
}

export default cliToArgs
