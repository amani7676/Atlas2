<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css" />
<script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.umd.js"></script>
<script>
  document.addEventListener('livewire:init', function() {
    // Initialize CKEditor when modal is shown
    Livewire.on('open-rule-editor', () => {
        setTimeout(() => {
            // Destroy any existing editor instance
            if (window.ruleEditor) {
                window.ruleEditor.destroy().catch(error => {
                    console.error('Error destroying editor:', error);
                });
                window.ruleEditor = null;
            }
            
            const {
                ClassicEditor,
                Essentials,
                Bold,
                Italic,
                Underline,
                Strikethrough,
                Font,
                Paragraph,
                Heading,
                List,
                Link,
                Image,
                ImageUpload,
                ImageToolbar,
                ImageCaption,
                Table,
                TableToolbar,
                MediaEmbed,
                BlockQuote,
                Indent,
                IndentBlock,
                Code,
                CodeBlock,
                HorizontalLine,
                RemoveFormat,
                SourceEditing
            } = CKEDITOR;

            ClassicEditor
                .create(document.querySelector('#ruleContent'), {
                    licenseKey: 'GPL',
                    plugins: [
                        Essentials,
                        Bold,
                        Italic,
                        Underline,
                        Strikethrough,
                        Font,
                        Paragraph,
                        Heading,
                        List,
                        Link,
                        Image,
                        ImageUpload,
                        ImageToolbar,
                        ImageCaption,
                        Table,
                        TableToolbar,
                        MediaEmbed,
                        BlockQuote,
                        Indent,
                        IndentBlock,
                        Code,
                        CodeBlock,
                        HorizontalLine,
                        RemoveFormat,
                        SourceEditing
                    ],
                    toolbar: [
                        'undo', 'redo', '|',
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'link', 'imageUpload', 'mediaEmbed', '|',
                        'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                        'blockQuote', 'codeBlock', 'horizontalLine', '|',
                        'sourceEditing', 'removeFormat'
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:full',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells',
                            'tableProperties'
                        ]
                    },
                    language: 'en',
                    direction: 'rtl'
                })
                .then(editor => {
                    window.ruleEditor = editor;
                    
                    // Set initial content
                    const textarea = document.getElementById('ruleContent');
                    if (textarea && textarea.value) {
                        editor.setData(textarea.value);
                    }
                    
                    // Update Livewire when content changes
                    editor.model.document.on('change:data', () => {
                        const content = editor.getData();
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
                })
                .catch(error => {
                    console.error('Error creating CKEditor:', error);
                    // Fallback to simple textarea if CKEditor fails
                    const textarea = document.getElementById('ruleContent');
                    if (textarea) {
                        textarea.style.display = 'block';
                        textarea.style.minHeight = '300px';
                    }
                });
        }, 300);
    });
    
    // Clean up editor when modal is closed
    Livewire.on('close-rule-modal', () => {
        if (window.ruleEditor) {
            window.ruleEditor.destroy().catch(error => {
                console.error('Error destroying editor:', error);
            });
            window.ruleEditor = null;
        }
    });
  });
</script>
