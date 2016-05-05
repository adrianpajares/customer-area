<?php /**
 * Template version: 1.0.0
 * Template zone: admin|frontend
 */ ?>
<script type="text/javascript">
    <!--
    "use strict";
    (function ($) {
        $(document).ready(function () {
            if ($.cuar.isAdmin) {
                // Init Select 2
                cuarInitFileAttachmentManager();
            } else {
                // Wait for wizard to be initialized before Select 2
                $('#cuar-js-content-container').on('cuar:wizard:initialized', cuarInitFileAttachmentManager);
            }

            function cuarInitFileAttachmentManager() {
                $('.cuar-js-file-attachment-manager').fileAttachmentManager();
            }
        });
    })(jQuery);
    //-->
</script>