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
                if ($logo_url):
                ?>
            
                <img decoding="async" src="<?php echo esc_url($logo_url); ?>" alt="Logo" id="logo_spin" style="width: <?php echo esc_attr($logo_width); ?>; height: <?php echo esc_attr($logo_height); ?>;">
                
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    // this function is used to tunnel functions within the iframes. 
    function tunnel(fn) {fn();}
    </script>
<?php get_footer(); ?>