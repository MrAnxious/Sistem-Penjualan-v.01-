// File: admin/assets/js/admin.js
// JavaScript untuk panel admin

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('body').classList.toggle('sidebar-toggled');
            document.querySelector('.sidebar').classList.toggle('toggled');
        });
    }
    
    // Close sidebar on mobile when screen size is changed
    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            document.querySelector('.sidebar').classList.add('toggled');
        } else {
            document.querySelector('.sidebar').classList.remove('toggled');
        }
    });
    
    // Image preview for forms
    const imageInputs = document.querySelectorAll('.image-input');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.querySelector(this.dataset.preview);
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                e.preventDefault();
            }
        });
    });
    
    // Datepicker
    const dateInputs = document.querySelectorAll('.datepicker');
    if (dateInputs.length > 0 && typeof flatpickr !== 'undefined') {
        dateInputs.forEach(input => {
            flatpickr(input, {
                enableTime: input.dataset.enableTime === 'true',
                dateFormat: input.dataset.format || 'Y-m-d',
                locale: 'id'
            });
        });
    }
    
    // Select2
    const select2Inputs = document.querySelectorAll('.select2');
    if (select2Inputs.length > 0 && typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    }
    
    // Summernote editor
    const editors = document.querySelectorAll('.summernote');
    if (editors.length > 0 && typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            height: 300,
            tabsize: 2,
            placeholder: 'Tulis deskripsi di sini...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    }
    
    // Multi-images upload
    const multiImageInput = document.querySelector('#multiImageInput');
    if (multiImageInput) {
        multiImageInput.addEventListener('change', function() {
            const previewContainer = document.querySelector('#multiImagePreview');
            previewContainer.innerHTML = '';
            
            if (this.files) {
                for (let i = 0; i < this.files.length; i++) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewCard = document.createElement('div');
                        previewCard.className = 'col-md-3 mb-3';
                        previewCard.innerHTML = `
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top" alt="Preview">
                                <div class="card-body p-2 text-center">
                                    <small>${this.files[i].name}</small>
                                </div>
                            </div>
                        `;
                        previewContainer.appendChild(previewCard);
                    }.bind(this);
                    
                    reader.readAsDataURL(this.files[i]);
                }
            }
        });
    }
    
    // Order status update
    const statusSelect = document.querySelector('#orderStatus');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            document.querySelector('#updateStatusForm').submit();
        });
    }
});