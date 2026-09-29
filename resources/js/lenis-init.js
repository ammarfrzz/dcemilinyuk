/**
 * lenis-init.js
 * Port dari hooks/useLenis.ts — inisialisasi Lenis + sync dengan GSAP ScrollTrigger.
 */
import Lenis from 'lenis'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

/**
 * Inisialisasi Lenis smooth scroll dan sinkronkan dengan GSAP ticker.
 * Identik dengan konfigurasi useLenis() versi React.
 */
export function initLenis() {
  const lenis = new Lenis({
    duration: 1.4,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    touchMultiplier: 1.5,
    smoothWheel: true,
    wheelMultiplier: 0.8,
  })

  lenis.on('scroll', ScrollTrigger.update)

  gsap.ticker.add((time) => {
    lenis.raf(time * 1000)
  })

  gsap.ticker.lagSmoothing(0)

  return lenis
}

/**
 * Smooth scroll ke elemen berdasarkan id (dipakai navbar & tombol hero).
 * Menggunakan lenis.scrollTo bila tersedia, fallback scrollIntoView.
 */
export function scrollToSection(lenis, id) {
  const target = document.getElementById(id)
  if (!target) return

  if (lenis) {
    lenis.scrollTo(target, { offset: -64 }) // offset tinggi navbar
  } else {
    target.scrollIntoView({ behavior: 'smooth' })
  }
}
