// TinyMCE Configuration for Article Editor

export function initializeTinyMCE(selector = '.tinymce-editor') {
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE is not loaded');
        return;
    }

    tinymce.init({
        selector: selector,
        height: 500,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'link image media | ' +
            'forecolor backcolor | ' +
            'removeformat | code fullscreen | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
        
        // Image upload configuration
        images_upload_url: '/writer/articles/upload-image',
        automatic_uploads: true,
        images_reuse_filename: false,
        images_upload_handler: function (blobInfo, success, failure, progress) {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            xhr.open('POST', '/writer/articles/upload-image');
            
            xhr.upload.onprogress = function (e) {
                progress(e.loaded / e.total * 100);
            };
            
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const json = JSON.parse(xhr.responseText);
                    
                    if (json.success && json.location) {
                        success(json.location);
                    } else {
                        failure('Image upload failed: ' + (json.error || 'Unknown error'));
                    }
                } else {
                    failure('HTTP Error: ' + xhr.status);
                }
            };
            
            xhr.onerror = function () {
                failure('Image upload failed. Please check your connection.');
            };
            
            xhr.send(formData);
        },
        
        // File size limit
        file_picker_types: 'image',
        
        // Video embed configuration
        media_url_resolver: function (data, resolve) {
            // YouTube
            if (data.url.match(/youtube\.com\/watch\?v=([^&]+)/)) {
                const videoId = data.url.match(/youtube\.com\/watch\?v=([^&]+)/)[1];
                resolve({
                    html: `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`
                });
            }
            // Vimeo
            else if (data.url.match(/vimeo\.com\/(\d+)/)) {
                const videoId = data.url.match(/vimeo\.com\/(\d+)/)[1];
                resolve({
                    html: `<iframe width="560" height="315" src="https://player.vimeo.com/video/${videoId}" frameborder="0" allowfullscreen></iframe>`
                });
            }
            else {
                resolve({ html: '' });
            }
        },
        
        // Word count setup
        setup: function (editor) {
            editor.on('change', function () {
                updateWordCount(editor);
                updateReadingTime(editor);
            });
        }
    });
}

// Update word count display
function updateWordCount(editor) {
    const wordCount = editor.plugins.wordcount.getCount();
    const wordCountElement = document.getElementById('word-count');
    if (wordCountElement) {
        wordCountElement.textContent = wordCount;
    }
}

// Update reading time display
function updateReadingTime(editor) {
    const wordCount = editor.plugins.wordcount.getCount();
    const readingTime = Math.ceil(wordCount / 200); // 200 words per minute
    const readingTimeElement = document.getElementById('reading-time');
    if (readingTimeElement) {
        readingTimeElement.textContent = readingTime;
    }
}

// Auto-generate slug from title
export function setupSlugGenerator() {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = slugify(this.value);
            }
        });
        
        slugInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    }
}

function slugify(text) {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.tinymce-editor')) {
        initializeTinyMCE();
        setupSlugGenerator();
    }
});
