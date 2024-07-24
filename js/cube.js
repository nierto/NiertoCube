const MOBILE_STATE = window.matchMedia("(min-width: 961px)").matches ? 0 : 1;
let sideCount = 0;
let updCount = 0;
let isTransitioning = false;
let lastTouchY;
let touchScrolling = false;
let scrollVelocity = 0;
let lastTouchTime;
let lastScrollTime = 0;
const scrollThrottle = 10; // ms

document.addEventListener('DOMContentLoaded', function () {
    window.cubeRotationX = 0;
    window.cubeRotationY = 0;
    window.cubeRotationZ = 0;
    window.cubescale = 1;
    window.cubeduration = 250;
    window.cubestate = 2;

    if (typeof variables !== 'undefined') {
        setupCubeButtons();
    } else {
        console.error('Required variables are not defined.');
    }

    document.addEventListener('keydown', arrowKeyHandler);

    document.querySelectorAll('.navButton .navName').forEach(button => {
        button.addEventListener('click', function (event) {
            if (isTransitioning) return;
            isTransitioning = true;
            const faceId = event.currentTarget.getAttribute('data-face');
            const slug = event.currentTarget.getAttribute('data-slug');
            goHome(() => {
                requestAnimationFrame(() => {
                    cubeMoveButton(faceId, slug);
                });
            });
        });
    });

    const logo = document.getElementById('logo_goHome');
    if (logo) {
        logo.addEventListener('click', handleLogoClick);
    } else {
        console.error('Logo element not found');
    }

    window.addEventListener('wheel', handleWheel, { passive: false });
    window.addEventListener('touchstart', handleTouchStart, { passive: true });
    window.addEventListener('touchmove', handleTouchMove, { passive: false });
    window.addEventListener('touchend', handleTouchEnd, { passive: true });
});

function rotateCube(anglex, angley, anglez) {
    const cube = $("#cube");
    cube.css('transition', `transform ${window.cubeduration}ms`);

    window.cubeRotationX = Math.round(window.cubeRotationX + anglex);
    window.cubeRotationY = Math.round(window.cubeRotationY + angley);
    window.cubeRotationZ = Math.round(window.cubeRotationZ + anglez);

    requestAnimationFrame(() => {
        const newTransform = `rotateX(${window.cubeRotationX}deg) rotateY(${window.cubeRotationY}deg) rotateZ(${window.cubeRotationZ}deg)`;
        cube.css('transform', newTransform);
    });

    sideCount = Math.abs(sideCount) === 4 ? 0 : sideCount;
}

function arrowKeyHandler(e) {
    const keyActions = {
        37: sim_left,  // Left arrow key
        39: sim_right, // Right arrow key
        38: sim_up,    // Up arrow key
        40: sim_down   // Down arrow key
    };
    if (keyActions[e.keyCode]) {
        keyActions[e.keyCode]();
    }
}

function sim_up() {
    if (updCount < 1 && sideCount === 0) {  // Allow rotation up if not at maximum up rotation and only if sideCount is 0
        updCount += 1;
        rotateCube(-90, 0, 0);
    }
}

function sim_down() {
    if (updCount > -1 && sideCount === 0) {  // Allow rotation down if not at maximum down rotation and only if sideCount is 0
        updCount -= 1;
        rotateCube(90, 0, 0);
    }
}

function sim_left() {
    if (updCount == 0) {  // Allow left rotation if updCount is zero
        sideCount += 1;
        rotateCube(0, 90, 0);
    }
}

function sim_right() {
    if (updCount == 0) {  // Allow right rotation if updCount is zero
        sideCount -= 1;
        rotateCube(0, -90, 0);
    }
}

function goHome(callback) {
    if (!isTransitioning) return;
    const actions = [
        { condition: () => updCount > 0, action: sim_down },
        { condition: () => updCount < 0, action: sim_up },
        { condition: () => sideCount < 0, action: sim_left },
        { condition: () => sideCount > 0, action: sim_right },
    ];
    const intervalId = setInterval(() => {
        const actionToTake = actions.find(a => a.condition());
        if (actionToTake) {
            actionToTake.action();
        } else {
            clearInterval(intervalId);
            window.cubeRotationX = 0;
            window.cubeRotationY = 0;
            window.cubeRotationZ = 0;
            const cube = $("#cube");
            cube.css('transform', 'rotateX(0deg) rotateY(0deg) rotateZ(0deg)');
            if (callback) setTimeout(callback, 50);
        }
    }, 50);
}

function cubeMoveButton(pageID, destPage) {
    rotateToCubeFace(pageID);
    const xDiv = document.getElementById("contentIframe");
    if (!xDiv) {
        createContentDiv(pageID, destPage);
        finishTransition();
    } else {
        if (xDiv.id === pageID) {
            finishTransition();
            return;
        }
        xDiv.classList.remove("fade-in", "focussed");
        xDiv.classList.add("fade-out");
        setTimeout(() => {
            xDiv.remove();
            createContentDiv(pageID, destPage);
            finishTransition();
        }, 389);
    }
}

function finishTransition() {
    setTimeout(() => {
        isTransitioning = false;
    }, 100); // buffer transition complete
}

function rotateToCubeFace(faceID) {
    switch (faceID) {
        case 'face0':
            sim_up();
            break;
        case 'face1':
            break;
        case 'face2':
            sim_right();
            break;
        case 'face3':
            sim_left();
            sim_left();
            break;
        case 'face4':
            sim_left();
            break;
        case 'face5':
            sim_down();
            break;
    }
}

function createContentDiv(pageID, destPage) {
    const particularDiv = document.getElementById(pageID);
    const destination = `${window.location.origin}/${destPage}`;
    const div = document.createElement("div");
    div.id = "contentIframe";
    div.classList.add("fade-in", "focussed");
    const iframe = document.createElement("iframe");
    iframe.className = "iframe-container";
    iframe.src = destination;
    iframe.frameBorder = "0";
    div.appendChild(iframe);
    particularDiv.appendChild(div);
}

function handleLogoClick() {
    if (!isTransitioning) {
        isTransitioning = true;
        goHome(() => {
            window.cubeRotationX = 0;
            window.cubeRotationY = 0;
            window.cubeRotationZ = 0;
            const cube = $("#cube");
            cube.css('transform', 'rotateX(0deg) rotateY(0deg) rotateZ(0deg)');
            sideCount = 0;
            updCount = 0;
            setTimeout(() => {
                isTransitioning = false;
            }, 100);
        });
    }
}
function getActiveIframe() {
    const focussedDiv = document.querySelector('.focussed');
    return focussedDiv ? focussedDiv.querySelector('iframe') : null;
}

function handleWheel(event) {
    if (isTransitioning) return;
    const now = performance.now();
    if (now - lastScrollTime < scrollThrottle) return;
    lastScrollTime = now;

    const activeIframe = getActiveIframe();
    if (activeIframe && activeIframe.contentWindow) {
        requestAnimationFrame(() => {
            activeIframe.contentWindow.postMessage({ type: 'wheel', deltaY: event.deltaY }, '*');
        });
        event.preventDefault();
    }
}

function handleTouchStart(event) {
    if (isTransitioning) return;
    touchScrolling = true;
    lastTouchY = event.touches[0].clientY;
    lastTouchTime = Date.now();
    scrollVelocity = 0;

    const activeIframe = getActiveIframe();
    if (activeIframe && activeIframe.contentWindow) {
        activeIframe.contentWindow.postMessage({ type: 'touchstart', y: lastTouchY }, '*');
    }
}

function handleTouchMove(event) {
    if (isTransitioning || !touchScrolling) return;

    const currentTouchY = event.touches[0].clientY;
    const deltaY = lastTouchY - currentTouchY;
    const currentTime = Date.now();
    const deltaTime = currentTime - lastTouchTime;

    scrollVelocity = deltaY / deltaTime;

    const activeIframe = getActiveIframe();
    if (activeIframe && activeIframe.contentWindow) {
        requestAnimationFrame(() => {
            activeIframe.contentWindow.postMessage({ type: 'touchmove', deltaY: deltaY }, '*');
        });
    }

    lastTouchY = currentTouchY;
    lastTouchTime = currentTime;

    event.preventDefault();
}

function handleTouchEnd() {
    touchScrolling = false;
    if (Math.abs(scrollVelocity) > 0.1) {
        decelerateScroll();
    }
}

function decelerateScroll() {
    if (Math.abs(scrollVelocity) < 0.01) {
        // Ensure we end on a whole pixel
        const activeIframe = getActiveIframe();
        if (activeIframe && activeIframe.contentWindow) {
            requestAnimationFrame(() => {
                activeIframe.contentWindow.postMessage({ type: 'finalScroll', position: Math.round(scrollPosition) }, '*');
            });
        }
        return;
    }

    const activeIframe = getActiveIframe();
    if (activeIframe && activeIframe.contentWindow) {
        requestAnimationFrame(() => {
            scrollPosition += scrollVelocity * 16; // 16ms is roughly one frame at 60fps
            activeIframe.contentWindow.postMessage({ type: 'scroll', deltaY: scrollVelocity * 16 }, '*');
        });
    }

    scrollVelocity *= 0.95; // Deceleration factor
    requestAnimationFrame(decelerateScroll);
}
