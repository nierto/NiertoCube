let sideCount = 0;
let updCount = 0;
let isTransitioning = false;
let lastTouchY;
let lastScrollTime = 0;
let lastWheelDelta = 0;
const scrollThrottle = 16; // ms
let accumulatedDelta = 0;
let isExpanded = false;
let longPressTimer;
const cube = document.getElementById('cube');
const wrapperLeft = document.getElementById('wrapper_left');
const wrapperRight = document.getElementById('wrapper_right');
// UTILITY MINI FUNCTIONS: (YES, THIS IS HOW MESSY THE COMMENTS WILL BE HENCEFORTH, BRACE YOURSELF BEFORE YOU RACE YOURSELF THRU IT)
function initializeContentInteraction() {
    document.addEventListener('touchstart', handleTouchStart, { passive: true });
    document.addEventListener('touchmove', handleTouchMove, { passive: false });
    document.addEventListener('touchend', handleTouchEnd);
}
function finishTransition() {
    setTimeout(() => {
        isTransitioning = false;
    }, 100); // buffer transition complete
}
function getCurrentContentDiv() {
    return document.querySelector('.cube-face-content');
}
function updateScrollability() {
    const contentDiv = getCurrentContentDiv();
    if (!contentDiv) return;

    if (isExpanded) {
        contentDiv.style.overflowY = 'auto';
    } else {
        contentDiv.style.overflowY = 'hidden';
    }
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
// DOM CONTENT LOADED EVENTLISTENER
document.addEventListener('DOMContentLoaded', function () {
    if (typeof niertoCubeInitialState !== 'undefined') {
        cubeMoveButton(niertoCubeInitialState.facePosition, niertoCubeInitialState.slug);
    }
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
    initializeContentInteraction();
    updateScrollability();
});
// ALL CUBE RELATED FUNCS:
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
// NAVIGATION BUTTONS FUNCS AND DYNAMIC CONTENT CREATION:
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
function createContentDiv(pageID, destPage) {
    const particularDiv = document.getElementById(pageID);
    const div = document.createElement("div");
    div.id = "contentIframe";
    div.classList.add("fade-in", "focussed", "cube-face-content");

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
        fetch(`/wp-json/wp/v2/cube_face?slug=${destPage}`)
            .then(response => response.json())
            .then(posts => {
                if (posts && posts.length > 0) {
                    const post = posts[0];
                    const template = post.meta._cube_face_template[0] || 'standard';

                    switch (template) {
                        case 'multi_post':
                            return fetch('/wp-json/wp/v2/posts?per_page=6')
                                .then(response => response.json())
                                .then(posts => {
                                    const content = posts.map(post => `
                                        <div class="multi-post-item">
                                            <h2>${post.title.rendered}</h2>
                                            <div>${post.excerpt.rendered}</div>
                                        </div>
                                    `).join('');
                                    div.innerHTML = content;
                                });
                        case 'settings':
                            div.innerHTML = `
                                <h1>Settings</h1>
                                <button onclick="clearLocalData()">Clear Local Data</button>
                                <!-- Add more settings options here -->
                            `;
                            break;
                        default:
                            div.innerHTML = `
                                <h1>${post.title.rendered}</h1>
                                <div class="entry-content">${post.content.rendered}</div>
                            `;
                    }
                } else {
                    div.innerHTML = '<p>Content not found.</p>';
                }
            })
            .catch(error => {
                console.error('Error loading face content:', error);
                div.textContent = 'Error loading content.';
            })
            .finally(() => {
                particularDiv.appendChild(div);
                initializeContentInteraction(); // Initialize touch events for the new content
                updateScrollability(); // Ensure proper scrolling behavior
            });
    }
}
function preloadCubeFaces() {
    niertoCubeCustomizer.cubeFaces.forEach(face => {
        if (face.contentType !== 'page') {  // Changed from 'post' to check if it's not a page
            fetch(niertoCubeData.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'nierto_cube_ajax',
                    cube_action: 'get_face_content',
                    slug: face.urlSlug,
                    post_type: face.contentType,  // Added post_type to the request
                    nonce: niertoCubeData.nonce
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const faceElement = document.getElementById(face.facePosition);
                        if (faceElement) {
                            faceElement.setAttribute('data-content', JSON.stringify(data.data));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error preloading face content:', error);
                });
        }
    });
}
// ZOOM CONTENT FUNCS:
function toggleContentExpansion() {
    const contentDiv = getCurrentContentDiv();
    if (!contentDiv) return;

    isExpanded = !isExpanded;
    contentDiv.classList.toggle('expanded');
    cube.classList.toggle('minimized');
    wrapperLeft.classList.toggle('hidden');
    wrapperRight.classList.toggle('hidden');

    if (isExpanded) {
        contentDiv.style.width = niertoCubeSettings.maxZoom + 'vw';
        contentDiv.style.height = niertoCubeSettings.maxZoom + 'vh';
    } else {
        contentDiv.style.width = '';
        contentDiv.style.height = '';
    }

    updateScrollability();
}
// LOGO LOGIC FUNCS:
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
// IFRAME RELATED FUNCS:
function getActiveIframe() {
    const focussedDiv = document.querySelector('.focussed');
    return focussedDiv ? focussedDiv.querySelector('iframe') : null;
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
//SCROLLING FUNCS:
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
function handleWheel(event) {
    if (isTransitioning || isExpanded) return;

    const now = performance.now();
    if (now - lastScrollTime < scrollThrottle) {
        lastWheelDelta += event.deltaY;
        event.preventDefault();
        return;
    }

    const deltaY = (lastWheelDelta + event.deltaY) * 1.25;
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
function handleTouchStart(e) {
    if (isTransitioning) return;
    lastTouchY = e.touches[0].clientY;

    const contentDiv = getCurrentContentDiv();
    if (!contentDiv) return;

    if (!e.target.closest('button, a, input, textarea, select')) {
        longPressTimer = setTimeout(() => {
            toggleContentExpansion();
        }, niertoCubeSettings.longPressDuration);
    }
}
function handleTouchMove(e) {
    if (isTransitioning) return;
    clearTimeout(longPressTimer);

    const currentTouchY = e.touches[0].clientY;
    const deltaY = (lastTouchY - currentTouchY) * 1.25;
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

    e.preventDefault();
}
function handleTouchEnd(e) {
    clearTimeout(longPressTimer);
}

