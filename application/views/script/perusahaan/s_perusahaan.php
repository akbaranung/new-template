<script>
    function previewImage(event) {
        // Get the image preview element
        const imagePreview = document.getElementById('logo_preview');
        // Get the selected file
        const file = event.target.files[0];

        // Ensure a file was selected and it's an image
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(e) {
                // Set the image source to the data URL of the selected file
                imagePreview.src = e.target.result;
                // Show the preview image
                imagePreview.style.display = 'block';
            };

            // Read the image as a Data URL
            reader.readAsDataURL(file);
        } else {
            // If no file is selected or it's not an image, hide the preview image
            imagePreview.style.display = 'none';
        }
    }

    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Get all buttons with the class 'btns-edit'
        const editButtons = document.querySelectorAll('.btns-edit');

        // Loop through each button and attach a click event listener
        editButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Get the ID of the target input field from the data-target attribute
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);

                if (targetInput) {
                    // Check if the target is the logo input
                    if (targetId === 'logo_input') {
                        // Toggle the disabled attribute and the custom class
                        if (targetInput.disabled) {
                            // If currently disabled, enable it for editing
                            targetInput.disabled = false;
                            targetInput.classList.remove('is-readonly');
                            this.classList.remove('btn-secondary');
                            this.classList.add('btn-primary');
                            this.innerHTML = '<i class="fe fe-check"></i>';
                        } else {
                            // If currently enabled, check if a file is present
                            if (targetInput.files.length > 0) {
                                // If a file is selected, do not disable it. Just apply the readonly styling.
                                targetInput.classList.add('is-readonly');
                                this.classList.remove('btn-primary');
                                this.classList.add('btn-secondary');
                                this.innerHTML = '<i class="fe fe-edit-2"></i>';
                            } else {
                                // If no file is selected, disable the input
                                targetInput.disabled = true;
                                targetInput.classList.add('is-readonly');
                                this.classList.remove('btn-primary');
                                this.classList.add('btn-secondary');
                                this.innerHTML = '<i class="fe fe-edit-2"></i>';
                            }
                        }
                    } else {
                        // Logic for other inputs using readonly
                        if (targetInput.readOnly) {
                            targetInput.readOnly = false;
                            targetInput.classList.remove('is-readonly');
                            // Add a visual indicator, like changing the button's color
                            this.classList.remove('btn-secondary');
                            this.classList.add('btn-primary');
                            this.innerHTML = '<i class="fe fe-check"></i>';
                        } else {
                            targetInput.readOnly = true;
                            targetInput.classList.add('is-readonly');
                            // Revert the visual indicator
                            this.classList.remove('btn-primary');
                            this.classList.add('btn-secondary');
                            this.innerHTML = '<i class="fe fe-edit-2"></i>';
                        }
                    }
                }
            });
        });
    });
</script>