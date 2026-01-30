<script>
  document.addEventListener('livewire:init', function() {
    // Initialize simple editor when modal is shown
    Livewire.on('open-rule-editor', () => {
        setTimeout(() => {
            const textarea = document.getElementById('ruleContent');
            if (textarea) {
                // Make textarea visible and styled
                textarea.style.display = 'block';
                textarea.style.minHeight = '400px';
                textarea.style.padding = '10px';
                textarea.style.border = '1px solid #ddd';
                textarea.style.borderRadius = '4px';
                textarea.style.fontFamily = 'Vazirmatn, Tahoma, Arial, sans-serif';
                textarea.style.fontSize = '14px';
                textarea.style.lineHeight = '1.6';
                textarea.style.direction = 'rtl';
                textarea.style.textAlign = 'right';
                textarea.style.resize = 'vertical';
                
                // Add simple toolbar
                const toolbar = document.createElement('div');
                toolbar.className = 'simple-editor-toolbar';
                toolbar.style.cssText = `
                    background: #f8f9fa;
                    border: 1px solid #ddd;
                    border-bottom: none;
                    padding: 10px;
                    border-radius: 4px 4px 0 0;
                    display: flex;
                    gap: 5px;
                    flex-wrap: wrap;
                `;
                
                // Add toolbar buttons
                const buttons = [
                    { text: 'B', action: 'bold', style: 'font-weight: bold;' },
                    { text: 'I', action: 'italic', style: 'font-style: italic;' },
                    { text: 'U', action: 'underline', style: 'text-decoration: underline;' },
                    { text: 'H1', action: 'heading1', style: 'font-size: 1.5em; font-weight: bold;' },
                    { text: 'H2', action: 'heading2', style: 'font-size: 1.3em; font-weight: bold;' },
                    { text: 'List', action: 'list', style: '' },
                    { text: 'Link', action: 'link', style: '' },
                    { text: 'Code', action: 'code', style: 'font-family: monospace; background: #f1f1f1; padding: 2px 4px;' }
                ];
                
                buttons.forEach(btn => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = btn.text;
                    button.style.cssText = `
                        padding: 5px 10px;
                        border: 1px solid #ddd;
                        background: white;
                        cursor: pointer;
                        border-radius: 3px;
                        ${btn.style}
                    `;
                    button.onclick = () => {
                        insertText(textarea, btn.action);
                    };
                    toolbar.appendChild(button);
                });
                
                // Insert toolbar before textarea
                textarea.parentNode.insertBefore(toolbar, textarea);
                
                // Store reference for cleanup
                window.simpleEditorToolbar = toolbar;
            }
        }, 300);
    });
    
    // Clean up when modal is closed
    Livewire.on('close-rule-modal', () => {
        if (window.simpleEditorToolbar) {
            window.simpleEditorToolbar.remove();
            window.simpleEditorToolbar = null;
        }
    });
    
    // Helper function to insert text/formatting
    function insertText(textarea, action) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        let newText = '';
        
        switch(action) {
            case 'bold':
                newText = `<strong>${selectedText || 'متن پررنگ'}</strong>`;
                break;
            case 'italic':
                newText = `<em>${selectedText || 'متن کج'}</em>`;
                break;
            case 'underline':
                newText = `<u>${selectedText || 'متن زیرخط‌دار'}</u>`;
                break;
            case 'heading1':
                newText = `<h1>${selectedText || 'عنوان ۱'}</h1>`;
                break;
            case 'heading2':
                newText = `<h2>${selectedText || 'عنوان ۲'}</h2>`;
                break;
            case 'list':
                newText = `<ul><li>${selectedText || 'آیتم لیست'}</li></ul>`;
                break;
            case 'link':
                const url = prompt('آدرس لینک را وارد کنید:', 'https://');
                newText = `<a href="${url}">${selectedText || 'متن لینک'}</a>`;
                break;
            case 'code':
                newText = `<code>${selectedText || 'کد'}</code>`;
                break;
            default:
                newText = selectedText;
        }
        
        textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
        
        // Update Livewire
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Set cursor position
        textarea.focus();
        textarea.setSelectionRange(start + newText.length, start + newText.length);
    }
</script>
