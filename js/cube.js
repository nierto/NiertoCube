const MOBILE_STATE = window.matchMedia("(min-width: 961px)").matches ? 0 : 1;
let sideCount = 0;
let updCount = 0;

document.addEventListener('DOMContentLoaded', function () {
    if (typeof variables !== 'undefined') {
        updateNavButtons();
    } else {
        console.error('Required variables are not defined.');
    }

    // Initialize other functionalities after DOM is loaded
    window.cubescale = 1;
    window.cubeduration = 250;
    window.cubestate = 2;
    document.addEventListener('keydown', arrowKeyHandler);
});

function updateNavButtons() {
    const navButtons = document.querySelectorAll('.navButton .navName');
    navButtons.forEach((button, index) => {
        const faceId = `face${index}`;
        if (variables.navTexts[faceId]) {
            button.textContent = variables.navTexts[faceId];
        } else {
            console.error(`Variable ${faceId} not found.`);
        }
    });
}

function rotateCube(anglex, angley, anglez) {
    const cube = $("#cube");
    const oldMatrix = new WebKitCSSMatrix(cube[0].style.webkitTransform);
    const extrarotate = new WebKitCSSMatrix().rotate(anglex, angley, anglez);
    const final = extrarotate.multiply(oldMatrix);
    const finalString = Array.from({ length: 4 }, (_, i) =>
        Array.from({ length: 4 }, (_, j) => Math.round(final[`m${i + 1}${j + 1}`]))
    ).flat();
    cube[0].style.webkitTransform = `matrix3d(${finalString.join(",")})`;
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

function resetCount() {
    if (sideCount >= 4 || sideCount <= -4) {
        sideCount = 0;
    }
    if (updCount >= 4 || updCount <= -4) {
        updCount = 0;
    }
}

function sim_up() {
    if (updCount < 1 && sideCount === 0) {  // Allow rotation up if not at maximum up rotation and only if sideCount is 0
        updCount += 1;
        rotateCube(-90, 0, 0);
        resetCount();
    }
}

function sim_down() {
    if (updCount > -1 && sideCount === 0) {  // Allow rotation down if not at maximum down rotation and only if sideCount is 0
        updCount -= 1;
        rotateCube(90, 0, 0);
        resetCount();
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
    const simActions = [
        { condition: updCount > 0, action: sim_down },
        { condition: updCount < 0, action: sim_up },
        { condition: sideCount < 0, action: sim_left },
        { condition: sideCount > 0, action: sim_right },
    ];
    simActions.forEach(({ condition, action }) => condition && action());
    if (sideCount === 0 && updCount === 0) {
        if (callback) callback(); // Call the callback function if provided
    } else {
        setTimeout(() => goHome(callback), 100); // Retry until the cube is home
    }
}

function cubeMoveButton(pageID, destPage) {
    goHome(() => {
        const xDiv = document.getElementById("contentIframe");
        if (!xDiv) {
            createContentDiv(pageID, destPage);
        } else {
            if (xDiv.id === pageID) return;
            xDiv.classList.remove("fade-in");
            setTimeout(() => {
                xDiv.remove();
                createContentDiv(pageID, destPage);
            }, 750);
        }
    });
}

function callCubeMoveButton(variableName) {
    const value = variables[variableName];
    if (value !== undefined) {
        cubeMoveButton(variableName, value);
    } else {
        console.error(`Variable ${variableName} not found.`);
    }
}

function createContentDiv(pageID, destPage) {
    const particularDiv = document.getElementById(pageID);
    const destination = `${window.location.origin}/${destPage}`;
    const div = document.createElement("div");
    div.id = "contentIframe";
    div.classList.add("scrollableIOS", "fade-in");
    div.innerHTML = `<iframe class="iframe-container" src=${destination} frameBorder="0"></iframe>`;
    particularDiv.appendChild(div);
    switchToContent(pageID);
}

function switchToContent(pageID) {
        const actions = {
            face0: () => { sim_up(); showLogo(); },
            face1: () => { sim_right(); showLogo(); },
            face2: () => { sim_left(); sim_left(); showLogo(); },
            face3: () => { sim_left(); showLogo(); },
            face4: () => { sim_down(); showLogo(); },
            face5: () => { sim_right(); sim_right(); showLogo(); },
        };
        actions[pageID]?.();
}

function showLogo() {
    if (MOBILE_STATE === 0) return;
    const targetNode = document.getElementById("logoWrapper");
    targetNode.innerHTML = '<img id="logo_top" class="accelerated" src="" onclick="handleLogoClick()" width="124px" height="124px">';
    targetNode.classList.toggle("invisible", sideCount === 0 && updCount === 0);
    targetNode.classList.toggle("leftcorner", sideCount !== 0 || updCount !== 0);
}

function handleLogoClick() {
    goHome(() => {
        // Additional actions can be added here if needed after the cube has returned home
    });
}