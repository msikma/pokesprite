<frm=::bold>PokéSprite {{$revision}}</frm>
{{$copyright}}
{{$error}}
Usage:
    <frm=::bold>pokesprite</frm> [<frm=::underline>options</frm>]

Description:
    Generates a complete image sprite of all Pokémon in the National Pokédex (along with several other types of icons), and the SCSS file to make them usable. This sprite can then be used to efficiently display these icons inside a web page. Pokémon icons are arranged sequentially, and a growing packer algorithm is used to arrange other images inside the sprite. For more information, see <{{$website}}>.
    
Options:
  <frm=::bold>--dir-icons=dir</frm>
    Icons directory. Default: <frm=::underline>./icons/</frm>.
  
  <frm=::bold>--dir-pkmn=dir</frm>
    Pokémon icons directory. Relative to the icons directory. Default: <frm=::underline>pokemon/</frm>.
    
  <frm=::bold>--dir-data=dir</frm>
    Data directory. Default: <frm=::underline>./data/</frm>.
    
  <frm=::bold>--file-data=filename</frm>
    Data filename. Relative to the data directory. Default: <frm=::underline>pkmn-icons.json</frm>.

  <frm=::bold>--dir-output=dir</frm>
    Output directory. Default: <frm=::underline>./output/</frm>.
    
  <frm=::bold>--file-output-img-tmp=filename</frm>
    Temporary output image filename. Default: <frm=::underline>pkmn_unoptimized.png</frm>.
    
  <frm=::bold>--file-output-img=filename</frm>
    Final output image filename. Default: <frm=::underline>pkmn.png</frm>.
    
  <frm=::bold>--file-output-scss=filename</frm>
    SCSS output filename. Default: <frm=::underline>pkmn.scss</frm>.
    
  <frm=::bold>--file-output-html=filename</frm>
    HTML icon overview output filename. Default: <frm=::underline>overview.html</frm>.
    
  <frm=::bold>--file-output-md=filename</frm>
    Markdown icon overview output filename. Default: <frm=::underline>overview.md</frm>.
    
  <frm=::bold>--dir-resources=dir</frm>
    Resources directory. Default: <frm=::underline>./resources/</frm>.
    
  <frm=::bold>--tpl-scss=filename</frm>
    Template to use for SCSS generation. Relative to the resources directory. Default: <frm=::underline>stylesheet-tpl.scss</frm>.
    
  <frm=::bold>--tpl-html</frm>
    Template to use for HTML icon overview generation. Relative to the resources directory. Default: <frm=::underline>overview-tpl.html</frm>.
    
  <frm=::bold>--path-pngcrush=filename</frm>
    Path to pngcrush. Default: <frm=::underline>./tools/pngcrush</frm>.
    
  <frm=::bold>--file-exts=expr</frm>
    Permitted file extensions for images to be included. Must be a comma-separated list. Default: <frm=::underline>png</frm>.
    
  <frm=::bold>--icon-sets=expr</frm>
    List of icon sets to include in the image. Defaults to <frm=::underline>all known sets</frm>.
  
  <frm=::bold>--include-right=[0|1|2]</frm>
    Whether to include right-facing icons. If the argument is <frm=::underline>0</frm>, none will be included. If no argument is given or the argument is <frm=::underline>1</frm>, only Pokémon that have a unique right-facing icon are included (default). If the argument is <frm=::underline>2</frm>, all Pokémon get a right-facing icon (and those that don't have one, have their regular icon flipped).

  <frm=::bold>--generate-markdown</frm>
    Generates a Markdown overview file in addition to the HTML overview file.

  <frm=::bold>--pkmn-lang=[eng|jpn|jpn_ro]</frm>
    Sets the language of Pokémon names to use for the output. Default: <frm=::underline>eng</frm>.

  <frm=::bold>--lang=[en_us|ja]</frm>
    Sets the language of program feedback. Default: <frm=::underline>en_us</frm>.
  
  <frm=::bold>--exclude-pkmn</frm>
    Excludes Pokémon icons.
    
  <frm=::bold>--exclude-regular</frm>
    Excludes regular (non-shiny) icons.
    
  <frm=::bold>--exclude-shiny</frm>
    Excludes shiny icons.

  <frm=::bold>--exclude-forms</frm>
    Excludes alternate forms.

  <frm=::bold>--exclude-icon-sets</frm>
    Excludes icon sets (other than the Pokémon icons).

  <frm=::bold>--no-pngcrush</frm>
    Skips the pngcrush step.

  <frm=::bold>--no-padding</frm>
    Don't add a 1px padding around all images (recommended for retina compatibility).

  <frm=::bold>--verbose</frm>
    Displays debugging information for every image added to the spritesheet.

  <frm=::bold>--monochrome</frm>
    Displays all debugging information without colors.

  <frm=::bold>--help</frm>
    Displays this help text.
