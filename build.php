<?php
namespace Ajg;

// Build the block
shell_exec('cd blocks/ajg-native-lands-search/ && npm run build');

// Zip the plugin
shell_exec('cd .. && zip -r ajg-native-lands.zip ajg-native-lands  -x *.git* *.gitignore* *node_modules* *package-lock.json* *package.json* *gulpfile.js* *build.php*');
