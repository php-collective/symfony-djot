---
layout: home

hero:
  name: Symfony Djot
  text: Djot for Symfony
  tagline: Twig filters, services, forms, and validation for the Djot markup language
  image:
    src: /logo.svg
    alt: Symfony Djot
  actions:
    - theme: brand
      text: Get Started
      link: /guide/
    - theme: alt
      text: Extensions
      link: /extensions/
    - theme: alt
      text: View on GitHub
      link: https://github.com/php-collective/symfony-djot

features:
  - icon: "\uD83C\uDF3F"
    title: Twig Integration
    details: "|djot filter and djot() function \u2014 render Djot markup directly in your Twig templates"
  - icon: "\uD83D\uDD12"
    title: Safe Mode
    details: Built-in XSS protection enabled by default for untrusted user input
  - icon: "\uD83C\uDFAD"
    title: Multiple Profiles
    details: Different converter configurations for different contexts (user content, admin, CMS)
  - icon: "\uD83E\uDDE9"
    title: Extensible
    details: 17 built-in extensions \u2014 autolink, mentions, TOC, heading permalinks, and more
  - icon: "\uD83D\uDCDD"
    title: Forms & Validation
    details: DjotType form field and ValidDjot constraint for form integration and input validation
  - icon: "\u26A1"
    title: Caching
    details: Optional output caching via Symfony cache pools for better performance
---
