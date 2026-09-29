/**
 * scroll-reveal.js
 * Port dari hooks/useScrollReveal.ts — helper animasi scroll reveal reusable.
 * Versi React memakai refs per komponen; versi ini memakai data-attributes di Blade.
 */
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

/**
 * Staggered reveal pada child elements dengan class .reveal-child
 * di dalam container [data-stagger-reveal].
 * Setara useScrollReveal() versi React.
 */
export function initStaggerReveal() {
  document.querySelectorAll('[data-stagger-reveal]').forEach((container) => {
    const selector = container.dataset.revealTarget || '.reveal-child'
    const children = container.querySelectorAll(selector)
    if (!children.length) return

    gsap.set(children, { y: 40, opacity: 0 })

    gsap.to(children, {
      y: 0,
      opacity: 1,
      duration: 0.8,
      ease: 'power3.out',
      stagger: 0.1,
      scrollTrigger: {
        trigger: container,
        start: 'top 80%',
        toggleActions: 'play none none none',
      },
    })
  })
}

/**
 * Text lines (.text-line) animate dari bawah dengan wrapper overflow hidden.
 * Setara useTextReveal() versi React.
 */
export function initTextReveal() {
  document.querySelectorAll('[data-text-reveal]').forEach((container) => {
    const lines = container.querySelectorAll('.text-line')
    if (!lines.length) return

    gsap.set(lines, { yPercent: 110 })

    gsap.to(lines, {
      yPercent: 0,
      duration: 1.4,
      ease: 'power4.out',
      stagger: 0.08,
      scrollTrigger: {
        trigger: container,
        start: 'top 85%',
        toggleActions: 'play none none none',
      },
    })
  })
}

/**
 * Parallax — elemen bergerak dengan kecepatan berbeda dari scroll.
 * Setara useParallax() versi React (dipakai hero bg).
 */
export function initParallax() {
  document.querySelectorAll('[data-parallax-bg]').forEach((el) => {
    gsap.to(el, {
      yPercent: 30,
      ease: 'none',
      scrollTrigger: {
        trigger: el.closest('section') || el,
        start: 'top bottom',
        end: 'bottom top',
        scrub: 1,
      },
    })
  })
}

/**
 * Clip-path reveal untuk images.
 * Setara useClipReveal() versi React (dipakai About image).
 */
export function initClipReveal() {
  document.querySelectorAll('[data-clip-reveal]').forEach((el) => {
    const img = el.querySelector('img')
    if (!img) return

    gsap.set(el, { clipPath: 'inset(100% 0% 0% 0%)' })
    gsap.set(img, { scale: 1.3 })

    gsap.to(el, {
      clipPath: 'inset(0% 0% 0% 0%)',
      duration: 1.6,
      ease: 'power4.inOut',
      scrollTrigger: {
        trigger: el,
        start: 'top 80%',
        toggleActions: 'play none none none',
      },
    })

    gsap.to(img, {
      scale: 1,
      duration: 1.8,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 80%',
        toggleActions: 'play none none none',
      },
    })
  })
}

/**
 * Clip-path reveal untuk semua gallery item (dipakai Gallery.tsx).
 */
export function initGalleryReveal() {
  const grid = document.querySelector('[data-gallery-grid]')
  if (!grid) return

  grid.querySelectorAll('.gallery-item').forEach((item, i) => {
    const img = item.querySelector('img')

    gsap.set(item, { clipPath: 'inset(100% 0% 0% 0%)' })
    if (img) gsap.set(img, { scale: 1.3 })

    gsap.to(item, {
      clipPath: 'inset(0% 0% 0% 0%)',
      duration: 1.2,
      ease: 'power4.inOut',
      delay: i * 0.08,
      scrollTrigger: { trigger: item, start: 'top 60%' },
    })

    if (img) {
      gsap.to(img, {
        scale: 1,
        duration: 1.5,
        ease: 'power3.out',
        delay: i * 0.08,
        scrollTrigger: { trigger: item, start: 'top 60%' },
      })
    }
  })
}
