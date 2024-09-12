<?php get_header(); ?>
        <div id="wrapper_left">
            <div id="button_one" class="navButton accelerated">
                <button class="navName" data-face-index="0" tabindex="0"></button>
            </div>
            <div id="button_two" class="navButton accelerated">
                <button class="navName" data-face-index="1" tabindex="0"></button>
            </div>
            <div id="button_three" class="navButton accelerated">
                <button class="navName" data-face-index="2" tabindex="0"></button>
            </div>
        </div>
        <div id="scene" role="region" aria-label="3D Cube Scene">
            <div id="cube" aria-hidden="true">
                <div class="face one" id='face0' role="img"></div>
                <div class="face two" id='face1' role="img"></div>
                <div class="face three" id='face2' role="img"></div>
                <div class="face four" id='face3' role="img"></div>
                <div class="face five" id='face4' role="img"></div>
                <div class="face six" id='face5' role="img"></div>
                <div class="logo seven"></div>
            </div>
        </div>
        <div id="wrapper_right">
            <div id="button_four" class="navButton accelerated">
                <button class="navName" data-face-index="3" tabindex="0"></button>
            </div>
            <div id="button_five" class="navButton accelerated">
                <button class="navName" data-face-index="4" tabindex="0"></button>
            </div>
            <div id="button_six" class="navButton accelerated">
                <button class="navName" data-face-index="5" tabindex="0"></button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    // this function is used to tunnel functions within the iframes. 
    function tunnel(fn) {fn();}
    </script>
<?php get_footer(); ?>