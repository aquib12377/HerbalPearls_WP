/**
 * Herbal Pearls — Tailwind configuration.
 *
 * Token strategy: every color references the CSS variables in
 * assets/css/tokens.css via var(--hp-*). That keeps the existing
 * tokens.css as the single source of truth and lets the theme
 * toggle colors at runtime without rebuilding.
 *
 * Spacing, radius, fonts, shadows mirror the existing components.
 *
 * Migration: utilities load BEFORE legacy .hp-* CSS (see inc/enqueue.php),
 * so .hp-* rules can still override during the page-by-page migration.
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		'./**/*.php',
		'./assets/js/**/*.js',
		'!./node_modules/**',
		'!./vendor/**',
	],
	theme: {
		container: {
			center: true,
			padding: {
				DEFAULT: '1rem',
				md: '2rem',
			},
			screens: {
				'2xl': '1280px',
			},
		},
		screens: {
			xs: '480px',
			sm: '600px',
			md: '768px',
			lg: '900px',
			xl: '1024px',
			'2xl': '1280px',
		},
		extend: {
			colors: {
				hp: {
					'bg-page':   'var(--hp-bg-page)',
					'bg-1':      'var(--hp-bg-1)',
					'bg-2':      'var(--hp-bg-2)',
					'bg-3':      'var(--hp-bg-3)',
					'bg-accent': 'var(--hp-bg-accent)',
					'bg-pure':   'var(--hp-bg-pure)',
					'line-1':    'var(--hp-line-1)',
					'line-2':    'var(--hp-line-2)',
					'line-3':    'var(--hp-line-3)',
					'gold-100':  'var(--hp-gold-100)',
					'gold-300':  'var(--hp-gold-300)',
					'gold-500':  'var(--hp-gold-500)',
					'gold-600':  'var(--hp-gold-600)',
					'gold-700':  'var(--hp-gold-700)',
					'brown-100': 'var(--hp-brown-100)',
					'brown-300': 'var(--hp-brown-300)',
					'brown-500': 'var(--hp-brown-500)',
					'brown-700': 'var(--hp-brown-700)',
					'brown-900': 'var(--hp-brown-900)',
					'peach-300': 'var(--hp-peach-300)',
					'peach-500': 'var(--hp-peach-500)',
					'blush':     'var(--hp-blush)',
					'coral':     'var(--hp-coral)',
					'green-logo': 'var(--hp-green-logo)',
					'green-leaf': 'var(--hp-green-leaf)',
					'sage-light': 'var(--hp-sage-light)',
					'mint':      'var(--hp-mint)',
					'text-1':    'var(--hp-text-1)',
					'text-2':    'var(--hp-text-2)',
					'text-3':    'var(--hp-text-3)',
					'text-4':    'var(--hp-text-4)',
					'on-gold':   'var(--hp-text-on-gold)',
					'success':   'var(--hp-success)',
					'warn':      'var(--hp-warn)',
					'error':     'var(--hp-error)',
					'sale':      'var(--hp-sale)',
				},
			},
			fontFamily: {
				serif: ['"Cormorant Garamond"', 'Georgia', '"Times New Roman"', 'serif'],
				sans: ['system-ui', '-apple-system', '"Segoe UI"', 'sans-serif'],
				script: ['Allura', '"Brush Script MT"', 'cursive'],
			},
			fontSize: {
				'fluid-h1':   ['clamp(1.75rem, 4vw, 3rem)',     { lineHeight: '1.15' }],
				'fluid-h2':   ['clamp(1.375rem, 3vw, 2.25rem)', { lineHeight: '1.2'  }],
				'fluid-h3':   ['clamp(1.125rem, 2vw, 1.5rem)',  { lineHeight: '1.25' }],
				'fluid-lead': ['clamp(1rem, 2vw, 1.25rem)',     { lineHeight: '1.55' }],
				'fluid-script': ['clamp(1.5rem, 3vw, 2.25rem)', { lineHeight: '1.3'  }],
				'fluid-accent': ['clamp(1.25rem, 2.5vw, 1.75rem)', { lineHeight: '1.2' }],
				'fluid-title':  ['clamp(1.75rem, 4vw, 2.75rem)', { lineHeight: '1.15' }],
			},
			spacing: {
				'section': 'clamp(3rem, 8vw, 5rem)',
				'section-sm': 'clamp(1.5rem, 4vw, 2.5rem)',
			},
			borderRadius: {
				'xs':  '4px',
				'sm':  '6px',
				'md':  '8px',
				'lg':  '12px',
				'xl':  '14px',
				'2xl': '16px',
				'pill': '100px',
			},
			boxShadow: {
				'hp-sm': 'var(--hp-shadow-sm)',
				'hp-md': 'var(--hp-shadow-md)',
				'hp-lg': 'var(--hp-shadow-lg)',
				'hp-focus': '0 0 0 3px rgba(160, 121, 63, 0.15)',
			},
			maxWidth: {
				'prose-hp': '720px',
				'container-hp': '1280px',
			},
			aspectRatio: {
				'hero': '16 / 6',
				'card': '4 / 3',
			},
		},
	},
	plugins: [
		require('@tailwindcss/typography'),
	],
	// Preserve the legacy .hp-* CSS while we migrate page-by-page.
	corePlugins: {
		preflight: false,
	},
};
