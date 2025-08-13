<script>
    $(document).ready(function() {
        // Get the current URL path
        var currentPath = window.location.pathname; // e.g., /new-template/subscription/upgrade

        // Get the base URL part (e.g., /new-template) if your CI is in a subfolder
        // This is important if your site is not in the root of localhost
        // For 'http://localhost/new-template/subscription/upgrade', this extracts '/new-template'
        var base_url_path = '<?php echo $this->config->item('base_url'); ?>';
        var path_parts = base_url_path.split('/');
        var ci_subfolder = path_parts[path_parts.length - 2]; // Gets 'new-template' assuming base_url ends with /new-template/

        // Construct the target URL path relative to the domain root
        var target_relative_path = '/' + ci_subfolder + '/subscription/upgrade';

        // Handle cases where CI is in root (ci_subfolder would be empty or not relevant)
        if (ci_subfolder === '' || ci_subfolder === 'http:' || ci_subfolder === 'https:') {
            target_relative_path = '/subscription/upgrade';
        }


        // Check if the current URL path matches the target upgrade page
        if (currentPath === target_relative_path) {
            // Add the 'collapsed' class to the .vertical element
            // This is the element that your original JS snippet targets for collapsing
            $(".vertical").addClass("collapsed");
        } else {
            // Ensure the 'collapsed' class is removed on other pages
            $(".vertical").removeClass("collapsed");
            // If your theme uses 'open' or 'narrow' classes, you might need to remove those too
            // $(".vertical").removeClass("open narrow");
        }

        // --- Your existing addRow and nominal_bayar logic would go here ---
        // (omitted for brevity, assume it's still present)
        // For example:
        // function cleanAndParseNumber(...) { ... }
        // $(document).on('input change', 'input[name="nominal_bayar"]', function() { ... });
        // var rowCount = 1;
        // $('#addRow').on('click', function() { ... });
    });
</script>