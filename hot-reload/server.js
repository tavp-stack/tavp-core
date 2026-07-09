const chokidar = require('chokidar');
const livereload = require('livereload');
const express = require('express');
const path = require('path');

const app = express();
const PORT = process.env.HOT_RELOAD_PORT || 35729;

// Start livereload server
const lrServer = livereload.createServer({
    port: PORT,
    delay: 100,
});

// Watch for file changes
const watchPaths = [
    path.join(__dirname, '../resources/views/**/*.volt'),
    path.join(__dirname, '../public/**/*.css'),
    path.join(__dirname, '../public/**/*.js'),
];

const watcher = chokidar.watch(watchPaths, {
    ignoreInitial: true,
    ignored: /(^|[\/\\])\.(?!git)/,
});

watcher.on('change', (filePath) => {
    console.log(`[Hot Reload] File changed: ${filePath}`);
    lrServer.refresh(filePath);
});

watcher.on('add', (filePath) => {
    console.log(`[Hot Reload] File added: ${filePath}`);
    lrServer.refresh(filePath);
});

watcher.on('unlink', (filePath) => {
    console.log(`[Hot Reload] File deleted: ${filePath}`);
    lrServer.refresh(filePath);
});

console.log(`[Hot Reload] Livereload server started on port ${PORT}`);
console.log(`[Hot Reload] Watching for changes in:`);
watchPaths.forEach(p => console.log(`  - ${p}`));

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\n[Hot Reload] Shutting down...');
    watcher.close();
    lrServer.close();
    process.exit(0);
});
