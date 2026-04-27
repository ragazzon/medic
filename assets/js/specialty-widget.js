/**
 * Specialty autocomplete widget with create-on-the-fly
 * Usage: initSpecialtyWidget(inputId, hiddenInputName, ajaxUrl)
 */
function initSpecialtyWidget(containerId, ajaxUrl) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var input = container.querySelector('.specialty-search');
    var dropdown = container.querySelector('.specialty-dropdown');
    var hiddenInput = container.querySelector('input[type="hidden"]');
    var debounceTimer = null;

    function fetchResults(q) {
        fetch(ajaxUrl + '?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(items) {
                var html = '';
                if (items.length === 0 && q.trim().length > 0) {
                    html = '<div class="specialty-option specialty-create" data-name="' + q.replace(/"/g, '&quot;') + '">' +
                        '<i class="bi bi-plus-circle me-1 text-success"></i>Criar "<strong>' + q + '</strong>"</div>';
                } else {
                    var exactMatch = false;
                    items.forEach(function(item) {
                        if (item.name.toLowerCase() === q.toLowerCase()) exactMatch = true;
                        html += '<div class="specialty-option" data-id="' + item.id + '" data-name="' + item.name.replace(/"/g, '&quot;') + '">' +
                            '<i class="bi bi-heart-pulse me-1 text-primary"></i>' + item.name + '</div>';
                    });
                    if (!exactMatch && q.trim().length > 0) {
                        html += '<div class="specialty-option specialty-create" data-name="' + q.replace(/"/g, '&quot;') + '">' +
                            '<i class="bi bi-plus-circle me-1 text-success"></i>Criar "<strong>' + q + '</strong>"</div>';
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
                    createSpecialty(name);
                } else {
                    selectSpecialty(name);
                }
            });
        });
    }

    function selectSpecialty(name) {
        input.value = name;
        hiddenInput.value = name;
        dropdown.style.display = 'none';
    }

    function createSpecialty(name) {
        var fd = new FormData();
        fd.append('name', name);
        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.name) {
                    selectSpecialty(data.name);
                }
            });
    }

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        hiddenInput.value = input.value;
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
}