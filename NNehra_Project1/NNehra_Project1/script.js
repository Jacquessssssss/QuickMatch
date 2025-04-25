// Function to toggle the mobile navigation menu
function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

document.addEventListener("DOMContentLoaded", function () {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', toggleMenu);
    }

    const playLink = document.querySelector('a[href="#play-section"]');
    if (playLink) {
        playLink.addEventListener('click', function(event) {
            event.preventDefault();
            const playSection = document.getElementById('play-section');
            if (playSection) {
                const yOffset = -100;
                const y = playSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({top: y, behavior: 'smooth'});
            }
        });
    }
});
//load the header and the footer
function loadComponent(id, file) {
    fetch(file)
        .then(response => response.text())
        .then(data => {
            document.getElementById(id).innerHTML = data;
        })
        .catch(error => console.error(`Error loading ${file}:`, error));
}

window.addEventListener("DOMContentLoaded", function () {
    loadComponent("header", "header.html");
    loadComponent("footer", "footer.html");
});


// javascript for form validation

const form = document.getElementById('form');
const firstname_input = document.getElementById('firstname-input');
const email_input = document.getElementById('email-input');
const password_input = document.getElementById('password-input');
const repeat_password_input = document.getElementById('repeat-password-input');
const error_message = document.getElementById('error-message');

const allInputs = [firstname_input, email_input, password_input, repeat_password_input].filter(input => input != null);
allInputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.parentElement.classList.remove('incorrect');
        error_message.innerText = '';
    });
});

if (form) {
    form.addEventListener('submit', (e) => {
        //e.preventDefault();
        let errors = firstname_input ? getSignupFormErrors(firstname_input.value, email_input.value, password_input.value, repeat_password_input.value) : getLoginFormErrors(email_input.value, password_input.value);
        if (errors.length > 0) {
            error_message.innerHTML = errors.join(". ");
        } else {
            form.submit();
        }
    });
}

function getSignupFormErrors(firstname, email, password, repeatPassword) {
    let errors = [];
    if (!firstname) errors.push('Firstname is required');
    if (!email) errors.push('Email is required');
    if (!password) errors.push('Password is required');
    if (password.length < 8) errors.push('Password must have at least 8 characters');
    if (password !== repeatPassword) errors.push("Password does not match repeated password");
    return errors;
}

function getLoginFormErrors(email, password) {
    let errors = [];
    if (!email) errors.push('Email is required');
    if (!password) errors.push('Password is required');
    return errors;
}






// games logic 

const gridContainer = document.querySelector(".grid-container");
let cards = [];
let firstCard, secondCard;
let lockBoard = false;
let matchedPairs = 0;
const totalPairs = 8;
let timeLeft = 60;
let timerInterval;
let attempts = 0;

fetch("data/cards.json")
    .then((res) => res.json())
    .then((data) => {
        cards = [...data, ...data].slice(0, 16); 
        setupGame();
    });

    let gameId = 1; // default to timed
    let scoreToSend = 0;

    if (window.location.href.includes("timedGame")) {
        gameId = 1;
    } else if (window.location.href.includes("freeGame")) {
        gameId = 2;
    } else if (window.location.href.includes("guess")) {
        gameId = 3;
    }

    


function setupGame() {
    shuffleCards();
    generateCards();
    
    if (document.querySelector(".timer")) {
        startTimer();
    }
}

function shuffleCards() {
    let currentIndex = cards.length, randomIndex;
    while (currentIndex !== 0) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;
        [cards[currentIndex], cards[randomIndex]] = [cards[randomIndex], cards[currentIndex]];
    }
}

function generateCards() {
    gridContainer.innerHTML = "";
    matchedPairs = 0;
    attempts = 0;
    document.querySelector(".score").textContent = attempts;
    
    for (let card of cards) {
        const cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.setAttribute("data-name", card.name);
        cardElement.innerHTML = `
            <div class="front">
                <img class="front-image" src="${card.image}" />
            </div>
            <div class="back"></div>
        `;
        gridContainer.appendChild(cardElement);
        cardElement.addEventListener("click", flipCard);
    }
}

function flipCard() {
    if (lockBoard || this === firstCard) return;
    this.classList.add("flipped");

    if (!firstCard) {
        firstCard = this;
        return;
    }
    
    secondCard = this;
    lockBoard = true;
    attempts++;
    document.querySelector(".score").textContent = attempts;
    
    checkForMatch();
}

function checkForMatch() {
    let isMatch = firstCard.dataset.name === secondCard.dataset.name;
    isMatch ? disableCards() : unflipCards();
}

function disableCards() {
    firstCard.removeEventListener("click", flipCard);
    secondCard.removeEventListener("click", flipCard);
    matchedPairs++;
    resetBoard();

    if (matchedPairs === totalPairs) {
        clearInterval(timerInterval);
        showModal(true);
    }
}

function unflipCards() {
    setTimeout(() => {
        firstCard.classList.remove("flipped");
        secondCard.classList.remove("flipped");
        resetBoard();
    }, 1000);
}

function resetBoard() {
    firstCard = null;
    secondCard = null;
    lockBoard = false;
}

function startTimer() {
    timeLeft = 90;
    document.querySelector(".timer").textContent = timeLeft;
    
    timerInterval = setInterval(() => {
        timeLeft--;
        document.querySelector(".timer").textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            showModal(false);
        }
    }, 1000);
}

function showModal(isWin) {
    const modalTitle = document.getElementById("gameModalLabel");
    const modalMessage = document.getElementById("gameMessage");

    if (isWin) {
        modalTitle.textContent = "Congratulations!";
        modalMessage.textContent = `You won in ${90 - timeLeft} seconds with ${attempts} attempts!`;

        // Define score based on game type
        if (gameId === 1) {
            scoreToSend = 90 - timeLeft;  // Timed Game
        } else if (gameId === 2) {
            scoreToSend = attempts;       // Attempts Game
        } else if (gameId === 3) {
            scoreToSend = 1;              // Mystery Game (just a win)
        }

        saveScore(scoreToSend, gameId); // Save with AJAX
    } else {
        modalTitle.textContent = "Time's Up!";
        modalMessage.textContent = "You ran out of time! Try again.";
    }

    new bootstrap.Modal(document.getElementById("gameModal")).show();
}



function restart() {
    resetBoard();
    clearInterval(timerInterval);
    setupGame();
}


function setupSearch(inputId, tableIndex) {
    const input = document.getElementById(inputId);
    const tables = document.querySelectorAll(".table-group-divider");
    const rows = tables[tableIndex].getElementsByTagName("tr");

    input.addEventListener("keyup", function () {
        const filter = input.value.toLowerCase();

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName("td");
            if (cells.length > 1) {
                const name = cells[0].textContent.toLowerCase();
                const email = cells[1].textContent.toLowerCase();
                rows[i].style.display = name.includes(filter) || email.includes(filter) ? "" : "none";
            }
        }
    });
}

// Attach search to each table: [0] = Timed, [1] = Attempts, [2] = Mystery
setupSearch("search-time", 0);
setupSearch("search-attempts", 1);
setupSearch("search-mystery", 2);

