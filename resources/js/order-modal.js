/**
 * order-modal.js — Kontrol interaktif Modal Formulir Pemesanan DcemilinYuk
 */
export function initOrderModal(lenis) {
  const backdrop = document.getElementById('orderModalBackdrop')
  if (!backdrop) return

  const closeBtn = document.getElementById('orderModalClose')
  const cancelBtn = document.getElementById('orderModalCancel')
  const form = document.getElementById('orderModalForm')
  const scrollableBody = document.getElementById('modalScrollableBody')

  const productIdInput = document.getElementById('modalProductId')
  const nameEl = document.getElementById('modalProductName')
  const priceEl = document.getElementById('modalProductPrice')
  const imgEl = document.getElementById('modalProductImg')
  const catEl = document.getElementById('modalProductCategory')
  const qtyInput = document.getElementById('modalProductQty')
  const minusBtn = document.getElementById('modalQtyMinus')
  const plusBtn = document.getElementById('modalQtyPlus')
  const subtotalPreview = document.getElementById('modalSubtotalPreview')

  let currentUnitPrice = 0

  function formatRupiah(amount) {
    return 'Rp ' + Number(amount).toLocaleString('id-ID')
  }

  function updateSubtotal() {
    const qty = parseInt(qtyInput.value) || 1
    const total = currentUnitPrice * qty
    if (subtotalPreview) {
      subtotalPreview.textContent = formatRupiah(total)
    }
  }

  function openModal(data) {
    currentUnitPrice = parseInt(data.price) || 0

    if (productIdInput) productIdInput.value = data.id || ''
    if (nameEl) nameEl.textContent = data.name || 'Produk'
    if (priceEl) priceEl.textContent = data.formattedPrice || formatRupiah(currentUnitPrice)
    if (imgEl) {
      imgEl.src = data.image || ''
      imgEl.alt = data.name || 'Produk'
    }
    if (catEl) catEl.textContent = data.category || 'Cemilan'
    if (qtyInput) qtyInput.value = 1

    updateSubtotal()

    // Stop Lenis smooth scroll on the background page
    if (lenis && typeof lenis.stop === 'function') {
      lenis.stop()
    }

    document.body.classList.add('modal-open')
    document.body.style.overflow = 'hidden'

    backdrop.classList.add('order-modal-backdrop--open')

    // Reset posisi scroll modal ke paling atas
    if (scrollableBody) {
      scrollableBody.scrollTop = 0
    }

    // Focus input pertama dengan delay lembut
    setTimeout(() => {
      document.getElementById('customerName')?.focus()
    }, 200)
  }

  function closeModal() {
    backdrop.classList.remove('order-modal-backdrop--open')
    document.body.classList.remove('modal-open')
    document.body.style.overflow = ''

    // Resume Lenis smooth scroll on the background page
    if (lenis && typeof lenis.start === 'function') {
      lenis.start()
    }
  }

  // Hentikan propagasi event wheel & touch ke window agar Lenis tidak scroll halaman utama
  backdrop.addEventListener('wheel', (e) => {
    e.stopPropagation()
  }, { passive: true })

  backdrop.addEventListener('touchmove', (e) => {
    if (!e.target.closest('#modalScrollableBody')) {
      e.preventDefault()
    }
    e.stopPropagation()
  }, { passive: false })

  // Event listener tombol-tombol "Pesan Sekarang"
  document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.js-open-order-modal')
    if (!trigger) return

    e.preventDefault()
    const dataset = trigger.dataset
    openModal({
      id: dataset.id,
      name: dataset.name,
      price: dataset.price,
      formattedPrice: dataset.formattedPrice,
      image: dataset.image,
      category: dataset.category,
    })
  })

  // Tombol Tutup / Batal
  if (closeBtn) closeBtn.addEventListener('click', closeModal)
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal)

  // Klik di luar kartu modal untuk menutup
  backdrop.addEventListener('click', (e) => {
    if (e.target === backdrop) {
      closeModal()
    }
  })

  // Tekan tombol Escape untuk menutup
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && backdrop.classList.contains('order-modal-backdrop--open')) {
      closeModal()
    }
  })

  // Stepper Qty (-) dan (+)
  if (minusBtn && qtyInput) {
    minusBtn.addEventListener('click', () => {
      let qty = parseInt(qtyInput.value) || 1
      if (qty > 1) {
        qty--
        qtyInput.value = qty
        updateSubtotal()
      }
    })
  }

  if (plusBtn && qtyInput) {
    plusBtn.addEventListener('click', () => {
      let qty = parseInt(qtyInput.value) || 1
      if (qty < 99) {
        qty++
        qtyInput.value = qty
        updateSubtotal()
      }
    })
  }
}
