/** PokéSprite - pokesprite-core <https://github.com/msikma/pokesprite>
  * © MIT license */

/**
 * Generates a sprite.
 *
 * Settings format:
 *
 *    { icons:
 *       { pokemon:
 *          { include: true,
 *            forms: true,
 *            regular: true,
 *            shiny: true,
 *            genders: 'both',
 *            flipped: true,
 *            generateFlipped: false },
 *         items: { include: true, sets: '*' },
 *         others: { include: true } },
 *      minify: true,
 *      padding: true,
 *      generate:
 *       { css: 'pkmn.css',
 *         html: 'overview.html',
 *         img: 'pkmn.png',
 *         md: 'overview.md',
 *         scss: 'pkmn.scss' } }
 *
 * TBA.
 */
const generateSprite = async (outputPath, settings) => {
  console.log('core args', outputPath, settings);
}

export default {
  generateSprite
}
