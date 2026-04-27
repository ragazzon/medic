/**
 * Multi-Specialty autocomplete widget with tags
 * Usage: initSpecialtyMultiWidget(containerId, ajaxUrl, existingValues)
 */
function initSpecialtyMultiWidget(containerId, ajaxUrl, existingValues) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var input = container.querySelector('.specialty-search');
    var dropdown = container.querySelector('.specialty-dropdown');
    var tagsContainer = container.querySelector('.specialty-tags');
    var hiddenContainer = container.querySelector('.specialty-hidden-inputs');
    var debounceTimer = null;
    var selectedSpecialties = [];

    // Inicializar com valores existentes
    if (existingValues && Array.isArray(existingValues)) {
        existingValues.forEach(function(name) {
            if (name && name.trim()) addTag(name.trim());
        });
    }

    function fetchResults(q) {
        fetch(ajaxUrl + '?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(items) {
                var html = '';
                // Filtrar itens já selecionados
                var filtered = items.filter(function(item) {
                    return selectedSpecialties.indexOf(item.name) === -1;
                });

                if (filtered.length === 0 && q.trim().length > 0 && selectedSpecialties.indexOf(q.trim()) === -1) {
                    html = '<div class="specialty-option specialty-create" data-name="' + q.replace(/"/g, '&quot;') + '">' +
                        '<i class="bi bi-plus-circle me-1 text-success"></i>Criar e adicionar "<strong>' + q + '</strong>"</div>';
                } else {
                    var exactMatch = false;
                    filtered.forEach(function(item) {
                        if (item.name.toLowerCase() === q.toLowerCase()) exactMatch = true;
                        html += '<div class="specialty-option" data-id="' + item.id + '" data-name="' + item.name.replace(/"/g, '&quot;') + '">' +
                            '<i class="bi bi-heart-pulse me-1 text-primary"></i>' + item.name + '</div>';
                    });
                    if (!exactMatch && q.trim().length > 0 && selectedSpecialties.indexOf(q.trim()) === -1) {
                        html += '<div class="specialty-option specialty-create" data-name="' + q.replace(/"/g, '&quot;') + '">' +
                            '<i class="bi bi-plus-circle me-1 text-success"></i>Criar e adicionar "<strong>' + q + '</strong>"</div>';
                    }
                }
                dropdown.innerHTML = html;
                dropdown.style.display = html ? 'block' : 'none';
                bindOptions();
            });
    }

    function bindOptions() {
        dropdown.querySelectorAll('.specialty-option').forEach(function(opt) {
            opt.addEventListener('mousedown', function(e) {
                e.preventDefault();
                var name = this.getAttribute('data-name');
                if (this.classList.contains('specialty-create')) {
                    createAndAdd(name);
                } else {
                    addTag(name);
                    input.value = '';
                    dropdown.style.display = 'none';
                }
            });
        });
    }

    function addTag(name) {
        if (selectedSpecialties.indexOf(name) !== -1) return;
        selectedSpecialties.push(name);
        renderTags();
        updateHiddenInputs();
    }

    function removeTag(name) {
        selectedSpecialties = selectedSpecialties.filter(function(s) { return s !== name; });
        renderTags();
        updateHiddenInputs();
    }

    function renderTags() {
        var html = '';
        selectedSpecialties.forEach(function(name) {
            html += '<span class="badge bg-primary me-1 mb-1 d-inline-flex align-items-center gap-1" style="font-size:.85rem;padding:.4em .6em;">' +
                '<i class="bi bi-heart-pulse"></i>' +
                '<span>' + name + '</span>' +
                '<button type="button" class="btn-close btn-close-white ms-1" style="font-size:.55rem;" data-name="' + name.replace(/"/g, '&quot;') + '" title="Remover"></button>' +
                '</span>';
        });
        tagsContainer.innerHTML = html;

        tagsContainer.querySelectorAll('.btn-close').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                removeTag(this.getAttribute('data-name'));
            });
        });
    }

    function updateHiddenInputs() {
        var html = '';
        selectedSpecialties.forEach(function(name) {
            html += '<input type="hidden" name="specialties[]" value="' + name.replace(/"/g, '&quot;') + '">';
        });
        // Also keep legacy single specialty field (first one) for backward compatibility
        if (selectedSpecialties.length > 0) {
            html += '<input type="hidden" name="specialty" value="' + selectedSpecialties[0].replace(/"/g, '&quot;') + '">';
        } else {
            html += '<input type="hidden" name="specialty" value="">';
        }
        hiddenContainer.innerHTML = html;
    }

    function createAndAdd(name) {
        var fd = new FormData();
        fd.append('name', name);
        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.name) {
                    addTag(data.name);
                    input.value = '';
                    dropdown.style.display = 'none';
                }
            });
    }

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            fetchResults(input.value);
        }, 250);
    });

    input.addEventListener('focus', function() {
        if (input.value.length === 0) fetchResults('');
        else fetchResults(input.value);
    });

    input.addEventListener('blur', function() {
        setTimeout(function() { dropdown.style.display = 'none'; }, 200);
    });

    // Enter key adds current text as tag
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var val = input.value.trim();
            if (val && selectedSpecialties.indexOf(val) === -1) {
                addTag(val);
                input.value = '';
                dropdown.style.display = 'none';
            }
        }
        // Backspace removes last tag when input is empty
        if (e.key === 'Backspace' && input.value === '' && selectedSpecialties.length > 0) {
            removeTag(selectedSpecialties[selectedSpecialties.length - 1]);
        }
    });

    // Initialize hidden inputs
    updateHiddenInputs();
}