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
sass --sourcemap=none --style compressed "$DIR/output/pokesprite.scss" "$DIR/output/pokesprite.css"
rm "$DIR/output/pokesprite.scss"
rm -f "$DIR/output/pokesprite.css.map"
sass --sourcemap=none --style compressed "$DIR/resources/overview.scss" "$DIR/output/overview.css"
rm -f "$DIR/output/overview.css.map"
