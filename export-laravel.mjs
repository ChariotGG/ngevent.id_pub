// export-laravel.mjs
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ============================================
// KONFIGURASI UNTUK LARAVEL PROJECT
// ============================================

// Folder yang INGIN di-include (kode custom Laravel)
const INCLUDE_FOLDERS = [
    'app',
    'config',
    'database',
    'resources',
    'routes',
    'tests',
];

// Folder yang PASTI di-exclude
const EXCLUDE_FOLDERS = [
    'node_modules',
    'vendor',
    '.git',
    'storage',
    'bootstrap/cache',
    'public/build',
    'public/vendor',
    '.idea',
    '.vscode',
];

// File yang di-exclude
const EXCLUDE_FILES = [
    '.env',
    '.env.local',
    '.env.production',
    '.env.example',
    'package-lock.json',
    'yarn.lock',
    'pnpm-lock.yaml',
    'composer.lock',
    '.gitignore',
    '.editorconfig',
    'phpunit.xml',
];

// EKSTENSI UNTUK LARAVEL - TERMASUK PHP DAN BLADE!
const EXTENSIONS = [
    '.php',
    '.blade.php',
    '.js',
    '.css',
    '.scss',
    '.vue',
    '.json',
    '.sql',
];

// File config penting di root
const ROOT_CONFIG_FILES = [
    'composer.json',
    'package.json',
    'tailwind.config.js',
    'vite.config.js',
    'webpack.mix.js',
];

let output = '';
let fileCount = 0;
let totalLines = 0;
let fileList = [];

function isExcludedPath(filePath) {
    const relativePath = path.relative(__dirname, filePath).replace(/\\/g, '/');

    for (const folder of EXCLUDE_FOLDERS) {
        if (relativePath.startsWith(folder + '/') || relativePath === folder) {
            return true;
        }
        if (relativePath.includes('/' + folder + '/')) {
            return true;
        }
    }
    return false;
}

function shouldIncludeFile(filePath) {
    const relativePath = path.relative(__dirname, filePath).replace(/\\/g, '/');
    const fileName = path.basename(filePath);

    // Check excluded
    if (isExcludedPath(filePath)) return false;
    if (EXCLUDE_FILES.includes(fileName)) return false;
    if (fileName.startsWith('.env')) return false;

    // Check extension - handle .blade.php specially
    const isBlade = filePath.endsWith('.blade.php');
    const ext = isBlade ? '.blade.php' : path.extname(filePath);

    if (!EXTENSIONS.includes(ext)) return false;

    // Check if in include folders
    const isInIncludeFolder = INCLUDE_FOLDERS.some(folder =>
        relativePath.startsWith(folder + '/')
    );

    const isRootConfig = ROOT_CONFIG_FILES.includes(relativePath);

    return isInIncludeFolder || isRootConfig;
}

function collectAllFiles(dir) {
    let items;
    try {
        items = fs.readdirSync(dir);
    } catch {
        return;
    }

    for (const item of items) {
        const itemPath = path.join(dir, item);

        // Skip excluded folders early
        if (isExcludedPath(itemPath)) continue;
        if (item.startsWith('.') && item !== '.env.example') continue;

        try {
            const stat = fs.statSync(itemPath);

            if (stat.isDirectory()) {
                collectAllFiles(itemPath);
            } else {
                if (shouldIncludeFile(itemPath)) {
                    fileList.push(itemPath);
                }
            }
        } catch {
            // Skip
        }
    }
}

function buildStructure() {
    const structure = {};

    for (const filePath of fileList) {
        const relativePath = path.relative(__dirname, filePath).replace(/\\/g, '/');
        const parts = relativePath.split('/');

        let current = structure;
        for (let i = 0; i < parts.length - 1; i++) {
            if (!current[parts[i]]) {
                current[parts[i]] = {};
            }
            current = current[parts[i]];
        }
        current[parts[parts.length - 1]] = null; // File marker
    }

    return structure;
}

function printStructure(struct, prefix = '') {
    const keys = Object.keys(struct).sort((a, b) => {
        // Folders first, then files
        const aIsDir = struct[a] !== null;
        const bIsDir = struct[b] !== null;
        if (aIsDir && !bIsDir) return -1;
        if (!aIsDir && bIsDir) return 1;
        return a.localeCompare(b);
    });

    let result = '';
    keys.forEach((key, index) => {
        const isLast = index === keys.length - 1;
        const connector = isLast ? '└── ' : '├── ';
        const newPrefix = prefix + (isLast ? '    ' : '│   ');

        result += prefix + connector + key + '\n';

        if (struct[key] !== null) {
            result += printStructure(struct[key], newPrefix);
        }
    });

    return result;
}

function addFileContents() {
    // Sort files by path
    fileList.sort();

    for (const filePath of fileList) {
        try {
            const relativePath = path.relative(__dirname, filePath).replace(/\\/g, '/');
            const content = fs.readFileSync(filePath, 'utf-8');
            const lines = content.split('\n').length;

            output += '\n';
            output += '='.repeat(70) + '\n';
            output += `FILE: ${relativePath} (${lines} lines)\n`;
            output += '='.repeat(70) + '\n';
            output += content;
            if (!content.endsWith('\n')) output += '\n';

            fileCount++;
            totalLines += lines;
        } catch (err) {
            console.log(`⚠️  Skip: ${filePath} - ${err.message}`);
        }
    }
}

// ============================================
// MAIN
// ============================================

console.log('🚀 Memulai export proyek Laravel NGEVENT.ID...\n');

// Collect all files first
collectAllFiles(__dirname);

// Add root config files
for (const configFile of ROOT_CONFIG_FILES) {
    const filePath = path.join(__dirname, configFile);
    if (fs.existsSync(filePath) && !fileList.includes(filePath)) {
        fileList.push(filePath);
    }
}

console.log(`📁 Ditemukan ${fileList.length} file...\n`);

// Build output
output += '='.repeat(70) + '\n';
output += 'PROYEK: NGEVENT.ID (LARAVEL)\n';
output += 'Export Date: ' + new Date().toISOString() + '\n';
output += '='.repeat(70) + '\n\n';

output += 'STRUKTUR FOLDER & FILE:\n';
output += '─'.repeat(50) + '\n';
const structure = buildStructure();
output += printStructure(structure);
output += '\n';

// Add all file contents
addFileContents();

// Write output
const outputFile = 'ngevent-laravel-code.txt';
fs.writeFileSync(outputFile, output, 'utf-8');

const fileSizeKB = (fs.statSync(outputFile).size / 1024).toFixed(2);
const fileSizeMB = (fs.statSync(outputFile).size / 1024 / 1024).toFixed(2);

console.log('─'.repeat(50));
console.log('✅ EXPORT SELESAI!');
console.log('─'.repeat(50));
console.log(`📁 Total file    : ${fileCount}`);
console.log(`📝 Total baris   : ${totalLines.toLocaleString()}`);
console.log(`📄 Output file   : ${outputFile}`);
console.log(`📊 Ukuran        : ${fileSizeKB} KB (${fileSizeMB} MB)`);
console.log('─'.repeat(50));

if (totalLines > 20000) {
    console.log('\n⚠️  FILE CUKUP BESAR!');
    console.log('   Claude masih bisa menganalisa, tapi pertimbangkan');
    console.log('   untuk export per-bagian jika perlu analisis detail.');
}

// Show summary per folder
console.log('\n📊 RINGKASAN PER FOLDER:');
console.log('─'.repeat(50));

const folderStats = {};
for (const filePath of fileList) {
    const relativePath = path.relative(__dirname, filePath).replace(/\\/g, '/');
    const topFolder = relativePath.split('/')[0];
    if (!folderStats[topFolder]) {
        folderStats[topFolder] = { files: 0, lines: 0 };
    }
    folderStats[topFolder].files++;
    try {
        const content = fs.readFileSync(filePath, 'utf-8');
        folderStats[topFolder].lines += content.split('\n').length;
    } catch {}
}

for (const [folder, stats] of Object.entries(folderStats).sort((a, b) => b[1].lines - a[1].lines)) {
    console.log(`   ${folder.padEnd(20)} : ${stats.files.toString().padStart(4)} files, ${stats.lines.toLocaleString().padStart(7)} lines`);
}
