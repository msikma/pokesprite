#!/usr/bin/env python3

# PokéSprite documentation script
#
# This script will generate the following documentation files:
#
#   * docs/overview/dex-gen7.html
#   * docs/overview/dex-gen8.html
#   * docs/overview/dex-gen8-new.html
#   * docs/overview/misc.html
#   * docs/overview/inventory.html
#   * docs/index.html
#
# These files are hosted on https://msikma.github.io/pokesprite.
# Existing files will be overwritten.

import json
import subprocess
import html
from os import makedirs
from os.path import abspath, dirname
from pathlib import Path
from pprint import pprint

BASE_DIR = str(Path(dirname(abspath(__file__))).parent)
TARGET_DIR = f'{BASE_DIR}/docs'
DEX_JSON = f'{BASE_DIR}/data/pokemon.json'
ITM_JSON = f'{BASE_DIR}/data/item-map.json'
MSC_JSON = f'{BASE_DIR}/data/misc.json'
ITM_UNL_JSON = f'{BASE_DIR}/data/item-unlinked.json'
ETC_JSON = f'{BASE_DIR}/data/other-sprites.json'
META_JSON = f'{BASE_DIR}/data/meta.json'
PROJECT_URL = 'https://github.com/msikma/pokesprite'
DOCS_BASE_URL = 'https://msikma.github.io/pokesprite'
REPO_BASE_URL = 'https://raw.githubusercontent.com/msikma/pokesprite/master'
REPO_PACKAGE = f'{BASE_DIR}/package.json'
DEX_SPRITE_DIR = { 7: f'{REPO_BASE_URL}/pokemon-gen7x', 8: f'{REPO_BASE_URL}/pokemon-gen8' }

# Text displayed for "empty" values (instead of an empty table cell)
EMPTY_PLACEHOLDER = '–'

# Global sprite counter for the leftmost column
_n_counter = None

def generate_index_page(version, commit):
  '''Generates the index page'''
  old_links = [
    ['https://web.archive.org/web/20200224000259/https://msikma.github.io/pokesprite/build/overview.html', 'Icon overview'],
    ['https://web.archive.org/web/20200224000241/https://msikma.github.io/pokesprite/build/files.html', 'CSS/JS/image files']
  ]
  old_images = [
    ['https://web.archive.org/web/20200224003306/https://msikma.github.io/pokesprite/other/pkmn-regular-only.png', 'Regular Pokémon only'],
    ['https://web.archive.org/web/20200224003306/https://msikma.github.io/pokesprite/other/pkmn-shiny-only.png', 'Shiny Pokémon only'],
    ['https://web.archive.org/web/20200224003306/https://msikma.github.io/pokesprite/other/items-only.png', 'Items only']
  ]
  content = wrap_in_html('''
    <div class="markdown-body">
      <div class="text-section">
        <h1 class="title">%(title_sprite)sPokéSprite</h1>
        <h2 class="subtitle">Database project of box and inventory sprites from the Pokémon core series games</h2>
        <ul class="menu">%(menu_links)s</ul>
        <p><img class="banner" src="%(example_image)s" width="%(example_image_width)s" /></p>
        <p>See the <a href="%(project_url)s">project page on Github</a> for more information.</a></p>
        <h3>Legacy images</h3>
        <p>As of Feb 2020, this project is up-to-date with Gen 8 (Pokémon Sword/Shield). All old images from Gen 7 (Pokémon Ultra Sun/Ultra Moon) are still available for legacy support.</p>
        <p><strong>Archived versions of the legacy overview pages:</strong></p>
        <ul>
          %(old_links)s
          %(old_images)s
        </ul>
      </div>
      <div class="text-section last">
        <h2>License</h2>
        <p>The sprite images are © Nintendo/Creatures Inc./GAME FREAK Inc.<br />
        Everything else, and the programming code, is governed by the MIT license.</p>
      </div>
    </div>
  ''' % {
    'menu_links': get_menu_links('index'),
    'example_image': 'https://raw.githubusercontent.com/msikma/pokesprite/master/resources/images/banner_gen8_2x.png',
    'example_image_width': '726',
    'title_sprite': get_title_venusaur(),
    'project_url': PROJECT_URL,
    'old_links': ''.join(['<li><a href="%s">Gen 7 - %s</a></li>' % (item[0], item[1]) for item in old_links]),
    'old_images': ''.join(['<li><a href="%s">Gen 7 - %s</a></li>' % (item[0], item[1]) for item in old_images])
  }, 'Index', version, commit, '.')
  return content

def wrap_docs_page(table_content, gen, gen_dir, curr_page, json_file, title, is_items_page, is_misc_page, version, commit, sprites_counter, new_sprites_only):
  '''Wraps a documentation page in a table node and adds styling'''
  gen_url = f'{REPO_BASE_URL}/{gen_dir}'
  json_url = f'{REPO_BASE_URL}/data/{json_file}'
  gen_link = f'<a href="{gen_url}"><code>{gen_dir}</code></a>'
  json_link = f'<a href="{json_url}"><code>data/{json_file}</code></a>'

  if title is None and gen:
    title = 'Gen ' + str(gen) + (f' (new sprites only)' if new_sprites_only else '')
  
  main_info = '''
    <p>This table lists all inventory item sprites. These items are from the last several games and is up-to-date as of Pokémon Sword/Shield. The sprites are from Gen 3 through 8.</p>
    <p>All sprites are 32×32 in size. There are two sets of sprites: one with a Sword/Shield style white outline around the sprites, and one without (as all previous games). Both sets contain the same number of sprites, and both are listed below.</p>
  ''' if is_items_page else '''
    <p>This table lists all miscellaneous sprites—all that aren't Pokémon box sprites or inventory items.</p>
    <p>
      The data for this list can be found in %(json_link)s.
    </p>
  ''' % {
    'json_link': json_link
  } if is_misc_page else '''
    <p>This table lists all Pokémon box sprites for <strong>Gen %(gen)s%(subtype)s</strong>, which can be found in the %(gen_link)s directory. The list is up-to-date as of Pokémon Sword/Shield, and some of the sprites are from an earlier generation. All shiny sprites were custom-made and are not found in-game.</p>
    <p>All box sprites are 68×56 as of Gen 8; the old Gen 7 sprites have been updated to the new size and contrast. (The original 40×30 sprites from Gen 7 are still available <a href="https://github.com/msikma/pokesprite/tree/master/icons">in the legacy sprites directory</a>.)</p>
    <p>
      The data for this list (Pokémon names, forms, etc.) is from the <code>gen-%(gen)s</code> key of the items from %(json_link)s.
      %(new_sprites_only)s
    </p>
  ''' % {
    'gen': gen,
    'gen_link': gen_link,
    'json_link': json_link,
    'new_sprites_only': '<strong>Only items that contain <code>"is_prev_gen_icon": false</code> are shown.</strong>' if new_sprites_only else '',
    'subtype': ' (new sprites only)' if '-new' in curr_page else '',
  }
  return wrap_in_html('''
    <div class="markdown-body">
      <div class="text-section">
        <h1 class="title">%(title_sprite)sPokéSprite</h1>
        <h2 class="subtitle">Database project of box and inventory sprites from the Pokémon core series games</h2>
        <ul class="menu">%(menu_links)s</ul>
        %(main_info)s
        <p>See the <a href="%(project_url)s">project page on Github</a> for more information.</a></p>
      </div>
      <table class="pokesprite%(gen)s">
        %(table_content)s
      </table>
      <div class="text-section last">
        <p>The sprite images are © Nintendo/Creatures Inc./GAME FREAK Inc.<br />
        Everything else, and the programming code, is governed by the MIT license.</p>
      </div>
    </div>
  ''' % {
    'table_content': table_content,
    'title_sprite': get_title_venusaur(),
    'gen': ' gen%s' % gen if gen else '',
    'main_info': main_info,
    'curr_page': curr_page,
    'version': version,
    'menu_links': get_menu_links(curr_page),
    'commit': commit,
    'project_url': PROJECT_URL,
    'sprites_counter': sprites_counter
  }, title, version, commit, '..')

def get_menu_links(curr_page):
  menu = [
    ['index', 'index', 'Index'],
    ['overview/dex-gen7', 'dex-gen7', 'Gen 7'],
    ['overview/dex-gen8', 'dex-gen8', 'Gen 8'],
    ['overview/dex-gen8-new', 'dex-gen8-new', 'Gen 8 (New sprites)'],
    ['overview/inventory', 'inventory', 'Inventory'],
    ['overview/misc', 'misc', 'Miscellaneous']
  ]
  menu_links = ['<li><a href="%s" class="%s">%s</a></li>' % (docs_url(item[0]), 'curr' if item[1] == curr_page else '', item[2]) for item in menu]
  return ''.join(menu_links)

def get_title_venusaur():
  return get_img_node(get_pkm_url(DEX_SPRITE_DIR[8], 'venusaur', True, False, False), None, 'Shiny Venusaur', 'p')

def wrap_in_html(content, title, version, commit, res_dir = '.'):
  return '''
<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>PokéSprite%(title)s</title>
    <!--
    PokéSprite - Documentation page
    pokesprite-images v%(version)s %(commit)s

    (；ﾟ～ﾟ)ゝ”
    -->
    <link rel="stylesheet" href="%(res_dir)s/resources/gh-markdown.css" />
    <link rel="stylesheet" href="%(res_dir)s/resources/pokesprite-docs.css" />
  </head>
  <body>
    %(content)s
  </body>
</html>
  '''.strip() % {
    'res_dir': res_dir,
    'content': content,
    'title': ' - ' + title if title else '',
    'version': version,
    'commit': commit
  }

def run_cmd(cmd):
  return subprocess.check_output(cmd, cwd=BASE_DIR).strip().decode('utf-8')

def write_file(filename, content):
  with open(filename, 'wt') as file:
    print(content, file=file)

def docs_url(slug):
  return f'{DOCS_BASE_URL}/{slug}.html'

def read_repo_state():
  '''Returns information about the current state of the repository'''
  version = '[unknown]'
  try:
    package = read_json_file(REPO_PACKAGE)
    version = package['version']
  except:
    pass

  # In case this fails (e.g. Git is not installed, or this isn't a repo).
  commit = '[unknown]'
  try:
    count = run_cmd(['git', 'rev-list', 'HEAD', '--count'])
    branch = run_cmd(['git', 'rev-parse', '--abbrev-ref', 'HEAD'])
    hash = run_cmd(['git', 'rev-parse', '--short', '--verify', 'HEAD'])
    commit = f'{branch}-{count} [{hash}]'
    return (version, commit)
  except subprocess.CalledProcessError:
    return (version, commit)
  except OSError:
    return (version, commit)

def read_json_file(file):
  '''Reads a single JSON and returns a dict'''
  with open(file) as json_file:
    return json.load(json_file)

def read_data():
  '''Retrieves Pokémon and items JSON data'''
  return {
    'dex': read_json_file(DEX_JSON),
    'itm': read_json_file(ITM_JSON),
    'misc': read_json_file(MSC_JSON),
    'itm_unl': read_json_file(ITM_UNL_JSON),
    'meta': read_json_file(META_JSON),
    'etc': read_json_file(ETC_JSON)
  }

def get_pkm_form(form_name, form_alias, is_unofficial_icon):
  title = []
  daggers = []

  if form_alias is not None:
    alias = f'&quot;{form_alias}&quot;' if form_alias else 'default form'
    title.append(f'Alias of {alias}')
    daggers.append('†')
  if is_unofficial_icon:
    title.append('Unofficial icon (see below)')
    daggers.append('‡')

  if len(title):
    title = '; '.join(title)
    daggers = ''.join(daggers)
    return f'<attr title="{title}"><span>{form_name}</span>{daggers}</attr>'

  return form_name

def get_pkm_url(base, slug, is_shiny, is_female, is_right):
  return ''.join([
    base,
    '/regular' if not is_shiny else '/shiny',
    '/female' if is_female else '',
    '/right' if is_right else '',
    '/',
    slug,
    '.png'
  ])

def get_etc_url(base, slug):
  return ''.join([
    base,
    '/',
    slug,
    '.png'
  ])

def get_itm_url(base, ns, group, file):
  return ''.join([
    base,
    '/',
    ns,
    '/',
    group,
    '/',
    file,
    '.png'
  ])

def get_misc_url(base, file):
  return ''.join([
    base,
    '/misc/',
    file
  ])

def get_pkm_gen(is_prev_gen_icon, docs_gen):
  prev_gen = str(int(docs_gen) - 1)
  if is_prev_gen_icon:
    return { 'node': prev_gen, 'expl': f'Icon is from generation {prev_gen}', 'cls': '' }
  return ''

def get_pkm_gender(is_female, has_female):
  if not has_female:
    return ''
  node = 'F' if is_female else 'M'
  expl = 'Female sprite' if is_female else 'Male sprite'
  return { 'node': node, 'expl': expl, 'cls': f' gender-{node.lower()}' }

def get_pkm_unofficial(is_unofficial_icon):
  return '✓' if is_unofficial_icon else ''

def get_td_node(td):
  # If the column is a list, we'll render several columns.
  if isinstance(td, list):
    cols = []
    non_empty = list(filter(len, td))
    first_col_span = len(td) - (len(non_empty) - 1)
    cols.append(f'<td class="form" colspan="{first_col_span}">{td[0]}</td>')
    # The rest of the columns are either plain strings, or a dict containing { node, expl }.
    for col in non_empty[1:]:
      if isinstance(col, dict):
        node = col['node']
        expl = col['expl']
        cls = col['cls']
        cols.append(f'<td title="{expl}" class="min{cls}">{node}</td>')
      else:
        cols.append(f'<td class="min">{col}</td>')
    return ''.join(cols)

  # Otherwise it's a string, and we'll check if it contains an image.
  attr = ' class="image"' if str(td)[:4] == '<img' else ''
  return f'<td{attr}>{td}</td>'

def get_img_node(url, name, form_name, type):
  form_name = html.escape(form_name)
  return f'<img class="{type}" src="{url}" alt="{form_name}" />'

def reset_counter():
  '''Resets the global sprite counter'''
  global _n_counter
  _n_counter = 0

def get_counter():
  '''Increments and returns the global sprite counter'''
  global _n_counter
  _n_counter += 1
  return _n_counter

def determine_form(slug, form_name, form_data):
  '''Return two Pokémon form strings: one for display, and one referencing a file'''
  # The regular form is indicated by a '$', and can be an alias of another one.
  form_value = '' if form_name == '$' else form_name
  form_alias = form_data.get('is_alias_of', None)
  form_alias = '' if form_alias == '$' else form_alias

  form_file = form_alias if form_alias is not None else form_value
  form_display = form_value

  # Save two slugs: the first one is literally just the Pokémon name plus its form,
  # and the other uses the 'is_alias_of' slug and is used for selecting the right file.
  form_slug_display = '-'.join(filter(len, [slug, form_display]))
  form_slug_file = '-'.join(filter(len, [slug, form_file]))

  return (form_slug_file, form_slug_display, form_alias)

def append_pkm(cols, base, slug_display, slug_file, form_name, form_alias, has_female, is_female, is_right, is_unofficial_icon, is_prev_gen_icon, docs_gen):
  '''Adds a single Pokémon row'''
  cols.append([
    get_counter(),
    get_img_node(get_pkm_url(base, slug_file, False, is_female, is_right), None, form_name, 'p'),
    get_img_node(get_pkm_url(base, slug_file, True, is_female, is_right), None, form_name, 'p'),
    [get_pkm_form(form_name, form_alias, is_unofficial_icon), get_pkm_gender(is_female, has_female), get_pkm_gen(is_prev_gen_icon, docs_gen)],
    f'<code>{slug_display}</code>'
  ])

def append_pkm_form(cols, base, slug_display, slug_file, form_name, form_alias, has_female, has_right, add_female, add_right, is_unofficial_icon, is_prev_gen_icon, docs_gen):
  '''Adds columns for a single form: at least two, then female sprites, then right-facing sprites'''
  append_pkm(cols, base, slug_display, slug_file, form_name, form_alias, has_female, False, False, is_unofficial_icon, is_prev_gen_icon, docs_gen)
  if has_female and add_female: append_pkm(cols, base, slug_display, slug_file, form_name, form_alias, has_female, True, False, is_unofficial_icon, is_prev_gen_icon, docs_gen)
  if has_right and add_right: append_pkm(cols, base, slug_display, slug_file, form_name, form_alias, has_female, False, True, is_unofficial_icon, is_prev_gen_icon, docs_gen)

def generate_misc_table(misc, meta, curr_page, json_file, version = '[unknown]', commit = '[unknown]'):
  '''Generates a documentation table for miscellaneous sprites'''
  reset_counter()
  groups = meta['misc-groups']
  base_url = REPO_BASE_URL

  sprites_counter = 0

  buffer = []
  buffer.append('<thead>')
  buffer.append('<tr class="title"><th></th><th colspan="10">Miscellaneous sprite overview table<br /><span>pokesprite-images v%(version)s %(commit)s</span></th></tr>' % { 'version': version, 'commit': commit })
  buffer.append('</thead>')
  buffer.append('<tbody>')

  # Ribbons
  buffer.append('<tr><th></th><td colspan="6" class="group">%s</td></tr>' % groups['ribbon']['name']['eng'])
  #buffer.append('<tr class="header"><th>#</th><th>Name</th><th>名前</th><th>Sprite</th><th>Origin</th><th colspan="2">Description/gen</th><th colspan="2">Filename/gen</th></tr>')
  buffer.append('<tr class="header"><th>#</th><th>Name</th><th>名前</th><th>Sprite</th><th>Origin</th><th colspan="2">Filename/gen</th></tr>')

  for item in misc['ribbon']:
    count = get_counter()
    name = item['name']
    name_eng = name['eng']
    name_jpn = name['jpn']
    name_jpn_ro = name['jpn_ro']
    origin_gen = item['origin_gen']
    desc = item['description']
    desc_eng = desc['eng']
    desc_gen = desc['from_gen']
    desc_eng_esc = html.escape(desc_eng)
    name_eng_desc = f'<attr title="{desc_eng_esc}">{name_eng}</attr>'
    for k, v in item['files'].items():
      gen_n = k.split('-')[1]
      buffer.append('<tr class="variable-height">')
      buffer.append(f'<td>{count}</td>')
      buffer.append(f'<td>{name_eng_desc}</td>')
      buffer.append(f'<td>{name_jpn}</td>')
      buffer.append('<td class="image item">' + get_img_node(get_misc_url(base_url, v), None, f"Sprite for '{name_eng}'", 'm') + '</td>')
      buffer.append(f'<td>Gen {origin_gen}</td>')
      #buffer.append(f'<td class="long-text">{desc_eng}</td>')
      #buffer.append(f'<td>Gen {desc_gen}</td>')
      buffer.append(f'<td class="filler"><code>{v}</code></td>')
      buffer.append(f'<td>Gen {gen_n}</td>')
      buffer.append('</tr>')
      sprites_counter += 1
  
  # Body styles
  buffer.append('<tr><th></th><td colspan="6" class="group">%s</td></tr>' % groups['body-style']['name']['eng'])
  buffer.append('<tr class="header"><th>#</th><th>Type</th><th>種類</th><th>Sprite</th><th colspan="4">Filename/gen</th></tr>')

  for item in misc['body-style']:
    count = get_counter()
    name = item['name']
    name_eng = name['eng']
    name_jpn = name['jpn']
    for k, v in item['files'].items():
      gen_n = k.split('-')[1]
      buffer.append('<tr class="variable-height">')
      buffer.append(f'<td>{count}</td>')
      buffer.append(f'<td>{name_eng}</td>')
      buffer.append(f'<td>{name_jpn}</td>')
      buffer.append('<td class="image item">' + get_img_node(get_misc_url(base_url, v), None, f"Sprite for '{name_eng}'", 'm') + '</td>')
      buffer.append(f'<td class="filler" colspan="2"><code>{v}</code></td>')
      buffer.append(f'<td>Gen {gen_n}</td>')
      buffer.append('</tr>')
      sprites_counter += 1
  
  buffer.append('</tbody>')
  buffer.append('<tfoot>')
  buffer.append('<tr>')
  buffer.append('''
    <td></td>
    <td colspan="10">
      <span>Note: ribbons for Gen 3 have had their sizes padded to 40×40 up from 32×32, and ribbons from Gen 3 and 4 have had their gamma curve adjusted to be identical to that of the later gens—both for consistency.<br /></span>
    </td>
  ''')
  buffer.append('</tr>')
  buffer.append('</tfoot>')
  return wrap_docs_page('\n'.join(buffer), None, None, curr_page, json_file, 'Miscellaneous sprites', False, True, version, commit, sprites_counter, False)

def generate_items_table(itm, itm_unl, inv, etc, dirs, curr_page, json_file, version = '[unknown]', commit = '[unknown]'):
  '''Generates a documentation table for inventory sprites'''
  reset_counter()
  new_sprites_only = '-new' in curr_page
  base_url = REPO_BASE_URL

  sprites_counter = 0

  buffer = []
  buffer.append('<thead>')
  buffer.append('<tr class="title"><th></th><th colspan="11">Inventory sprite overview table<br /><span>pokesprite-images v%(version)s %(commit)s</span></th></tr>' % { 'version': version, 'commit': commit })
  buffer.append('<tr class="header"><th>#</th><th>Item ID</th><th>Name</th><th colspan="2">Sprites</th><th>Group</th><th colspan="2">Filename/notes</th></tr>')
  buffer.append('</thead>')
  buffer.append('<tbody>')

  item_dict = {}
  for id, item in itm.items():
    group, name = item.split('/')
    if not item_dict.get(group):
      item_dict[group] = []
    item_dict[group].append({ 'name': name, 'id': id, 'linked': True })
  
  for item, details in itm_unl.items():
    group, name = item.split('/')
    type = { 'name': name, 'id': None, 'linked': False, 'type': details['type'], 'dupe_id': details.get('of', {}).get('item_id') }
    of_file = details.get('of', {}).get('file')
    if details['type'] == 'duplicate' and of_file:
      type['expl'] = f'Duplicate of <code>{of_file}</code>'
    if details['type'] == 'specific' and of_file:
      type['expl'] = f'Subitem of <code>{of_file}</code>'
    item_dict[group].append(type)

  for group, items in item_dict.items():
    item_dict[group] = sorted(items, key=lambda x: x['name'])

  for group, items in item_dict.items():
    if not len(items): continue
    title = inv['item-groups'].get(group, None)
    title = title['name']['eng'] if title else group.title()
    buffer.append(f'<tr><td></td><td colspan="7" class="group">{title}</td></tr>')
    for item in items:
      count = get_counter()
      name = item['name']
      id = item['id']
      expl = item.get('expl', False)
      imgs = ['<td class="image item">' + get_img_node(get_itm_url(base_url, dir, group, name), None, f'"{name}" (dir)', 'i') + '</td>' for dir in dirs]
      filename = group + '/' + name + '.png'
      buffer.append('<tr>')
      buffer.append(f'<td>{count}</td>')
      if id is not None:
        buffer.append(f'<td class="item-id"><code>{id}</code></td>')
      else:
        buffer.append(f'<td class="item-id">{EMPTY_PLACEHOLDER}</td>')
      buffer.append(f'<td>{name}</td>')
      buffer.append(''.join(imgs))
      buffer.append(f'<td>{group}</td>')
      if expl:
        buffer.append(f'<td class="item-id" colspan="2"><code>{filename}</code> ({expl})</td>')
      else:
        buffer.append(f'<td colspan="2" class="item-id"><code>{filename}</code></td>')
      buffer.append('</tr>')
      sprites_counter += 1

  buffer.append('</tbody>')

  buffer.append('<tfoot>')
  buffer.append('<tr>')
  buffer.append('''
    <td></td>
    <td colspan="10">
      <span>Note: item IDs are accurate only for the latest Pokémon game.<br /></span>
      <span>Only filenames are available, not proper item names or aliases (hence some items appear multiple times). This will be fixed in a future release.</span>
    </td>
  ''')
  buffer.append('</tr>')
  buffer.append('</tfoot>')
  return wrap_docs_page('\n'.join(buffer), None, None, curr_page, json_file, 'Inventory item sprites', True, False, version, commit, sprites_counter, new_sprites_only)

def generate_dex_table(dex, etc, gen, gen_dir, curr_page, json_file, add_female = True, add_right = False, version = '[unknown]', commit = '[unknown]'):
  '''Generates a documentation table for Pokémon sprites'''
  reset_counter()
  new_sprites_only = '-new' in curr_page
  base_url = DEX_SPRITE_DIR[gen]

  sprites_counter = 0

  buffer = []
  buffer.append('<thead>')
  buffer.append('<tr class="title"><th></th><th colspan="10">Gen %(gen)s sprite overview table%(subtype)s<br /><span>pokesprite-images v%(version)s %(commit)s</span></th></tr>' % { 'subtype': ' (new sprites only)' if new_sprites_only else '', 'gen': gen, 'version': version, 'commit': commit })
  buffer.append('<tr class="header"><th>#</th><th>Dex</th><th>Name</th><th colspan="2">名前/ローマ字</th><th colspan="2">Sprites</th><th colspan="3">Form</th><th>Slug</th></tr>')
  buffer.append('</thead>')
  buffer.append('<tbody>')

  # Loop over each Pokémon and generate rows for each of its forms, one regular and one shiny,
  # including gender differences and right-facing sprites.
  for idx, pkm in dex.items():
    #if int(idx) > 25 and idx != '172' and idx != '593': continue
    slug_en = pkm['slug']['eng']
    gen_data = pkm[f'gen-{str(gen)}']

    # Main columns - contains general information spanned across all rows.
    main_cols = [f'#{str(idx)}', pkm['name']['eng'], pkm['name']['jpn'], pkm['name']['jpn_ro']]

    # Form columns - form-specific information. A global sprite counter is also prepended.
    form_cols = []

    if not 'forms' in gen_data:
      continue

    for form_name, form_data in gen_data['forms'].items():
      form_slug_file, form_slug_display, form_alias = determine_form(slug_en, form_name, form_data)
      form_name_clean = EMPTY_PLACEHOLDER if form_name == '$' else form_name
      is_prev_gen_icon = form_data.get('is_prev_gen_icon', False)
      if new_sprites_only and is_prev_gen_icon:
        continue
      append_pkm_form(
        form_cols,
        base_url,
        form_slug_display,
        form_slug_file,
        form_name_clean,
        form_alias,
        form_data.get('has_female', False),
        form_data.get('has_right', False),
        add_female,
        add_right,
        form_data.get('is_unofficial_icon', False),
        form_data.get('is_prev_gen_icon', False),
        gen
      )

    if not form_cols:
      continue
    first_col = form_cols[0]
    rest_cols = form_cols[1:]
    sprites_counter += len(form_cols)

    # First row (containing one form and all main cols):
    buffer.append('<tr>')
    buffer.append(f'<td class="counter">{first_col[0]}</td>')
    for col in main_cols:
      buffer.append(f'<td rowspan="{max(len(form_cols), 1)}">{col}</td>')
    for first_row_col in first_col[1:]:
      buffer.append(get_td_node(first_row_col))
    buffer.append('</tr>')

    # All other rows (only form cols, skipping over the main cols):
    for row in rest_cols:
      buffer.append('<tr>')
      for col in row:
        buffer.append(get_td_node(col))
      buffer.append('</tr>')

  # Add the remaining other icons.
  if not new_sprites_only:
    buffer.append('<tr class="header">')
    buffer.append('<th>#</th><th>Dex</th><th>Name</th><th colspan="2">名前/ローマ字</th><th colspan="2">Sprites</th><th colspan="4">Filename</th>')
    buffer.append('</tr>')
    for item in etc['pokemon']:
      buffer.append('<tr>')
      count = get_counter()
      name_eng = item['name']['eng']
      name_jpn = item['name']['jpn']
      file = item['file']
      img = get_img_node(get_etc_url(base_url, file), None, name_eng, 'p')
      buffer.append(f'''
        <td>{count}</td>
        <td rowspan="1">{EMPTY_PLACEHOLDER}</td>
        <td rowspan="1">{name_eng}</td>
        <td rowspan="1" colspan="2">{name_jpn}</td>
        <td class="image" colspan="2">{img}</td>
        <td colspan="4"><code>{file}.png</code></td>
      ''')
      buffer.append('</tr>')

  buffer.append('</tbody>')

  buffer.append('<tfoot>')
  buffer.append('<tr>')
  buffer.append('''
    <td></td>
    <td colspan="10">
      <span>†: form is an alias of another form and doesn't have a separate image.<br /></span>
      <span>‡: this icon is unofficial (not directly lifted from the games; only applies to non-shiny sprites, as shiny sprites are all unofficial).</span>
    </td>
  ''')
  buffer.append('</tr>')
  buffer.append('</tfoot>')
  return wrap_docs_page('\n'.join(buffer), gen, gen_dir, curr_page, json_file, None, False, False, version, commit, sprites_counter, new_sprites_only)

def main():
  '''Generates several documentation files for the /docs directory'''
  version, commit = read_repo_state()
  json_data = read_data()
  makedirs(f'{TARGET_DIR}/overview/', exist_ok=True)

  write_file(f'{TARGET_DIR}/overview/dex-gen7.html', generate_dex_table(json_data['dex'], json_data['etc'], 7, 'pokemon-gen7x', 'dex-gen7', 'pokemon.json', True, False, version, commit))
  write_file(f'{TARGET_DIR}/overview/dex-gen8.html', generate_dex_table(json_data['dex'], json_data['etc'], 8, 'pokemon-gen8', 'dex-gen8', 'pokemon.json', True, False, version, commit))
  write_file(f'{TARGET_DIR}/overview/dex-gen8-new.html', generate_dex_table(json_data['dex'], json_data['etc'], 8, 'pokemon-gen8', 'dex-gen8-new', 'pokemon.json', True, False, version, commit))
  write_file(f'{TARGET_DIR}/overview/inventory.html', generate_items_table(json_data['itm'], json_data['itm_unl'], json_data['meta'], json_data['etc'], ['items', 'items-outline'], 'inventory', 'item-map.json', version, commit))
  write_file(f'{TARGET_DIR}/overview/misc.html', generate_misc_table(json_data['misc'], json_data['meta'], 'misc', 'misc.json', version, commit))
  write_file(f'{TARGET_DIR}/index.html', generate_index_page(version, commit))

if __name__== "__main__":
  main()
