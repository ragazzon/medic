/**
 * MEDIC - Sistema de Controle Médico Familiar
 * JavaScript Principal
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initFileUpload();
    initLightbox();
    initAlerts();
    initConfirmDelete();
    initDateMasks();
    initInputMasks();
});

/* ============ SIDEBAR ============ */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
}

/* ============ FILE UPLOAD ============ */
function initFileUpload() {
    const uploadAreas = document.querySelectorAll('.file-upload-area');
    
    uploadAreas.forEach(function(area) {
        // Skip batch upload areas (handled by their own inline JS)
        if (area.id === 'batchUploadArea') return;

        const input = area.querySelector('input[type="file"]');
        const previewContainer = area.closest('.upload-wrapper')?.querySelector('.file-preview');

        // Click to upload
        area.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON') {
                input.click();
            }
        });

        // Drag and drop
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', function(e) {
            e.preventDefault();
            area.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });

        // Preview files
        if (input) {
            input.addEventListener('change', function() {
                if (previewContainer) {
                    showFilePreview(this.files, previewContainer);
                }
            });
        }
    });
}

function showFilePreview(files, container) {
    container.innerHTML = '';
    
    Array.from(files).forEach(function(file, index) {
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                item.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <div class="file-info">${file.name}</div>
                    <button type="button" class="remove-file" data-index="${index}" title="Remover">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                container.appendChild(item);
            };
            reader.readAsDataURL(file);
        } else {
            item.innerHTML = `
                <div style="height:90px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                    <i class="bi bi-file-earmark-pdf" style="font-size:36px;color:#dc3545;"></i>
                </div>
                <div class="file-info">${file.name}</div>
                <button type="button" class="remove-file" data-index="${index}" title="Remover">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(item);
        }
    });
}

/* ============ LIGHTBOX ============ */
function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    const lightboxImg = lightbox.querySelector('img');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    const counter = lightbox.querySelector('.lightbox-counter');
    
    let images = [];
    let currentIndex = 0;

    // Attach to gallery items
    document.querySelectorAll('.gallery-item[data-image]').forEach(function(item, idx) {
        item.addEventListener('click', function() {
            images = Array.from(document.querySelectorAll('.gallery-item[data-image]')).map(function(el) {
                return el.getAttribute('data-image');
            });
            currentIndex = idx;
            showImage();
            lightbox.classList.add('active');
        });
    });

    function showImage() {
        lightboxImg.src = images[currentIndex];
        if (counter) {
            counter.textContent = (currentIndex + 1) + ' / ' + images.length;
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            lightbox.classList.remove('active');
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage();
        });
    }

    // Close on background click
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.classList.remove('active');
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') lightbox.classList.remove('active');
        if (e.key === 'ArrowLeft' && prevBtn) prevBtn.click();
        if (e.key === 'ArrowRight' && nextBtn) nextBtn.click();
    });
}

/* ============ AUTO-DISMISS ALERTS ============ */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        }, 5000);
    });
}

/* ============ CONFIRM DELETE ============ */
function initConfirmDelete() {
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja excluir?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

/* ============ DATE MASKS (DD/MM/YYYY) ============ */
function initDateMasks() {
    document.querySelectorAll('input.date-br').forEach(function(input) {
        input.setAttribute('maxlength', '10');
        input.setAttribute('placeholder', 'DD/MM/AAAA');
        input.addEventListener('input', function(e) {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 8) v = v.substring(0, 8);
            if (v.length >= 5) {
                v = v.substring(0,2) + '/' + v.substring(2,4) + '/' + v.substring(4);
            } else if (v.length >= 3) {
                v = v.substring(0,2) + '/' + v.substring(2);
            }
            this.value = v;
        });
        input.addEventListener('blur', function() {
            const v = this.value;
            if (v && !/^\d{2}\/\d{2}\/\d{4}$/.test(v)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

/* ============ INPUT MASKS (CPF, Phone) ============ */
function initInputMasks() {
    document.querySelectorAll('input.mask-cpf').forEach(function(input) {
        input.setAttribute('maxlength', '14');
        input.addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 9) v = v.substring(0,3)+'.'+v.substring(3,6)+'.'+v.substring(6,9)+'-'+v.substring(9);
            else if (v.length > 6) v = v.substring(0,3)+'.'+v.substring(3,6)+'.'+v.substring(6);
            else if (v.length > 3) v = v.substring(0,3)+'.'+v.substring(3);
            this.value = v;
        });
    });
    document.querySelectorAll('input.mask-phone').forEach(function(input) {
        input.setAttribute('maxlength', '15');
        input.addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '');
            if (v.length > 11) v = v.substring(0, 11);
            if (v.length > 6) v = '('+v.substring(0,2)+') '+v.substring(2,7)+'-'+v.substring(7);
            else if (v.length > 2) v = '('+v.substring(0,2)+') '+v.substring(2);
            else if (v.length > 0) v = '('+v;
            this.value = v;
        });
    });
}

/* ============ UTILITY FUNCTIONS ============ */

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' bytes';
}

/**
 * Show loading state on button
 */
function setButtonLoading(btn, loading) {
    if (loading) {
        btn.setAttribute('data-original-text', btn.innerHTML);
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Aguarde...';
        btn.disabled = true;
    } else {
        btn.innerHTML = btn.getAttribute('data-original-text');
        btn.disabled = false;
    }
}