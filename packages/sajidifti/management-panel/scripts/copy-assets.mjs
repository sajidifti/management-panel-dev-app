import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'
import postcss from 'postcss'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const pkgRoot = path.resolve(__dirname, '..')
const cssFile = path.resolve(pkgRoot, 'public/css/app.css')

// When used with --noop (postinstall), don't fail if files missing
const noop = process.argv.includes('--noop')

try {
  // Post-process CSS: duplicate prefers-color-scheme dark media rules into
  // class-based selectors so toggling `document.documentElement.classList`
  // works at runtime. This avoids relying on the user's system media.
  if (!fs.existsSync(cssFile)) {
    if (!noop) {
      console.warn('app.css not found, skipping post-processing')
    }
    process.exit(0)
  }

  const css = fs.readFileSync(cssFile, 'utf8')
  const root = postcss.parse(css)

  // Collect cloned rules and media rules to remove
  const clones = []
  const mediaRulesToRemove = []

  root.walkAtRules('media', (atRule) => {
    if (atRule.params && atRule.params.includes('prefers-color-scheme:dark')) {
      // Check if this media rule contains dark variant selectors
      let hasDarkVariants = false
      atRule.walkRules((rule) => {
        if (!rule.selector) return
        // Only process selectors that are the escaped dark-variant (e.g. .dark\:bg-...)
        if (rule.selector.includes('\\.dark\\:') || rule.selector.includes('.dark\\:')) {
          hasDarkVariants = true
          // For each selector (comma separated) create a cloned rule prefixed with `.dark `
          const selParts = rule.selector.split(',').map(s => s.trim())
          const newSelector = selParts.map(s => `.dark ${s}`).join(', ')
          const cloned = rule.clone({ selector: newSelector })
          clones.push(cloned)
        }
      })
      
      // Mark this media rule for removal if it had dark variants
      // (we'll remove it after walking to avoid modifying while iterating)
      if (hasDarkVariants) {
        mediaRulesToRemove.push(atRule)
      }
    }
  })

  // Remove the media query rules that we've duplicated
  mediaRulesToRemove.forEach(rule => rule.remove())

  if (clones.length) {
    clones.forEach(c => root.append(c))
    fs.writeFileSync(cssFile, root.toResult().css, 'utf8')
    console.log(`post-processed ${clones.length} dark media rules -> class-based duplicates (removed ${mediaRulesToRemove.length} media queries)`)
  } else {
    console.log('no dark media rules found to post-process')
  }
} catch (err) {
  if (!noop) {
    console.error('post-process error:', err.message)
    process.exit(1)
  }
}
