document.addEventListener('DOMContentLoaded', function () {

    // 1. Ask before running a form that deletes something.
    //    Any <form data-confirm="..."> shows the message first.
    var forms = document.querySelectorAll('form[data-confirm]');
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (event) {
            var message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    }

    // 2. Show the chosen image before it is uploaded.
    var fileInput = document.getElementById('image');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var oldPreview = document.getElementById('preview');
            if (oldPreview) {
                oldPreview.remove();
            }

            var file = this.files[0];
            if (!file) {
                return;
            }

            var img = document.createElement('img');
            img.id = 'preview';
            img.className = 'current-image img-thumbnail d-block mt-2';
            img.alt = 'Preview of the image you chose';
            img.src = URL.createObjectURL(file);
            this.parentNode.appendChild(img);
        });
    }

});
