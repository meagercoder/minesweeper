let rows = 9; // Default rows for Easy
let cols = 9; // Default cols for Easy
let mineCount = 10; // Default mine count
let board = [];
let timerInterval;
let gameStarted = false;
let gameOver = false;
let remainingCells;
let correctlyFlagged = 0;
let elapsedTime = 0;
let difficulty = "easy";

const boardElement = document.getElementById("game-board");
const mineInput = document.getElementById("mine-count");
const timerDisplay = document.getElementById("timer");
const startButton = document.getElementById("start-button");
const restartButton = document.getElementById("restart-button");
const boardSizeSelector = document.getElementById("board-size");
const flagToggle = document.getElementById("flag-toggle");

// Timer
function startTimer() {
    stopTimer(); // Clear any previous timer
    elapsedTime = 0;
    timerDisplay.textContent = `Time: ${elapsedTime}`;
    timerInterval = setInterval(() => {
        timerDisplay.textContent = `Time: ${++elapsedTime}`;
    }, 1000);
}

function stopTimer() {
    clearInterval(timerInterval);
}

// Board Size Selector
boardSizeSelector.addEventListener("change", (e) => {
    const size = e.target.value;

    if (size === "easy") {
        rows = 9;
        cols = 9;
        mineCount = 10;
        difficulty = "easy";
    } else if (size === "medium") {
        rows = 16;
        cols = 16;
        mineCount = 40;
        difficulty = "medium";
    } else if (size === "difficult") {
        rows = 16;
        cols = 30;
        mineCount = 99;
        difficulty = "difficult";
    }

    mineInput.max = rows * cols - 1;
    mineInput.value = mineCount;
    if (gameStarted) {
        restartGame();
    }
});

// Mine Count Input
mineInput.addEventListener("change", (e) => {
    const input = parseInt(e.target.value, 10);
    mineCount = Math.max(1, Math.min(input, rows * cols - 1));
    e.target.value = mineCount;
});

// Start Button
startButton.addEventListener("click", () => {
    if (gameStarted) return;
    startGame();
});

// Restart Button
restartButton.addEventListener("click", () => {
    restartGame();
});

// Game Logic
function startGame() {
    gameStarted = true;
    gameOver = false;
    remainingCells = rows * cols - mineCount;
    correctlyFlagged = 0;

    startTimer();

    generateBoard();
    renderBoard();

    restartButton.disabled = false;
}

function restartGame() {
    gameStarted = false;
    stopTimer();
    elapsedTime = 0;
    timerDisplay.textContent = "Time: 0";  // Reset timer display
    boardElement.innerHTML = "";
    //startGame();
}

function generateBoard() {
    board = Array.from({ length: rows }, () =>
        Array(cols).fill(null).map(() => ({
            revealed: false,
            mine: false,
            adjacentMines: 0,
            flagged: false,
        }))
    );

    let minesPlaced = 0;
    while (minesPlaced < mineCount) {
        const row = Math.floor(Math.random() * rows);
        const col = Math.floor(Math.random() * cols);
        if (!board[row][col].mine) {
            board[row][col].mine = true;
            minesPlaced++;
        }
    }

    calculateAdjacentMines();
}

function calculateAdjacentMines() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            if (board[r][c].mine) continue;

            let count = 0;
            for (let dr = -1; dr <= 1; dr++) {
                for (let dc = -1; dc <= 1; dc++) {
                    const nr = r + dr;
                    const nc = c + dc;
                    if (nr >= 0 && nr < rows && nc >= 0 && nc < cols && board[nr][nc].mine) {
                        count++;
                    }
                }
            }
            board[r][c].adjacentMines = count;
        }
    }
}

function renderBoard() {
    boardElement.innerHTML = "";
    boardElement.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
    boardElement.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const cell = document.createElement("div");
            cell.classList.add("cell");
            cell.dataset.row = r;
            cell.dataset.col = c;

            cell.addEventListener("click", () => handleCellClick(r, c));
            cell.addEventListener("contextmenu", (e) => {
                e.preventDefault();
                toggleFlag(r, c);
            });

            boardElement.appendChild(cell);
        }
    }
}

function handleCellClick(row, col) {
    if (flagToggle.checked) {
        toggleFlag(row, col);
    } else {
        revealCell(row, col);
    }
}

function revealCell(row, col) {
    if (gameOver || board[row][col].revealed || board[row][col].flagged) return;

    board[row][col].revealed = true;
    const cell = document.querySelector(`.cell[data-row="${row}"][data-col="${col}"]`);
    cell.classList.add("revealed");

    if (board[row][col].mine) {
        cell.textContent = "💣";
        endGame(false);
        return;
    }

    cell.textContent = board[row][col].adjacentMines || "";
    remainingCells--;

    if (remainingCells === 0) {
        endGame(true);
    }

    if (board[row][col].adjacentMines === 0) {
        for (let dr = -1; dr <= 1; dr++) {
            for (let dc = -1; dc <= 1; dc++) {
                const nr = row + dr;
                const nc = col + dc;
                if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
                    revealCell(nr, nc);
                }
            }
        }
    }
}

function toggleFlag(row, col) {
    if (gameOver || board[row][col].revealed) return;

    board[row][col].flagged = !board[row][col].flagged;
    const cell = document.querySelector(`.cell[data-row="${row}"][data-col="${col}"]`);
    cell.textContent = board[row][col].flagged ? "🚩" : "";

    if (board[row][col].flagged && board[row][col].mine) {
        correctlyFlagged++;
    } else if (!board[row][col].flagged && board[row][col].mine) {
        correctlyFlagged--;
    }

    if (correctlyFlagged === mineCount && remainingCells === 0) {
        endGame(true);
    }
}

function endGame(won) {
    gameOver = true;
    stopTimer();

    const score = won ? remainingCells * 10 : 0; // Example scoring formula
    alert(won ? "You win!" : "You lose!");

    // Save the score to the database
    saveScore(score, elapsedTime, difficulty);
}

function saveScore(score, timePlayed, difficulty) {
    fetch('save_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `score=${score}&time_played=${timePlayed}&difficulty=${difficulty}`
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            console.log("Score saved successfully.");
        } else {
            console.error("Error saving score:", data);
        }
    })
    .catch(error => console.error("Network error:", error));
}
