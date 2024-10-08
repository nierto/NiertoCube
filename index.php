<?php get_header(); ?>
        <div id="wrapper_left">
            <?php for ($i = 0; $i < 3; $i++): ?>
                <div id="button_<?php echo $i+1; ?>" class="navButton accelerated">
                    <button class="navName" data-face-index="<?php echo $i; ?>" data-face="face<?php echo $i; ?>" data-slug="face-<?php echo $i+1; ?>" tabindex="0">Face <?php echo $i+1; ?></button>
                </div>
            <?php endfor; ?>
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
            <?php for ($i = 3; $i < 6; $i++): ?>
                <div id="button_<?php echo $i+1; ?>" class="navButton accelerated">
                    <button class="navName" data-face-index="<?php echo $i; ?>" data-face="face<?php echo $i; ?>" data-slug="face-<?php echo $i+1; ?>" tabindex="0">Face <?php echo $i+1; ?></button>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <script type="text/javascript">
    // this function is used to tunnel functions within the iframes. 
    function tunnel(fn) {fn();}
    </script>
<?php get_footer(); ?>