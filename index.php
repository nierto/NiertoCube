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
                       <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" id="logo_spin" style="width: <?php echo esc_attr($logo_width); ?>; height: <?php echo esc_attr($logo_height); ?>;">
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
    });
    </script>
<?php get_footer(); ?>
