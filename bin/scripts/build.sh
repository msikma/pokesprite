#!/usr/bin/env bash
BASEDIR=$(dirname "$0")
DIR=$BASEDIR/..
if [ ! -f "$DIR/pokesprite.php" ]; then
    echo "Cannot find pokesprite.php"
    exit
fi

rm -f "$DIR/output/"*
"$DIR/pokesprite.php"
java -jar "$DIR/tools/closure-compiler.jar" --compilation_level ADVANCED_OPTIMIZATIONS --js "$DIR/output/pokesprite.js" --js_output_file "$DIR/output/pokesprite.min.js" --charset UTF-8
rm "$DIR/output/pokesprite.js"
node-sass --sourcemap=none --output-style compressed "$DIR/output/pokesprite.scss" "$DIR/output/pokesprite.min.css"
rm "$DIR/output/pokesprite.scss"
rm -f "$DIR/output/pokesprite.css.map"
node-sass --sourcemap=none --output-style compressed "$DIR/resources/overview.scss" "$DIR/output/overview.min.css"
rm -f "$DIR/output/overview.css.map"
cp "$DIR/resources/node-readme.md" "$DIR/output/readme.md"
cp "$DIR/LICENSE" "$DIR/output/license"
cp "$DIR/resources/pkg-info.json" "$DIR/output/package.json"
