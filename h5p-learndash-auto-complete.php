<?php
/**
 * Auto-complete LearnDash topic when H5P Course Presentation is completed
 * Place this file inside your child theme, then include it in functions.php
 */

function h5p_auto_complete_learndash_topic() {
    if ( ! is_singular('sfwd-topic') ) {
        return;
    }

    global $post;

    if ( has_block( 'h5p/h5p', $post->post_content ) || strpos( $post->post_content, 'h5p-content' ) !== false ) {
        ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Wait for both H5P and the Mark Complete button to be available
    const checkInterval = setInterval(function () {
        const h5pReady = typeof H5P !== 'undefined' && H5P.externalDispatcher;
        const $btn = jQuery('.ld-button--topic-mark-complete');

        if (h5pReady && $btn.length) {
            clearInterval(checkInterval);

            // Disable and dim the button
            $btn.prop('disabled', true);
            $btn.css({
                'opacity': '0.5',
                'pointer-events': 'none',
                'cursor': 'not-allowed'
            });

            // Listen for H5P xAPI completion
            H5P.externalDispatcher.on('xAPI', function (event) {
                const verbId = event.data.statement.verb.id;
                const activityType = event.getVerifiedStatementValue(['object', 'definition', 'type']);

                if (verbId === 'http://adlnet.gov/expapi/verbs/completed' && (
                    activityType === 'http://adlnet.gov/expapi/activities/course' ||
                    activityType === 'http://h5p.org/x-api/activities/presentation'
                )) {
                    if (window.h5pCompletionSent) return;
                    window.h5pCompletionSent = true;

                    // Re-enable the button
                    $btn.prop('disabled', false);
                    $btn.css({
                        'opacity': '1',
                        'pointer-events': 'auto',
                        'cursor': 'pointer'
                    });

                    // Optional message
                    const msg = document.createElement('div');
                    msg.innerText = '✅ Slides completed. You may now continue.';
                    msg.style = 'margin-top: 20px; color: green;';
                    $btn.after(msg);
                }
            });
        }
    }, 300); // Check every 300ms
});
</script>


        <?php
    }
}
add_action( 'wp_footer', 'h5p_auto_complete_learndash_topic' );
