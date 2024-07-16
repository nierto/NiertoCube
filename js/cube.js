const MOBILE_STATE = window.matchMedia("(min-width: 961px)").matches ? 0 : 1;
let sideCount = 0;
let updCount = 0;
let isTransitioning = false;

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof variables !== 'undefined') {
        setupCubeButtons();
    } else {
        console.error('Required variables are not defined.');
    }

    // Initialize other functionalities
    window.cubescale = 1;
    window.cubeduration = 250;
    window.cubestate = 2;
    document.addEventListener('keydown', arrowKeyHandler);
    
    // Set up button event listeners with debounce
    document.querySelectorAll('.navButton .navName').forEach(button => {
        button.addEventListener('click', debounce(function (event) {
            if (isTransitioning) return;
            const faceId = event.target.getAttribute('data-face');
            const slug = event.target.getAttribute('data-slug');
            cubeMoveButton(faceId, slug);
        }, 250)); // 250ms debounce
    });
});

function rotateCube(anglex, angley, anglez) {
    const cube = $("#cube");
    cube.css('transition', `transform ${window.cubeduration}ms`);
    const currentTransform = cube.css('transform');
    const newRotation = `rotateX(${anglex}deg) rotateY(${angley}deg) rotateZ(${anglez}deg)`;
    const finalTransform = currentTransform === 'none' ? newRotation : `${currentTransform} ${newRotation}`;
    cube.css('transform', finalTransform);
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
            if (callback) setTimeout(callback, 100);
        }
    }, 100);
}

function cubeMoveButton(pageID, destPage) {
    if (isTransitioning) return; // Ignore clicks if a transition is in progress
    isTransitioning = true;

    goHome(() => {
        setTimeout(() => {
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
                xDiv.classList.remove("fade-in");
                xDiv.classList.add("fade-out");
                setTimeout(() => {
                    xDiv.remove();
                    createContentDiv(pageID, destPage);
                    finishTransition();
                }, 500);
            }
        }, 100);
    });
}

function finishTransition() {
    setTimeout(() => {
        isTransitioning = false;
    }, 100); // Add a small buffer after the transition completes
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
    div.classList.add("fade-in");
    div.innerHTML = `<iframe class="iframe-container" src=${destination} frameBorder="0"></iframe>`;
    particularDiv.appendChild(div);
}

function handleLogoClick() {
    goHome(() => {
        // Additional actions can be added here if needed after the cube has returned home, anybody have an idea what to add?
    });
}