<style>
    .cke_notifications_area { display: none; !important; }
</style>


<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

<script>
    function initializeEditors() {
        const editors = document.querySelectorAll('.editor');
        editors.forEach((editor) => {
            if (editor.id) {
                CKEDITOR.replace(editor.id);
            }
        });

    }

    initializeEditors();
</script>

<script>
    CKEDITOR.editorConfig = function( config ) {
        config.versionCheck = false;
    };
</script>
