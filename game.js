let rows = 9; // Default rows for Easy
let cols = 9; // Default cols for Easy
let mineCount = 10; // Default mine count
let board = [];
let timerInterval;
let gameStarted = false;
let gameOver = false;
let remainingCells;
let correctlyFlagged = 0; // Track correctly flagged mines
let difficulty = "easy";
let alertShown = false;
let elapsedTime = 0; // Declare elapsedTime globally
let stepCount = 0;

const boardElement = document.getElementById("game-board");
const mineInputElement = document.getElementById("mine-count");
const timerDisplay = document.getElementById("timer");
const startButton = document.getElementById("start-button");
const restartButton = document.getElementById("restart-button");
const boardSizeSelector = document.getElementById("board-size");
const flagToggle = document.getElementById("flag-toggle");

// Timer
function resetTimerDisplay() {
    timerDisplay.textContent = "Time: 0";
}

function startTimer() {
    stopTimer(); // Clear any previous timer
    elapsedTime = 0; // Reset the global elapsedTime
    resetTimerDisplay();
    timerInterval = setInterval(() => {
        elapsedTime++; // Update the global elapsedTime
        timerDisplay.textContent = `Time: ${elapsedTime}`;
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

    mineInputElement.max = rows * cols - 1;
    mineInputElement.value = mineCount;
    if (gameStarted) {
        restartGame();
    }
});

// Mine Count Input
mineInputElement.addEventListener("change", (e) => {
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
    alertShown = false;
    remainingCells = rows * cols - mineCount;
    correctlyFlagged = 0; // Reset correctly flagged mines

    startTimer();

    generateBoard();
    renderBoard();

    restartButton.disabled = false;
    //console.log("Game started!");
}

function restartGame() {
    gameStarted = false;
    gameOver = false;
    stopTimer();
    resetTimerDisplay();
    boardElement.innerHTML = ""; // Clear the board
    flagToggle.checked = false; // Reset flag mode
    alertShown = false;
    //console.log("Game restarted!");
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
    //console.log("Board generated!");
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

    //console.log("Board rendered!");
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

    stepCount++;

    board[row][col].revealed = true;
    const cell = document.querySelector(`.cell[data-row="${row}"][data-col="${col}"]`);
    cell.classList.add("revealed");

    if (board[row][col].mine) {
        cell.textContent = "💣";
        //console.log("Game lost! Mine revealed.");
        endGame(false); // End game on mine reveal
        setTimeout(restartGame, 2000); // Restart after 2 seconds
        return;
    }

    // Show the number of adjacent mines, or an empty space
    cell.textContent = board[row][col].adjacentMines || "";
    remainingCells--;

    checkGameStatus(); // Check the win/loss status after each move

    // If there are no adjacent mines, recursively reveal surrounding cells
    if (board[row][col].adjacentMines === 0) {
        for (let dr = -1; dr <= 1; dr++) {
            for (let dc = -1; dc <= 1; dc++) {
                const nr = row + dr;
                const nc = col + dc;
                // Ensure we stay within bounds
                if (nr >= 0 && nr < rows && nc >= 0 && nc < cols) {
                    // Skip the current cell
                    if (nr === row && nc === col) continue;
                    // Recursively reveal surrounding cells
                    revealCell(nr, nc);
                }
            }
        }
    }
}

function checkGameStatus() {
    if (remainingCells === 0 && correctlyFlagged === mineCount) {
        // All cells revealed and all mines flagged correctly
        endGame(true); // End game on win
    }
}

function toggleFlag(row, col) {
    if (gameOver || board[row][col].revealed) return;

    stepCount++;

    board[row][col].flagged = !board[row][col].flagged;
    const cell = document.querySelector(`.cell[data-row="${row}"][data-col="${col}"]`);
    cell.textContent = board[row][col].flagged ? "🚩" : "";

    if (board[row][col].flagged && board[row][col].mine) {
        correctlyFlagged++;
    } else if (!board[row][col].flagged && board[row][col].mine) {
        correctlyFlagged--;
    }

    checkGameStatus(); // Check if all mines are flagged correctly

    if (correctlyFlagged === mineCount && remainingCells === 0) {
        //console.log("Game won! All mines correctly flagged.");
        endGame(true); // End game on correct flags
    }
}

function revealAllMines() {
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const cell = document.querySelector(`.cell[data-row="${r}"][data-col="${c}"]`);
            if (board[r][c].mine && !board[r][c].flagged) {
                cell.textContent = "💣";
                cell.classList.add("mine");
            } else if (!board[r][c].mine && board[r][c].flagged) {
                cell.classList.add("wrong-flag");
            }
        }
    }
}

function endGame(won) {
    if (alertShown) return;
    alertShown = true;

    gameOver = true;
    stopTimer();

    revealAllMines();
    alert(won ? "You win!" : "You lose!");

    //console.log("End game called. Saving history...");
    saveGameHistory(won, elapsedTime, difficulty, stepCount); // Pass stepCount here
}
