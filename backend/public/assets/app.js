/* ---------- Параметры ---------- */
const minDelay = 2000;   // ms
const maxDelay = 3000;   // ms
const responseTimeout = 5000; // ms после появления probe — ждать ответ

/* ---------- State ---------- */
let trialIndex = 0;
let running = false;
let pairOrder = [];      // список индексов пар, будет 0..images.length-1
let current = null;      // {pairIdx, anxiousSide: 'left'|'right', neutralSide, probeSide: 'anxious'|'neutral'}
let probeShownAt = 0;
let responseTimer = null;
let showTimer = null;

/* Результаты */
let results = []; // {trial, pairIdx, anxiousSide, probeOn, actualProbeSide ('left'|'right'), clickedSide, correct, rt}

/* Elements */
const leftBox = document.getElementById('leftBox');
const rightBox = document.getElementById('rightBox');
const leftImg = document.getElementById('leftImg');
const rightImg = document.getElementById('rightImg');
const probeLeft = document.getElementById('probeLeft');
const probeRight = document.getElementById('probeRight');

const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const exportBtn = document.getElementById('exportBtn');
const probeTarget = document.getElementById('probeTarget');
const probeIcon = document.getElementById('probeIcon');
const status = document.getElementById('status');
const trialInfo = document.getElementById('trialInfo');
const logPre = document.getElementById('log');
const csvOut = document.getElementById('csvOut');
const stage = document.getElementById('stage');

/* ---------- Инициализация ---------- */
function resetState() {
    trialIndex = 0;
    running = false;
    current = null;
    probeShownAt = 0;
    clearTimers();
    results = [];
    logPre.textContent = '—';
    updateUI();
}

function clearTimers() {
    if (showTimer) {
        clearTimeout(showTimer);
        showTimer = null;
    }
    if (responseTimer) {
        clearTimeout(responseTimer);
        responseTimer = null;
    }
}

/* ---------- UI ---------- */
function updateUI() {
    trialInfo.textContent = `${trialIndex} / ${images.length}`;
    startBtn.disabled = running;
    stopBtn.disabled = !running;
    exportBtn.disabled = results.length === 0;
}

/* ---------- Основная логика ---------- */

startBtn.addEventListener('click', () => {

    stage.classList.remove('d-none');

    if (!images || !images.length) {
        alert('Нет данных изображений!');
        return;
    }
    // подготовим порядок (по порядку). Если нужно случайное перемешивание — замените на shuffle.
    pairOrder = [...Array(images.length).keys()];
    trialIndex = 0;
    running = true;
    results = [];
    updateUI();
    nextTrial();
});

stopBtn.addEventListener('click', () => {
    running = false;
    clearTimers();
    hideProbe();
    status.textContent = 'Остановлено пользователем.';
    updateUI();
});

exportBtn.addEventListener('click', () => {
    const csv = makeCsv(results);
    csvOut.value = csv;
    // скачать как файл
    const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'dotprobe_results.csv';
    a.click();
    URL.revokeObjectURL(url);
});

/* Функция следующего раунда */
function nextTrial() {
    clearTimers();
    hideProbe();

    if (!running) return;
    if (trialIndex >= pairOrder.length) {
        finishRun();
        return;
    }

    const pairIdx = pairOrder[trialIndex];
    const pair = images[pairIdx];      // [anxious, neutral]
    // случайно назначаем anxious слева/справа
    const anxiousOnLeft = Math.random() < 0.5;
    const anxiousSide = anxiousOnLeft ? 'left' : 'right';
    const neutralSide = anxiousOnLeft ? 'right' : 'left';

    // Запомним текущее состояние
    current = {
        pairIdx,
        anxiousSide,
        neutralSide,
        probeOn: null,        // 'anxious' или 'neutral'
        actualProbeSide: null // 'left'|'right'
    };

    // Поставим src в зависимости от расположения
    if (anxiousOnLeft) {
        leftImg.src = pair[0];   // anxious
        rightImg.src = pair[1];  // neutral
    } else {
        leftImg.src = pair[1];   // neutral
        rightImg.src = pair[0];  // anxious
    }

    trialIndex++;
    updateUI();
    status.textContent = 'Пара загружена — ждем появления смайлика...';

    // случайная задержка 2000-3000 ms
    const delay = minDelay + Math.floor(Math.random() * (maxDelay - minDelay + 1));
    showTimer = setTimeout(() => {
        // определить, где показать probe: по настройке (anxious/neutral/random)
        const targetSetting = probeTarget.value; // 'anxious'|'neutral'|'random'
        let target = targetSetting;
        if (targetSetting === 'random') {
            target = (Math.random() < 0.5) ? 'anxious' : 'neutral';
        }
        current.probeOn = target;
        current.actualProbeSide = (target === 'anxious') ? current.anxiousSide : current.neutralSide;
        showProbe(current.actualProbeSide);
        probeShownAt = performance.now();

        // включаем таймаут ожидания клика
        responseTimer = setTimeout(() => {
            // no response within time
            recordResponse(null, null, true); // missed
            // перейти к следующему через короткую паузу
            setTimeout(nextTrial, 800);
        }, responseTimeout);
    }, delay);
}

/* Показать probe на стороне 'left' или 'right' */
function showProbe(side) {
    hideProbe();
    const icon = probeIcon.value || '⭐';
    if (side === 'left') {
        probeLeft.textContent = icon;
        probeLeft.style.display = 'block';
    } else {
        probeRight.textContent = icon;
        probeRight.style.display = 'block';
    }
    status.textContent = `Probe показан на ${side}. Ожидаем ответ...`;
}

/* Спрятать probe */
function hideProbe() {
    probeLeft.style.display = 'none';
    probeRight.style.display = 'none';
    probeLeft.textContent = '';
    probeRight.textContent = '';
}

/* Клики по контейнерам — измеряем RT и correctness */
leftBox.addEventListener('click', () => onUserClick('left'));
rightBox.addEventListener('click', () => onUserClick('right'));

function onUserClick(sideClicked) {
    if (!current || !running) return;
    // игнорируем клики до появления probe
    if (!probeVisible()) return;

    const rt = Math.round(performance.now() - probeShownAt);
    // остановить таймер
    clearTimers();

    recordResponse(sideClicked, rt, false);
    // перейти к следующему раунду через короткую паузу
    setTimeout(nextTrial, 600);
}

function probeVisible() {
    return (probeLeft.style.display !== 'none') || (probeRight.style.display !== 'none');
}

/* Записать результат одного trial */
function recordResponse(sideClicked, rtMs, missed) {
    const actual = current.actualProbeSide; // 'left'|'right'
    const clicked = missed ? null : sideClicked;
    const correct = !missed && (clicked === actual);

    const row = {
        trial: trialIndex,
        pairIdx: current.pairIdx,
        anxiousSide: current.anxiousSide,
        probeOn: current.probeOn,          // 'anxious'|'neutral'
        actualProbeSide: actual,
        clickedSide: clicked,
        correct: correct,
        rt: missed ? null : rtMs,
        timestamp: Date.now()
    };
    results.push(row);
    appendLog(row);
    updateUI();
}

/* Добавить в лог отображаемый */
function appendLog(row) {
    // выводим последние 10
    const last = results.slice(-10).reverse();
    let s = last.map(r => {
        return `#${r.trial} pair${r.pairIdx} probe=${r.probeOn}(${r.actualProbeSide}) click=${r.clickedSide || 'MISS'} rt=${r.rt || '-'}`;
    }).join("\n");
    logPre.textContent = s || '—';
    status.textContent = row.clickedSide ? `Нажали: ${row.clickedSide}, ${row.rt} ms — ${row.correct ? 'правильно' : 'неправильно'}` : 'Нет ответа (miss)';
}

/* Завершение */
function finishRun() {
    running = false;
    clearTimers();
    hideProbe();
    updateUI();
    status.textContent = `Готово. Всего записано: ${results.length} результатов.`;
    exportBtn.disabled = results.length === 0 ? true : false;
}

/* Создать CSV */
function makeCsv(arr) {
    const header = ['trial', 'pairIdx', 'anxiousSide', 'probeOn', 'actualProbeSide', 'clickedSide', 'correct', 'rt_ms', 'timestamp'];
    const lines = [header.join(',')];
    arr.forEach(r => {
        lines.push([
            r.trial, r.pairIdx, r.anxiousSide, r.probeOn, r.actualProbeSide,
            r.clickedSide ? r.clickedSide : '',
            r.correct, r.rt === null ? '' : r.rt, r.timestamp
        ].join(','));
    });
    return lines.join('\n');
}

/* Начальное состояние */
resetState();
updateUI();
