<script>
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
                    // Toggle the readonly attribute and the custom CSS class
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
            });
        });
    });
</script>