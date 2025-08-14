<script>
    $(document).ready(function() {
        // Get the current URL path
        var currentPath = window.location.pathname; // e.g., /new-template/subscription/upgrade

        // Get the base URL part
        var base_url_path = '<?php echo $this->config->item('base_url'); ?>';
        var path_parts = base_url_path.split('/');
        var ci_subfolder = path_parts[path_parts.length - 2];

        // Construct the target URL path
        var target_relative_path = '/' + ci_subfolder + '/subscription/upgrade';

        if (ci_subfolder === '' || ci_subfolder === 'http:' || ci_subfolder === 'https:') {
            target_relative_path = '/subscription/upgrade';
        }

        // Check if the current URL path matches the target page and the screen width is greater than 480px
        if (currentPath === target_relative_path && $(window).width() > 980) {
            // Add the 'collapsed' class only if both conditions are true
            $(".vertical").addClass("collapsed");
        }
    });
</script>