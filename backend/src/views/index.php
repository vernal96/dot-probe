<?php

/** @var array $items */

$items = array_values($items);

?>

<script>
    let images = <?=json_encode($items)?>
</script>

<div class="row mb-3">
    <div class="col-lg-8">
        <div class="card p-3">
            <h5 class="card-title">Настройки теста</h5>

            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Где появится смайлик?</label>
                    <select id="probeTarget" class="form-select">
                        <option value="anxious">На anxious</option>
                        <option value="neutral">На neutral</option>
                        <option value="random">Случайно (50/50)</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Смайлик / знак</label>
                    <select id="probeIcon" class="form-select">
                        <option value="⭐">⭐</option>
                        <option value="⚫">⚫</option>
                        <option value="🙂">🙂</option>
                        <option value="🔴">🔴</option>
                        <option value="❤">❤</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Пауза до смайлика</label>
                    <input id="delayRange" type="text" class="form-control" value="2000 - 3000 ms" readonly>
                </div>

            </div>

            <div class="mt-3">
                <button id="startBtn" class="btn btn-primary">Начать</button>
                <button id="stopBtn" class="btn btn-secondary ms-2" disabled>Остановить</button>
                <button id="exportBtn" class="btn btn-outline-success ms-2" disabled>Экспорт CSV</button>
            </div>

            <div class="mt-3 text-muted small">
                Пары изображений фиксированы. Для каждой пары anxious/neutral случайно размещаются слева/справа.
                Ребёнок может нажать по левой или правой половине (или по большой картинке), чтобы указать, где появился смайлик.
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-3">
            <h6>Прогресс / Статус</h6>
            <div id="status">Готов.</div>
            <hr/>
            <div>
                <strong>Текущая пара:</strong> <span id="trialInfo">0 / 0</span>
            </div>
            <div class="mt-2">
                <strong>Результаты (последние 10):</strong>
                <pre id="log" class="border mt-2">—</pre>
            </div>
        </div>
    </div>
</div>

<!-- Stage: большая область с двумя изображениями -->
<div id="stage" class="row g-3 d-none">
    <div class="col-12 col-md-6">
        <div id="leftBox" class="image-box clickable border" title="Нажмите сюда">
            <img id="leftImg" src="" alt="left" />
            <div id="probeLeft" class="probe"></div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div id="rightBox" class="image-box clickable border" title="Нажмите сюда">
            <img id="rightImg" src="" alt="right" />
            <div id="probeRight" class="probe"></div>
        </div>
    </div>
</div>

<!-- Скрытая область для CSV -->
<textarea id="csvOut" style="display:none;"></textarea>
<script src="/assets/app.js"></script>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register("service-worker.js")
            .then(() => console.log("SW registered"))
            .catch(err => console.error("SW error", err));
    }
</script>