// export-app-database-only.mjs
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ============================================
// KONFIGURASI KHUSUS APP & DATABASE
// ============================================

const TARGET_FOLDERS = ['app', 'database']; // GANTI dari 'resources' ke 'app' dan 'database'

// File yang tetap di-exclude
const EXCLUDE_FILES = [
    '.DS_Store',
    'Thumbs.db',
];

// Ekstensi yang ditampilkan (lebih lengkap untuk PHP files)
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
    '.sql',
    '.md',
    '.txt',
    '.yml',
    '.yaml',
    '.xml',
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
        // Get relative path from project root
        let relativePath = filePath;
        for (const targetFolder of TARGET_FOLDERS) {
            const targetPath = path.join(__dirname, targetFolder);
            if (filePath.startsWith(targetPath)) {
                relativePath = path.relative(targetPath, filePath).replace(/\\/g, '/');
                break;
            }
        }
        
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

function getRelativePath(filePath) {
    // Determine which target folder this file belongs to
    for (const targetFolder of TARGET_FOLDERS) {
        const targetPath = path.join(__dirname, targetFolder);
        if (filePath.startsWith(targetPath)) {
            return targetFolder + '/' + path.relative(targetPath, filePath).replace(/\\/g, '/');
        }
    }
    return path.relative(__dirname, filePath).replace(/\\/g, '/');
}

function addFileContents() {
    // Sort files by path
    fileList.sort();

    for (const filePath of fileList) {
        try {
            const relativePath = getRelativePath(filePath);
            
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

console.log('🚀 Memulai export folder APP & DATABASE...\n');

// Check if folders exist
let foundFolders = [];
for (const folder of TARGET_FOLDERS) {
    const folderPath = path.join(__dirname, folder);
    if (fs.existsSync(folderPath)) {
        foundFolders.push(folder);
        console.log(`✓ Folder '${folder}' ditemukan`);
    } else {
        console.log(`✗ Folder '${folder}' tidak ditemukan`);
    }
}

if (foundFolders.length === 0) {
    console.error(`\n❌ Tidak ada folder yang ditemukan!`);
    console.error(`   Pastikan folder 'app' dan 'database' ada di directory ini.`);
    process.exit(1);
}

console.log('');

// Collect all files and folders from each target folder
for (const folder of foundFolders) {
    const folderPath = path.join(__dirname, folder);
    collectAllFilesAndFolders(folderPath, folderPath);
}

console.log(`📁 Ditemukan ${fileList.length} file dalam ${folderList.size} folder`);
if (emptyFolders.length > 0) {
    console.log(`📂 Ditemukan ${emptyFolders.length} folder kosong\n`);
}

// Build output
output += '='.repeat(70) + '\n';
output += 'EXPORT FOLDER APP & DATABASE - NGEVENT.ID (LARAVEL)\n';
output += 'Export Date: ' + new Date().toISOString() + '\n';
output += '='.repeat(70) + '\n\n';

output += 'STRUKTUR LENGKAP FOLDER APP & DATABASE:\n';
output += '─'.repeat(50) + '\n';

// Print structure for each folder
for (const folder of foundFolders) {
    output += `${folder}/\n`;
    
    // Build structure for this specific folder
    const folderPath = path.join(__dirname, folder);
    const folderStructure = {};
    
    for (const filePath of fileList) {
        if (filePath.startsWith(folderPath)) {
            const relativePath = path.relative(folderPath, filePath).replace(/\\/g, '/');
            const parts = relativePath.split('/');
            
            let current = folderStructure;
            for (let i = 0; i < parts.length - 1; i++) {
                if (!current[parts[i]]) {
                    current[parts[i]] = {};
                }
                current = current[parts[i]];
            }
            current[parts[parts.length - 1]] = null;
        }
    }
    
    // Add empty folders
    for (const emptyFolder of emptyFolders) {
        // Check if this empty folder belongs to current target folder
        const fullEmptyPath = path.join(folderPath, emptyFolder);
        if (fs.existsSync(fullEmptyPath)) {
            const parts = emptyFolder.split('/');
            let current = folderStructure;
            for (const part of parts) {
                if (!current[part]) {
                    current[part] = {};
                }
                current = current[part];
            }
        }
    }
    
    output += printStructure(folderStructure, '', '');
    output += '\n';
}

// Add summary of empty folders
if (emptyFolders.length > 0) {
    output += '\n📂 FOLDER KOSONG:\n';
    output += '─'.repeat(50) + '\n';
    for (const folder of emptyFolders.sort()) {
        // Determine which target folder this belongs to
        let targetFolder = '';
        for (const tf of foundFolders) {
            const tfPath = path.join(__dirname, tf);
            const fullPath = path.join(tfPath, folder);
            if (fs.existsSync(fullPath)) {
                targetFolder = tf;
                break;
            }
        }
        output += `   ${targetFolder}/${folder}/ (kosong)\n`;
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
const outputFile = 'ngevent-app-database-only.txt';
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
    const relativePath = getRelativePath(filePath);
    const topFolder = relativePath.split('/').slice(0, 2).join('/'); // e.g., "app/Http"
    
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
    console.log(`   ${folder.padEnd(30)} : ${filesText} ${linesText}`);
}

console.log('\n💡 TIP: File ini berisi SEMUA file di folder app & database,');
console.log('   termasuk struktur folder dan penanda folder kosong.');
