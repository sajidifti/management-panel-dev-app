import fs from 'fs'
import path from 'path'

const pkgRoot = path.resolve(new URL(import.meta.url).pathname.replace(/^\//, ''), '..', '..')
const publicDir = path.resolve(pkgRoot, 'public')
const cssDir = path.resolve(publicDir, 'css')
const jsDir = path.resolve(publicDir, 'js')

function findFile(dir, namePrefix, ext) {
  if (!fs.existsSync(dir)) return null
  const files = fs.readdirSync(dir).filter(f => f.startsWith(namePrefix) && f.endsWith(ext))
  if (files.length === 0) return null
  // prefer non-hashed exact match, else first hashed
  const exact = files.find(f => f === `${namePrefix}${ext}`)
  return exact || files[0]
}

function copyIfExists(src, dest) {
  if (!src) return false
  const srcPath = path.resolve(cssDir, src)
  if (!fs.existsSync(srcPath)) return false
  fs.copyFileSync(srcPath, dest)
  return true
}

// When used with --noop (postinstall), don't fail if files missing
const noop = process.argv.includes('--noop')

try {
  // CSS
  const cssSrc = findFile(cssDir, 'app', '.css') || findFile(cssDir, 'style', '.css')
  if (cssSrc) {
    const dest = path.resolve(cssDir, 'app.css')
    fs.copyFileSync(path.resolve(cssDir, cssSrc), dest)
    console.log('copied', cssSrc, '->', 'public/css/app.css')
  } else if (!noop) {
    console.warn('No CSS build files found to copy')
  }

  // JS
  const jsSrc = findFile(jsDir, 'app', '.js')
  if (jsSrc) {
    const dest = path.resolve(jsDir, 'app.js')
    fs.copyFileSync(path.resolve(jsDir, jsSrc), dest)
    console.log('copied', jsSrc, '->', 'public/js/app.js')
  } else if (!noop) {
    console.warn('No JS build files found to copy')
  }
} catch (err) {
  if (!noop) {
    console.error('copy-assets error', err)
    process.exit(1)
  }
}
