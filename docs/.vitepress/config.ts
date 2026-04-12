import { defineConfig } from 'vitepress'
import { readFileSync } from 'fs'
import { dirname, resolve } from 'path'
import { fileURLToPath } from 'url'

const __dirname = dirname(fileURLToPath(import.meta.url))

// Load custom Djot grammar for syntax highlighting
const djotGrammar = JSON.parse(
  readFileSync(resolve(__dirname, 'grammars/djot.tmLanguage.json'), 'utf-8')
)

export default defineConfig({
  title: 'Symfony Djot',
  description: 'Djot markup language integration for Symfony — Twig filters, services, forms, and validation',

  base: '/symfony-djot/',

  head: [
    ['link', { rel: 'icon', href: '/symfony-djot/favicon.svg', type: 'image/svg+xml' }],
  ],

  markdown: {
    languages: [
      {
        ...djotGrammar,
        name: 'djot',
        aliases: ['dj', 'Djot'],
      },
    ],
  },

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Guide', link: '/guide/', activeMatch: '/guide/' },
      { text: 'Extensions', link: '/extensions/', activeMatch: '/extensions/' },
      {
        text: 'Links',
        items: [
          { text: 'Playground', link: 'https://sandbox.dereuromark.de/sandbox/djot' },
          { text: 'Demo App', link: 'https://github.com/php-collective/symfony-djot-demo' },
          { text: 'djot-php', link: 'https://php-collective.github.io/djot-php/' },
          { text: 'Laravel Package', link: 'https://php-collective.github.io/laravel-djot/' },
          { text: 'Djot Spec', link: 'https://djot.net/' },
          { text: 'Changelog', link: 'https://github.com/php-collective/symfony-djot/releases' },
          { text: 'Packagist', link: 'https://packagist.org/packages/php-collective/symfony-djot' },
          { text: 'Issues', link: 'https://github.com/php-collective/symfony-djot/issues' },
        ],
      },
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Introduction',
          items: [
            { text: 'Getting Started', link: '/guide/' },
            { text: 'Installation', link: '/guide/installation' },
            { text: 'Configuration', link: '/guide/configuration' },
          ],
        },
        {
          text: 'Usage',
          items: [
            { text: 'Twig Usage', link: '/guide/twig-usage' },
            { text: 'Service Usage', link: '/guide/service-usage' },
            { text: 'Forms', link: '/guide/forms' },
            { text: 'Validation', link: '/guide/validation' },
          ],
        },
        {
          text: 'Advanced',
          items: [
            { text: 'Safe Mode', link: '/guide/safe-mode' },
            { text: 'Caching', link: '/guide/caching' },
            { text: 'Djot Syntax', link: '/guide/djot-syntax' },
          ],
        },
      ],
      '/extensions/': [
        {
          text: 'Extensions',
          link: '/extensions/',
        },
      ],
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/php-collective/symfony-djot' },
    ],

    search: {
      provider: 'local',
    },

    editLink: {
      pattern: 'https://github.com/php-collective/symfony-djot/edit/master/docs/:path',
      text: 'Edit this page on GitHub',
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright PHP Collective',
    },
  },
})
