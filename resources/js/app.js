/**
 * app.js — Entry point DcemilinYuk (Laravel port)
 * Import semua modul JS dan jalankan semua animasi section,
 * setara komposisi App.tsx + tiap komponen versi React.
 */
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

import { initLenis, scrollToSection } from './lenis-init'
import {
  initStaggerReveal,
  initTextReveal,
  initParallax,
  initClipReveal,
  initGalleryReveal,
} from './scroll-reveal'
import { initOrderModal } from './order-modal'

gsap.registerPlugin(ScrollTrigger)

/* ============================================
   Preloader — counter 000-100 + curtain reveal
   Hanya dijalankan pada kunjungan pertama customer di sesi ini
   ============================================ */
function initPreloader(onComplete) {
  const container = document.getElementById('preloader')
  const counterEl = document.getElementById('preloader-counter')
  if (!container || !counterEl) {
    onComplete()
    return
  }

  // Jika customer sudah pernah melihat loading screen di sesi ini (misal kembali dari checkout)
  if (sessionStorage.getItem('dc_has_seen_preloader')) {
    container.style.display = 'none'
    onComplete()
    return
  }

  const duration = 2500
  const steps = 100
  const interval = duration / steps
  let current = 0
  let hasCompleted = false

  const finish = () => {
    if (hasCompleted) return
    hasCompleted = true
    try {
      sessionStorage.setItem('dc_has_seen_preloader', 'true')
    } catch (e) {
      // Ignore sessionStorage quota / private browsing errors
    }
    setTimeout(() => {
      gsap.to(container, {
        yPercent: -100,
        duration: 0.8,
        ease: 'power4.inOut',
        onComplete,
      })
    }, 300)
  }

  const timer = setInterval(() => {
    current++
    counterEl.textContent = String(current).padStart(3, '0')
    if (current >= steps) {
      clearInterval(timer)
      finish()
    }
  }, interval)

  // Safety: jangan pernah biarkan preloader menggantung
  setTimeout(finish, duration + 1000)
}

/* ============================================
   Navigation — scrolled state, sliding active indicator,
   mobile menu, smooth scroll & real-time scrollspy
   ============================================ */
function initNavigation(lenis) {
  const navbar = document.getElementById('navbar')
  const toggle = document.getElementById('mobile-toggle')
  const mobileMenu = document.getElementById('mobile-menu')
  const navLinksContainer = document.getElementById('nav-links')
  const indicator = document.getElementById('nav-indicator')
  const desktopLinks = document.querySelectorAll('.nav-links a[data-section]')
  const mobileLinks = document.querySelectorAll('.mobile-menu a[data-section]')

  // Scroll blur effect
  const updateScrolledState = () => {
    if (navbar) {
      navbar.classList.toggle('navbar--scrolled', window.scrollY > 20)
    }
  }
  window.addEventListener('scroll', updateScrolledState, { passive: true })
  updateScrolledState()

  // Sliding indicator positioning
  function moveIndicatorToLink(link) {
    if (!indicator || !navLinksContainer) return
    if (!link) {
      indicator.style.opacity = '0'
      return
    }
    const containerRect = navLinksContainer.getBoundingClientRect()
    const linkRect = link.getBoundingClientRect()
    const left = linkRect.left - containerRect.left
    const width = linkRect.width

    indicator.style.opacity = '1'
    indicator.style.transform = `translateX(${left}px)`
    indicator.style.width = `${width}px`
  }

  // Active section mapping (ordered from top to bottom)
  const sectionMap = [
    { id: 'hero', nav: 'hero' },
    { id: 'about', nav: 'about' },
    { id: 'peel', nav: 'peel' },
    { id: 'howto', nav: 'howto' },
    { id: 'categories', nav: 'categories' },
    { id: 'featured', nav: 'products' }, // Makanan terlaris -> Produk
    { id: 'products', nav: 'products' },
    { id: 'gallery', nav: 'gallery' },
    { id: 'testimonials', nav: 'gallery' }, // Testimoni -> Galeri
    { id: 'contact', nav: 'contact' },
  ]

  let currentActiveNav = null
  let isHovering = false

  function setActiveNav(navId, forceMove = false) {
    if (currentActiveNav === navId && !forceMove) return
    currentActiveNav = navId

    let activeEl = null
    desktopLinks.forEach((link) => {
      const match = link.dataset.section === navId
      link.classList.toggle('active', match)
      if (match) activeEl = link
    })

    mobileLinks.forEach((link) => {
      link.classList.toggle('active', link.dataset.section === navId)
    })

    if (!isHovering && activeEl) {
      moveIndicatorToLink(activeEl)
    }
  }

  // Real-time scroll spy calculation
  function calculateActiveSection() {
    const scrollY = window.scrollY
    const viewportHeight = window.innerHeight
    const docHeight = document.documentElement.scrollHeight

    // 1. Bottom of page -> contact
    if (scrollY + viewportHeight >= docHeight - 80) {
      return 'contact'
    }

    // 2. Very top of page -> hero
    if (scrollY < 120) {
      return 'hero'
    }

    // 3. Scan sections from bottom to top against the trigger point (35% from viewport top)
    const triggerY = viewportHeight * 0.35

    for (let i = sectionMap.length - 1; i >= 0; i--) {
      const item = sectionMap[i]
      const el = document.getElementById(item.id)
      if (!el) continue

      const rect = el.getBoundingClientRect()
      // Section is active if its top has reached or passed trigger line,
      // and its bottom hasn't scrolled completely past the navbar
      if (rect.top <= triggerY && rect.bottom >= 70) {
        return item.nav
      }
    }

    return 'hero'
  }

  let ticking = false
  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const activeNav = calculateActiveSection()
        setActiveNav(activeNav)
        ticking = false
      })
      ticking = true
    }
  }

  // Hook scroll events (both native and Lenis)
  window.addEventListener('scroll', onScroll, { passive: true })
  if (lenis) {
    lenis.on('scroll', onScroll)
  }

  // Hover animations for desktop navbar
  desktopLinks.forEach((link) => {
    link.addEventListener('mouseenter', () => {
      isHovering = true
      moveIndicatorToLink(link)
    })
  })

  if (navLinksContainer) {
    navLinksContainer.addEventListener('mouseleave', () => {
      isHovering = false
      const activeLink = navLinksContainer.querySelector('a.active')
      if (activeLink) {
        moveIndicatorToLink(activeLink)
      } else {
        indicator.style.opacity = '0'
      }
    })
  }

  // Window resize: recompute indicator position
  window.addEventListener('resize', () => {
    const activeLink = navLinksContainer?.querySelector('a.active')
    if (activeLink) moveIndicatorToLink(activeLink)
  })

  // Mobile menu toggle
  if (toggle && mobileMenu) {
    toggle.addEventListener('click', () => {
      toggle.classList.toggle('active')
      mobileMenu.classList.toggle('active')
    })
  }

  // Smooth scroll untuk semua link [data-scroll-to]
  document.querySelectorAll('[data-scroll-to]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault()
      if (toggle && mobileMenu) {
        toggle.classList.remove('active')
        mobileMenu.classList.remove('active')
      }

      const targetId = el.dataset.scrollTo
      const matched = sectionMap.find((item) => item.id === targetId)
      if (matched) {
        setActiveNav(matched.nav, true)
      }

      scrollToSection(lenis, targetId)
    })
  })

  // Initial call
  setTimeout(() => {
    const initialNav = calculateActiveSection()
    setActiveNav(initialNav, true)
  }, 150)
}

/* ============================================
   Hero — word-by-word reveal (port Hero.tsx)
   ============================================ */
function initHero() {
  const title = document.getElementById('hero-title')
  const content = document.getElementById('hero-content')
  if (!title || !content) return

  const words = title.querySelectorAll('.hero-word')
  if (words.length) {
    gsap.set(words, { y: 40, opacity: 0 })
    gsap.to(words, {
      y: 0,
      opacity: 1,
      duration: 0.8,
      ease: 'power3.out',
      stagger: 0.05,
      delay: 2.8,
    })
  }

  gsap.fromTo(
    content,
    { opacity: 0, y: 30 },
    { opacity: 1, y: 0, duration: 1, ease: 'power3.out', delay: 3.2 }
  )
}

/* ============================================
   Products — filter tab kategori (port Products.tsx)
   ============================================ */
function initProductFilter() {
  const tabs = document.getElementById('product-tabs')
  const grid = document.getElementById('products-grid')
  if (!tabs || !grid) return

  // Reveal pertama kali saat section masuk viewport
  ScrollTrigger.create({
    trigger: grid,
    start: 'top 85%',
    once: true,
    onEnter: () => {
      const cards = grid.querySelectorAll('.product-card')
      gsap.fromTo(
        cards,
        { y: 30, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.6, ease: 'power3.out', stagger: 0.05 }
      )
    },
  })

  tabs.querySelectorAll('.product-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
      const filter = tab.dataset.filter

      tabs.querySelectorAll('.product-tab').forEach((t) => t.classList.remove('product-tab--active'))
      tab.classList.add('product-tab--active')

      const cards = grid.querySelectorAll('.product-card')
      cards.forEach((card) => {
        const show = filter === 'all' || card.dataset.category === filter
        card.style.display = show ? '' : 'none'
      })

      gsap.fromTo(
        grid.querySelectorAll('.product-card:not([style*="none"])'),
        { y: 20, opacity: 0 },
        { y: 0, opacity: 1, duration: 0.4, ease: 'power3.out', stagger: 0.03 }
      )
    })
  })
}

/* ============================================
   PeelReveal — horizontal bars peel away
   (port PeelReveal.tsx)
   ============================================ */
function initPeelReveal() {
  const section = document.getElementById('peel')
  const content = document.getElementById('peel-content')
  const barsContainer = document.getElementById('peel-bars')
  if (!section || !content || !barsContainer) return

  const BAR_COUNT = 6
  for (let i = 0; i < BAR_COUNT; i++) {
    const bar = document.createElement('div')
    bar.className = 'peel__bar'
    bar.style.height = `${100 / BAR_COUNT}%`
    bar.style.top = `${(i / BAR_COUNT) * 100}%`
    barsContainer.appendChild(bar)
  }

  const bars = barsContainer.querySelectorAll('.peel__bar')
  const halfIndex = BAR_COUNT / 2

  gsap.set(content, { opacity: 0, scale: 1.1 })

  const tl = gsap.timeline({
    scrollTrigger: {
      trigger: section,
      start: 'top 60%',
      toggleActions: 'play none none none',
    },
  })

  for (let i = 0; i < halfIndex; i++) {
    tl.to(bars[i], { yPercent: -200, duration: 0.8, ease: 'power3.inOut' }, i * 0.05)
  }
  for (let i = halfIndex; i < BAR_COUNT; i++) {
    tl.to(bars[i], { yPercent: 200, duration: 0.8, ease: 'power3.inOut' }, (BAR_COUNT - 1 - i) * 0.05)
  }

  tl.to(content, { opacity: 1, scale: 1, duration: 0.8, ease: 'power3.out' }, 0.3)
}

/* ============================================
   Featured — hero card + items slide-in
   (port Featured.tsx)
   ============================================ */
function initFeatured() {
  const layout = document.querySelector('[data-featured-reveal]')
  if (!layout) return

  const hero = layout.querySelector('.featured-hero')
  const items = layout.querySelectorAll('.featured-item')

  if (hero) {
    gsap.set(hero, { x: -60, opacity: 0 })
    gsap.to(hero, {
      x: 0,
      opacity: 1,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: { trigger: layout, start: 'top 75%' },
    })
  }

  if (items.length) {
    gsap.set(items, { x: 60, opacity: 0 })
    gsap.to(items, {
      x: 0,
      opacity: 1,
      duration: 0.8,
      ease: 'power3.out',
      stagger: 0.15,
      scrollTrigger: { trigger: layout, start: 'top 75%' },
    })
  }
}

/* ============================================
   SpecialtyDrinks — text scatter on hover
   (port SpecialtyDrinks.tsx)
   ============================================ */
function initSpecialty() {
  const list = document.querySelector('[data-specialty-list]')
  if (!list) return
  const items = list.querySelectorAll('.specialty-item')

  // Scroll reveal staggered
  gsap.fromTo(
    items,
    { y: 40, opacity: 0 },
    {
      y: 0,
      opacity: 1,
      duration: 0.7,
      stagger: 0.1,
      ease: 'power3.out',
      scrollTrigger: { trigger: list, start: 'top 85%' },
    }
  )

  // Scatter characters on hover
  items.forEach((item) => {
    const nameEl = item.querySelector('.specialty-item__name')
    const bgEl = item.querySelector('.specialty-item__bg')
    const descEl = item.querySelector('.specialty-item__desc')
    if (!nameEl || !bgEl || !descEl) return

    const text = nameEl.textContent || ''
    nameEl.innerHTML = ''
    ;[...text].forEach((char) => {
      const span = document.createElement('span')
      span.className = 'specialty-char'
      span.textContent = char === ' ' ? '\u00A0' : char
      nameEl.appendChild(span)
    })

    const chars = nameEl.querySelectorAll('.specialty-char')

    item.addEventListener('mouseenter', () => {
      gsap.to(bgEl, { opacity: 0.3, duration: 0.4 })
      gsap.to(chars, {
        x: () => gsap.utils.random(-30, 30),
        y: () => gsap.utils.random(-20, 20),
        rotation: () => gsap.utils.random(-15, 15),
        duration: 0.4,
        ease: 'power2.out',
        stagger: 0.01,
      })
      gsap.to(descEl, { opacity: 1, y: 0, duration: 0.4, delay: 0.1 })
    })

    item.addEventListener('mouseleave', () => {
      gsap.to(bgEl, { opacity: 0, duration: 0.3 })
      gsap.to(chars, { x: 0, y: 0, rotation: 0, duration: 0.5, ease: 'power3.out', stagger: 0.01 })
      gsap.to(descEl, { opacity: 0, y: 10, duration: 0.3 })
    })
  })
}

/* ============================================
   HorizontalScroll — GSAP pin + scrub
   (port HorizontalScroll.tsx)
   ============================================ */
function initHorizontalScroll() {
  const container = document.querySelector('[data-hscroll-container]')
  const strip = document.querySelector('[data-hscroll-strip]')
  if (!container || !strip) return

  // Nonaktifkan animasi horizontal di mobile
  if (window.innerWidth <= 768) {
    strip.querySelectorAll('.hscroll__item').forEach((item) => {
      gsap.set(item, { scale: 1, opacity: 1 })
      const label = item.querySelector('.hscroll__item-label')
      if (label) gsap.set(label, { opacity: 1, y: 0 })
    })
    return
  }

  const items = strip.querySelectorAll('.hscroll__item')
  const getTotalWidth = () => strip.scrollWidth - window.innerWidth

  const scrollTween = gsap.to(strip, {
    x: () => -getTotalWidth(),
    ease: 'none',
    scrollTrigger: {
      trigger: container,
      start: 'center center',
      end: () => `+=${getTotalWidth()}`,
      scrub: 1,
      pin: true,
      pinSpacing: true,
      anticipatePin: 1,
      invalidateOnRefresh: true,
    },
  })

  items.forEach((item, i) => {
    gsap.set(item, { scale: 0.9 })

    gsap.to(item, {
      scale: 1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: container,
        start: () => `left+=${(i / items.length) * getTotalWidth() - window.innerWidth * 0.3} center`,
        end: () => `left+=${(i / items.length) * getTotalWidth() + window.innerWidth * 0.3} center`,
        scrub: 1,
        containerAnimation: scrollTween,
      },
    })

    const label = item.querySelector('.hscroll__item-label')
    if (label) {
      gsap.fromTo(
        label,
        { opacity: 0, y: 15 },
        {
          opacity: 1,
          y: 0,
          duration: 0.5,
          scrollTrigger: {
            trigger: container,
            start: () => `left+=${(i / items.length) * getTotalWidth() - window.innerWidth * 0.2} center`,
            end: () => `left+=${(i / items.length) * getTotalWidth()} center`,
            scrub: 1,
            containerAnimation: scrollTween,
          },
        }
      )
    }
  })

  setTimeout(() => ScrollTrigger.refresh(), 800)
}

/* ============================================
   About — stat counter animation (port About.tsx)
   ============================================ */
function initAboutStats() {
  const stats = document.querySelector('[data-stats-counter]')
  if (!stats) return

  stats.querySelectorAll('.about-stat__number').forEach((el) => {
    const target = parseInt(el.getAttribute('data-target') || '0', 10)
    const obj = { val: 0 }

    gsap.to(obj, {
      val: target,
      duration: 2,
      ease: 'power2.out',
      snap: { val: 1 },
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
        toggleActions: 'play none none none',
      },
      onUpdate: () => {
        el.textContent = Math.round(obj.val).toString()
      },
    })
  })
}

/* ============================================
   Testimonials — auto-rotating quotes
   (port Testimonials.tsx)
   ============================================ */
function initTestimonials() {
  const root = document.querySelector('[data-testimonials]')
  if (!root) return

  const dataEl = document.getElementById('testimonials-json-data') || root.querySelector('[data-testimonials-data]')
  let testimonials = []
  try {
    testimonials = JSON.parse(dataEl?.textContent || '[]')
  } catch {
    testimonials = []
  }
  if (!testimonials.length) return

  const card = root.querySelector('[data-testimonial-card]')
  const starsEl = root.querySelector('[data-testimonial-stars]')
  const textEl = root.querySelector('[data-testimonial-text]')
  const avatarEl = root.querySelector('[data-testimonial-avatar]')
  const nameEl = root.querySelector('[data-testimonial-name]')
  const roleEl = root.querySelector('[data-testimonial-role]')
  const dots = root.querySelectorAll('[data-testimonial-index]')
  const prevBtn = document.getElementById('testimonial-prev')
  const nextBtn = document.getElementById('testimonial-next')

  let active = 0
  let timer = null
  const duration = 4000

  const render = (index, animate = true) => {
    const t = testimonials[index]
    if (!t) return

    const updateContent = () => {
      if (starsEl) {
        let starsHtml = ''
        for (let s = 0; s < (t.stars || 5); s++) {
          starsHtml += '<i class="fa-solid fa-star"></i>'
        }
        starsEl.innerHTML = starsHtml
      }
      if (textEl) {
        textEl.textContent = `"${t.text}"`
      }
      if (avatarEl) {
        if (t.avatar) {
          avatarEl.innerHTML = `<img src="${t.avatar}" alt="${t.author}" class="testimonial-avatar-img" loading="lazy">`
        } else {
          avatarEl.textContent = t.author ? t.author[0] : 'U'
        }
      }
      if (nameEl) nameEl.textContent = t.author
      if (roleEl) roleEl.textContent = t.role

      dots.forEach((d, i) => d.classList.toggle('testimonial-dot--active', i === index))
    }

    if (animate && card && typeof gsap !== 'undefined') {
      gsap.to(card, {
        opacity: 0.3,
        y: 8,
        duration: 0.18,
        ease: 'power2.in',
        onComplete: () => {
          updateContent()
          gsap.to(card, {
            opacity: 1,
            y: 0,
            duration: 0.35,
            ease: 'power2.out',
          })
        },
      })
    } else {
      updateContent()
    }
  }

  const stopAutoRotate = () => {
    if (timer) {
      clearInterval(timer)
      timer = null
    }
  }

  const startAutoRotate = () => {
    stopAutoRotate()

    timer = setInterval(() => {
      active = (active + 1) % testimonials.length
      render(active, true)
    }, duration)
  }

  const goTo = (index) => {
    active = (index + testimonials.length) % testimonials.length
    render(active, true)
    startAutoRotate()
  }

  dots.forEach((dot) => {
    dot.addEventListener('click', () => {
      const idx = parseInt(dot.dataset.testimonialIndex, 10)
      goTo(idx)
    })
  })

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      goTo(active - 1)
    })
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      goTo(active + 1)
    })
  }

  if (card) {
    card.addEventListener('mouseenter', stopAutoRotate)
    card.addEventListener('mouseleave', startAutoRotate)
    card.addEventListener('touchstart', stopAutoRotate, { passive: true })
    card.addEventListener('touchend', startAutoRotate, { passive: true })
  }

  render(0, false)
  startAutoRotate()
}

/* ============================================
   CTA — scale-in banner (port CTA.tsx)
   ============================================ */
function initCTA() {
  const banner = document.querySelector('[data-cta-banner]')
  if (!banner) return

  gsap.fromTo(
    banner,
    { opacity: 0, scale: 0.95 },
    {
      opacity: 1,
      scale: 1,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: banner,
        start: 'top 85%',
        toggleActions: 'play none none none',
      },
    }
  )
}

/* ============================================
   Scroll progress bar (port App.tsx)
   ============================================ */
function initScrollProgress() {
  const progressBar = document.getElementById('scroll-progress')
  if (!progressBar) return

  gsap.to(progressBar, {
    scaleX: 1,
    ease: 'none',
    scrollTrigger: {
      trigger: document.body,
      start: 'top top',
      end: 'bottom bottom',
      scrub: 0.3,
    },
  })
}

/* ============================================
   Hero Banner Carousel (Takapedia Style)
   ============================================ */
function initHeroBannerCarousel() {
  const carousel = document.getElementById('banner-carousel')
  if (!carousel) return

  const slides = carousel.querySelectorAll('.banner-slide')
  const dots = carousel.querySelectorAll('.banner-dot')
  const prevBtn = document.getElementById('banner-prev')
  const nextBtn = document.getElementById('banner-next')

  if (!slides.length) return

  let current = 0
  let autoTimer = null
  const total = slides.length
  const intervalTime = 4500

  const updateSlide = (index) => {
    current = (index + total) % total

    slides.forEach((slide, i) => {
      if (i === current) {
        slide.classList.add('banner-slide--active')
        slide.setAttribute('aria-hidden', 'false')
      } else {
        slide.classList.remove('banner-slide--active')
        slide.setAttribute('aria-hidden', 'true')
      }
    })

    dots.forEach((dot, i) => {
      dot.classList.toggle('banner-dot--active', i === current)
      dot.setAttribute('aria-current', i === current ? 'true' : 'false')
    })
  }

  const nextSlide = () => updateSlide(current + 1)
  const prevSlide = () => updateSlide(current - 1)

  const startAuto = () => {
    stopAuto()
    autoTimer = setInterval(nextSlide, intervalTime)
  }

  const stopAuto = () => {
    if (autoTimer) {
      clearInterval(autoTimer)
      autoTimer = null
    }
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.preventDefault()
      nextSlide()
      startAuto()
    })
  }

  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault()
      prevSlide()
      startAuto()
    })
  }

  dots.forEach((dot) => {
    dot.addEventListener('click', (e) => {
      e.preventDefault()
      const targetIndex = parseInt(dot.dataset.slide, 10)
      if (!isNaN(targetIndex)) {
        updateSlide(targetIndex)
        startAuto()
      }
    })
  })

  // Pause on hover
  carousel.addEventListener('mouseenter', stopAuto)
  carousel.addEventListener('mouseleave', startAuto)

  // Touch swipe gestures for mobile
  let touchStartX = 0
  let touchEndX = 0

  carousel.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX
  }, { passive: true })

  carousel.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].screenX
    const diff = touchStartX - touchEndX
    if (Math.abs(diff) > 40) {
      if (diff > 0) {
        nextSlide()
      } else {
        prevSlide()
      }
      startAuto()
    }
  }, { passive: true })

  updateSlide(0)
  startAuto()
}

/* ============================================
   Category Cards — filter & scroll to products
   ============================================ */
function initCategoryCardActions() {
  const cards = document.querySelectorAll('.cat-card[data-category-filter]')
  if (!cards.length) return

  cards.forEach((card) => {
    const handleAction = () => {
      const catKey = card.dataset.categoryFilter
      if (!catKey) return

      // Find matching product tab
      const tab = document.querySelector(`.product-tab[data-filter="${catKey}"]`)
      if (tab) {
        tab.click()
      }

      // Smooth scroll to products
      const target = document.getElementById('products')
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' })
      }
    }

    card.addEventListener('click', handleAction)
    card.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault()
        handleAction()
      }
    })
  })
}

/* ============================================
   Bootstrap — setara urutan mounting di App.tsx
   ============================================ */
function revealMain() {
  document.getElementById('main')?.classList.add('main--visible')
  ScrollTrigger.refresh()
  window.dispatchEvent(new Event('scroll'))
  window.dispatchEvent(new Event('resize'))
}

// Preloader dulu, lalu main content muncul
initPreloader(revealMain)

// Lenis + GSAP sync (port useLenis.ts)
const lenis = initLenis()

// Navigasi & scroll progress
initNavigation(lenis)
initScrollProgress()

// Takapedia Hero Banner Carousel
initHeroBannerCarousel()
initCategoryCardActions()

// Animasi per section (port hooks useScrollReveal.ts)
initHero()
initStaggerReveal()
initTextReveal()
initParallax()
initClipReveal()
initGalleryReveal()
initPeelReveal()
initFeatured()
initSpecialty()
initHorizontalScroll()
initAboutStats()
initTestimonials()
initCTA()
initProductFilter()
initOrderModal(lenis)
