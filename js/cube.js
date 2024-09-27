let sideCount = 0;
let updCount = 0;
let isTransitioning = false;
let lastTouchY;
let lastScrollTime = 0;
let lastWheelDelta = 0;
const scrollThrottle = 16; // ms
let accumulatedDelta = 0;

document.addEventListener('DOMContentLoaded', function () {
    const cube = document.getElementById('cube');

    check3DSupport();
    window.cubeRotationX = 0;
    window.cubeRotationY = 0;
    window.cubeRotationZ = 0;
    window.cubescale = 1;
    window.cubeduration = 250;
    window.cubestate = 2;

    document.addEventListener('keydown', arrowKeyHandler);

    const cubeFaces = document.querySelectorAll('#cube .face');
    cubeFaces.forEach((face, index) => {
        face.setAttribute('aria-label', `Cube face ${index + 1}`);
    });
    setupCubeButtons();
    preloadCubeFaces();

    const logo = document.getElementById('logo_goHome');
    if (logo) {
        logo.addEventListener('click', handleLogoClick);
    } else {
        console.error('Logo element not found');
    }

    window.addEventListener('wheel', handleWheel, { passive: false });
    window.addEventListener('touchstart', handleTouchStart, { passive: true });
    window.addEventListener('touchmove', handleTouchMove, { passive: false });
    window.addEventListener('message', function (event) {
        if (event.data.type === 'contentHeightChanged') {
            updateIframeHeight();
        }
    });
    window.addEventListener('resize', function () {
        setTimeout(updateIframeHeight, 100);
    });
});


function setupCubeButtons() {
    const navButtons = document.querySelectorAll(".navButton .navName");
    navButtons.forEach((button, index) => {
        const customFace = niertoCubeCustomizer.cubeFaces[index];
        if (customFace) {
            button.textContent = customFace.buttonText;
            button.setAttribute("data-face", customFace.facePosition);
            button.setAttribute("data-slug", customFace.urlSlug);
        }
        button.addEventListener('click', handleNavButtonClick);
        button.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                handleNavButtonClick.call(this, event);
            }
        });
    });
}

function handleNavButtonClick(event) {
    if (isTransitioning) return;
    isTransitioning = true;
    const faceId = event.currentTarget.getAttribute('data-face');
    const slug = event.currentTarget.getAttribute('data-slug');
    goHome(() => {
        requestAnimationFrame(() => {
            cubeMoveButton(faceId, slug);
        });
    });
}

function rotateCube(anglex, angley, anglez) {
    const cube = document.getElementById('cube');
    cube.style.transition = `transform ${window.cubeduration}ms`;

    window.cubeRotationX = Math.round(window.cubeRotationX + anglex);
    window.cubeRotationY = Math.round(window.cubeRotationY + angley);
    window.cubeRotationZ = Math.round(window.cubeRotationZ + anglez);

    requestAnimationFrame(() => {
        const newTransform = `rotateX(${window.cubeRotationX}deg) rotateY(${window.cubeRotationY}deg) rotateZ(${window.cubeRotationZ}deg)`;
        cube.style.transform = newTransform;
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
            const cube = document.getElementById('cube');
            cube.style.transform = 'rotateX(0deg) rotateY(0deg) rotateZ(0deg)';
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

function preloadCubeFaces() {
    niertoCubeCustomizer.cubeFaces.forEach(face => {
        if (face.contentType === 'post') {
            fetch(niertoCubeData.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'nierto_cube_ajax',
                    cube_action: 'get_face_content',
                    slug: face.urlSlug,
                    nonce: niertoCubeData.nonce
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const faceElement = document.getElementById(face.facePosition);
                        if (faceElement) {
                            faceElement.setAttribute('data-content', data.data);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error preloading face content:', error);
                });
        }
    });
}


function createContentDiv(pageID, destPage) {
    const particularDiv = document.getElementById(pageID);
    const div = document.createElement("div");
    div.id = "contentIframe";
    div.classList.add("fade-in", "focussed");

    const faceSettings = niertoCubeCustomizer.cubeFaces.find(face => face.urlSlug === destPage);
    if (!faceSettings) {
        console.error(`Face settings not found for slug: ${destPage}`);
        return;
    }

    if (faceSettings.contentType === 'page') {
        const iframe = document.createElement("iframe");
        iframe.className = "iframe-container";
        iframe.src = `${window.location.origin}/${destPage}`;
        div.appendChild(iframe);
    } else {
        const contentDiv = particularDiv.querySelector('div');
        if (contentDiv) {
            contentDiv.style.display = 'block';
            div.appendChild(contentDiv);
        } else {
            // If content is not preloaded, load it now
            fetch(niertoCubeData.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'nierto_cube_get_face_content',
                    slug: destPage,
                    nonce: niertoCubeData.nonce
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        div.innerHTML = data.data;
                    }
                })
                .catch(error => {
                    console.error('Error loading face content:', error);
                });
        }
    }
    particularDiv.appendChild(div);
}

function handleLogoClick() {
    if (!isTransitioning) {
        isTransitioning = true;
        goHome(() => {
            window.cubeRotationX = 0;
            window.cubeRotationY = 0;
            window.cubeRotationZ = 0;
            const cube = document.getElementById('cube');
            cube.style.transform = 'rotateX(0deg) rotateY(0deg) rotateZ(0deg)';
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
    if (now - lastScrollTime < scrollThrottle) {
        lastWheelDelta += event.deltaY;
        event.preventDefault();
        return;
    }

    const deltaY = lastWheelDelta + event.deltaY;
    lastWheelDelta = 0;
    lastScrollTime = now;

    const activeIframe = getActiveIframe();
    if (activeIframe && activeIframe.contentWindow && activeIframe.contentWindow.handleScroll) {
        requestAnimationFrame(() => {
            tunnel(function () {
                activeIframe.contentWindow.handleScroll(deltaY);
            });
        });
        event.preventDefault();
    }
}

function handleTouchStart(event) {
    if (isTransitioning) return;
    lastTouchY = event.touches[0].clientY;
}

function handleTouchMove(event) {
    if (isTransitioning) return;

    const currentTouchY = event.touches[0].clientY;
    const deltaY = lastTouchY - currentTouchY;
    lastTouchY = currentTouchY;

    const now = performance.now();
    if (now - lastScrollTime > scrollThrottle) {
        requestAnimationFrame(() => {
            const activeIframe = getActiveIframe();
            if (activeIframe && activeIframe.contentWindow && activeIframe.contentWindow.handleScroll) {
                tunnel(() => {
                    activeIframe.contentWindow.handleScroll(Math.round(accumulatedDelta + deltaY));
                });
            }
            accumulatedDelta = 0;
            lastScrollTime = now;
        });
    } else {
        accumulatedDelta += deltaY;
    }

    event.preventDefault();
}

function updateIframeHeight() {
    const iframe = document.querySelector('#contentIframe iframe');
    if (iframe && iframe.contentWindow) {
        tunnel(() => {
            const contentHeight = iframe.contentWindow.document.body.scrollHeight;
            iframe.style.height = contentHeight + 'px';
            iframe.contentWindow.postMessage({ type: 'setHeight', height: contentHeight }, '*');
        });
    }
}

function updateScrollPosition(scrollPosition, maxScroll) {
    // Use these values to update any necessary UI elements or perform any required actions
    console.log('Scroll position:', scrollPosition, 'Max scroll:', maxScroll);
}

function setupScrolling(container) {
    container.style.height = '100%';
    container.style.overflowY = 'scroll';
    container.style.scrollbarWidth = 'none';
    container.style.msOverflowStyle = 'none';
    container.addEventListener('wheel', handleWheel, { passive: false });
    container.addEventListener('touchstart', handleTouchStart, { passive: true });
    container.addEventListener('touchmove', handleTouchMove, { passive: false });
}

function check3DSupport() {
    var el = document.createElement('p'),
        has3d,
        transforms = {
            'webkitTransform': '-webkit-transform',
            'OTransform': '-o-transform',
            'msTransform': '-ms-transform',
            'MozTransform': '-moz-transform',
            'transform': 'transform'
        };

    document.body.insertBefore(el, null);

    for (var t in transforms) {
        if (el.style[t] !== undefined) {
            el.style[t] = "translate3d(1px,1px,1px)";
            has3d = window.getComputedStyle(el).getPropertyValue(transforms[t]);
        }
    }

    document.body.removeChild(el);

    if (!(has3d !== undefined && has3d.length > 0 && has3d !== "none")) {
        document.body.innerHTML = '<div style="padding: 20px; background: #f0f0f0; text-align: center;">Please update your browser to view this website properly.</div>';
    }
}