import fs from 'fs'
import path from 'path'
import postcss from 'postcss'

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
    const srcPath = path.resolve(cssDir, cssSrc)
    fs.copyFileSync(srcPath, dest)
    console.log('copied', cssSrc, '->', 'public/css/app.css')

    // Post-process: duplicate prefers-color-scheme dark media rules into
    // class-based selectors so toggling `document.documentElement.classList`
    // works at runtime. This avoids relying on the user's system media.
    try {
      const css = fs.readFileSync(dest, 'utf8')
      const root = postcss.parse(css)

      // Collect cloned rules to append after parsing to avoid modifying while walking
      const clones = []

      root.walkAtRules('media', (atRule) => {
        if (atRule.params && atRule.params.includes('prefers-color-scheme:dark')) {
          atRule.walkRules((rule) => {
            if (!rule.selector) return
            // Only process selectors that are the escaped dark-variant (e.g. .dark\:bg-...)
            if (rule.selector.includes('\\.dark\\:') || rule.selector.includes('.dark\\:')) {
              // For each selector (comma separated) create a cloned rule prefixed with `.dark `
              const selParts = rule.selector.split(',').map(s => s.trim())
              const newSelector = selParts.map(s => `.dark ${s}`).join(', ')
              const cloned = rule.clone({ selector: newSelector })
              clones.push(cloned)
            }
          })
        }
      })

      if (clones.length) {
        clones.forEach(c => root.append(c))
        fs.writeFileSync(dest, root.toResult().css, 'utf8')
        console.log('post-processed dark media rules -> class-based duplicates')
      }
    } catch (e) {
      console.warn('failed to post-process app.css for dark rules:', e.message)
    }
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
