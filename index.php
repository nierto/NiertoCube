<?php get_header(); ?>
    <div id="scene">
        <div id="cube">
            <div class="face one" id='face0'></div>
            <div class="face two" id='face1'></div>
            <div class="face three" id='face2'></div>
            <div class="face four" id='face3'></div>
            <div class="face five" id='face4'></div>
            <div class="face six" id='face5'></div>
            <div class="logo seven">
                <?php
                $logo_url = get_theme_mod('logo_source', ''); // logo
                if ($logo_url):
                ?>
                    <div id="logo_center">
                        <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" id="logo_spin">
                    </div>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    function tunnel(fn) {fn();} // this function is used to tunnel functions within the iframes. 
    document.addEventListener('DOMContentLoaded', (event) => {
     // Ensure variables are defined before this script runs
    if (typeof variables !== 'undefined' && typeof cubeMoveButton === 'function' && typeof callCubeMoveButton === 'function') {
        createControlBar();
    } else {
        console.error('Required variables or functions are not defined.');
    }
    });
    </script>
<?php get_footer(); ?>
