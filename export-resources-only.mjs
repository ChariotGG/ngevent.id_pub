// export-resources-only.mjs
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ============================================
// KONFIGURASI KHUSUS RESOURCES
// ============================================

const TARGET_FOLDER = 'resources';

// File yang tetap di-exclude
const EXCLUDE_FILES = [
    '.DS_Store',
    'Thumbs.db',
];

// Ekstensi yang ditampilkan (lebih lengkap)
const EXTENSIONS = [
    '.php',
    '.blade.php',
    '.js',
    '.jsx',
    '.ts',
    '.tsx',
    '.vue',
    '.css',
    '.scss',
    '.sass',
    '.less',
    '.json',
    '.html',
    '.svg',
    '.jpg',
    '.jpeg',
    '.png',
    '.gif',
    '.webp',
    '.md',
    '.txt',
];

let output = '';
let fileCount = 0;
let totalLines = 0;
let fileList = [];
let folderList = new Set();
let emptyFolders = [];

function shouldIncludeFile(filePath) {
    const fileName = path.basename(filePath);

    // Check excluded files
    if (EXCLUDE_FILES.includes(fileName)) return false;
    if (fileName.startsWith('.')) return false;

    // Check extension - handle .blade.php specially
    const isBlade = filePath.endsWith('.blade.php');
    const ext = isBlade ? '.blade.php' : path.extname(filePath);

    // Jika tidak ada extension atau tidak dalam list, skip
    if (!ext || !EXTENSIONS.includes(ext)) return false;

    return true;
}

function collectAllFilesAndFolders(dir, baseDir = dir) {
    let items;
    try {
        items = fs.readdirSync(dir);
    } catch {
        return;
    }

    // Track this folder
    const relativePath = path.relative(baseDir, dir).replace(/\\/g, '/');
    if (relativePath) {
        folderList.add(relativePath);
    }

    let hasFiles = false;

    for (const item of items) {
        const itemPath = path.join(dir, item);

        // Skip hidden files/folders
        if (item.startsWith('.')) continue;

        try {
            const stat = fs.statSync(itemPath);

            if (stat.isDirectory()) {
                const subHasFiles = collectAllFilesAndFolders(itemPath, baseDir);
                if (subHasFiles) hasFiles = true;
            } else {
                if (shouldIncludeFile(itemPath)) {
                    fileList.push(itemPath);
                    hasFiles = true;
                }
            }
        } catch {
            // Skip
        }
    }

    // Jika folder tidak punya file sama sekali, tandai sebagai empty
    if (!hasFiles && relativePath) {
        emptyFolders.push(relativePath);
    }

    return hasFiles;
}

function buildStructure() {
    const structure = {};

    // Add all folders first
    for (const folderPath of Array.from(folderList).sort()) {
        const parts = folderPath.split('/');
        let current = structure;
        
        for (const part of parts) {
            if (!current[part]) {
                current[part] = {};
            }
            current = current[part];
        }
    }

    // Add all files
    for (const filePath of fileList) {
        const relativePath = path.relative(
            path.join(__dirname, TARGET_FOLDER), 
            filePath
        ).replace(/\\/g, '/');
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

function printStructure(struct, prefix = '', currentPath = '') {
    const keys = Object.keys(struct).sort((a, b) => {
        // Folders first, then files
        const aIsFile = struct[a] === null;
        const bIsFile = struct[b] === null;
        if (aIsFile && !bIsFile) return 1;
        if (!aIsFile && bIsFile) return -1;
        return a.localeCompare(b);
    });

    let result = '';
    keys.forEach((key, index) => {
        const isLast = index === keys.length - 1;
        const connector = isLast ? '└── ' : '├── ';
        const newPrefix = prefix + (isLast ? '    ' : '│   ');
        const newPath = currentPath ? `${currentPath}/${key}` : key;

        // Check if this is an empty folder
        const isFolder = struct[key] !== null;
        const isEmpty = isFolder && Object.keys(struct[key]).length === 0;

        if (isEmpty) {
            result += prefix + connector + key + '/ (kosong)\n';
        } else if (isFolder) {
            result += prefix + connector + key + '/\n';
            result += printStructure(struct[key], newPrefix, newPath);
        } else {
            result += prefix + connector + key + '\n';
        }
    });

    return result;
}

function addFileContents() {
    // Sort files by path
    fileList.sort();

    for (const filePath of fileList) {
        try {
            const relativePath = path.relative(
                path.join(__dirname, TARGET_FOLDER), 
                filePath
            ).replace(/\\/g, '/');
            
            const content = fs.readFileSync(filePath, 'utf-8');
            const lines = content.split('\n').length;

            output += '\n';
            output += '='.repeat(70) + '\n';
            output += `FILE: resources/${relativePath} (${lines} lines)\n`;
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

console.log('🚀 Memulai export folder RESOURCES...\n');

const resourcesPath = path.join(__dirname, TARGET_FOLDER);

if (!fs.existsSync(resourcesPath)) {
    console.error(`❌ Folder '${TARGET_FOLDER}' tidak ditemukan!`);
    process.exit(1);
}

// Collect all files and folders
collectAllFilesAndFolders(resourcesPath, resourcesPath);

console.log(`📁 Ditemukan ${fileList.length} file dalam ${folderList.size} folder`);
if (emptyFolders.length > 0) {
    console.log(`📂 Ditemukan ${emptyFolders.length} folder kosong\n`);
}

// Build output
output += '='.repeat(70) + '\n';
output += 'EXPORT FOLDER RESOURCES - NGEVENT.ID (LARAVEL)\n';
output += 'Export Date: ' + new Date().toISOString() + '\n';
output += '='.repeat(70) + '\n\n';

output += 'STRUKTUR LENGKAP FOLDER RESOURCES:\n';
output += '─'.repeat(50) + '\n';
output += 'resources/\n';
const structure = buildStructure();
output += printStructure(structure, '', '');
output += '\n';

// Add summary of empty folders
if (emptyFolders.length > 0) {
    output += '\n📂 FOLDER KOSONG:\n';
    output += '─'.repeat(50) + '\n';
    for (const folder of emptyFolders.sort()) {
        output += `   resources/${folder}/ (kosong)\n`;
    }
    output += '\n';
}

// Add all file contents
if (fileList.length > 0) {
    output += '\n';
    output += '='.repeat(70) + '\n';
    output += 'ISI FILE:\n';
    output += '='.repeat(70) + '\n';
    addFileContents();
}

// Write output
const outputFile = 'ngevent-resources-only.txt';
fs.writeFileSync(outputFile, output, 'utf-8');

const fileSizeKB = (fs.statSync(outputFile).size / 1024).toFixed(2);
const fileSizeMB = (fs.statSync(outputFile).size / 1024 / 1024).toFixed(2);

console.log('─'.repeat(50));
console.log('✅ EXPORT SELESAI!');
console.log('─'.repeat(50));
console.log(`📁 Total folder  : ${folderList.size}`);
console.log(`📂 Folder kosong : ${emptyFolders.length}`);
console.log(`📁 Total file    : ${fileCount}`);
console.log(`📝 Total baris   : ${totalLines.toLocaleString()}`);
console.log(`📄 Output file   : ${outputFile}`);
console.log(`📊 Ukuran        : ${fileSizeKB} KB (${fileSizeMB} MB)`);
console.log('─'.repeat(50));

// Show summary per subfolder
console.log('\n📊 RINGKASAN PER SUBFOLDER:');
console.log('─'.repeat(50));

const folderStats = {};
for (const filePath of fileList) {
    const relativePath = path.relative(resourcesPath, filePath).replace(/\\/g, '/');
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
    const filesText = `${stats.files} file${stats.files > 1 ? 's' : ''}`.padEnd(10);
    const linesText = `${stats.lines.toLocaleString()} baris`.padStart(12);
    console.log(`   resources/${folder.padEnd(20)} : ${filesText} ${linesText}`);
}

console.log('\n💡 TIP: File ini berisi SEMUA file di folder resources,');
console.log('   termasuk struktur folder dan penanda folder kosong.');
