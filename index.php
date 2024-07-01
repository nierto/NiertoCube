<?php get_header(); ?>
    <div id="wrapper_left">
        <div id="button_one" class="navButton accelerated">
            <button class="navName";"></button>
        </div>
        <div id="button_two" class="navButton accelerated">
            <button class="navName"></button>
        </div>
        <div id="button_three" class="navButton accelerated">
            <button class="navName"></button>
        </div>
    </div>
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
                <img decoding="async" src="<?php echo esc_url($logo_url); ?>" alt="Logo" id="logo_spin accelerated" style="max-width: <?php echo esc_attr($logo_width); ?>;">
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
    <div id="wrapper_right">
        <div id="button_four "class="navButton accelerated">
            <button class="navName"></button>
        </div>
        <div id="button_five" class="navButton accelerated">
            <button class="navName"></button> 
        </div>
        <div id="button_six" class="navButton accelerated">
            <button class="navName"></button>
        </div>
    </div>
    <script type="text/javascript">
    // this function is used to tunnel functions within the iframes. 
    function tunnel(fn) {fn();}
    </script>
<?php get_footer(); ?>