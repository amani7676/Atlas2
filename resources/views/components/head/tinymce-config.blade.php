<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  document.addEventListener('livewire:init', function() {
    // Initialize TinyMCE when modal is shown
    Livewire.on('open-rule-editor', () => {
        setTimeout(() => {
            // Remove any existing instance
            if (tinymce.get('ruleContent')) {
                tinymce.get('ruleContent').remove();
            }
            
            tinymce.init({
                selector: 'textarea#ruleContent',
                directionality: 'rtl',
                language: 'fa',
                plugins: 'code table lists link image charmap preview anchor searchreplace visualblocks fullscreen insertdatetime media help wordcount',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link | image',
                content_style: 'body { font-family: Vazirmatn, Tahoma, Arial, sans-serif; direction: rtl; text-align: right; }',
                setup: function (editor) {
                    // Update Livewire when content changes
                    editor.on('change input', function () {
                        const content = editor.getContent();
                        // Find the Livewire component
                        const textarea = document.getElementById('ruleContent');
                        if (textarea) {
                            const wireElement = textarea.closest('[wire\\:id]');
                            if (wireElement) {
                                const wireId = wireElement.getAttribute('wire:id');
                                const component = Livewire.find(wireId);
                                if (component) {
                                    component.set('content', content);
                                }
                            }
                        }
                    });
                    
                    // Set initial content
                    editor.on('init', function() {
                        const textarea = document.getElementById('ruleContent');
                        if (textarea && textarea.value) {
                            editor.setContent(textarea.value);
                        }
                    });
                }
            });
        }, 300);
    });
  });
</script>