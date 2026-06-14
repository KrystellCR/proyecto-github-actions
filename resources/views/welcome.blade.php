<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Task API test</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --bg: #f7f8fb;
            --panel: #ffffff;
            --text: #172033;
            --muted: #647087;
            --line: #dfe4ec;
            --primary: #0f766e;
            --primary-dark: #115e59;
            --danger: #b42318;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
        }

        main {
            width: min(1040px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.05;
        }

        p {
            margin: 0;
            color: var(--muted);
        }

        .status {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--panel);
            color: var(--muted);
            font-size: 14px;
            white-space: nowrap;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 380px) minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }

        section {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(23, 32, 51, 0.06);
        }

        h2 {
            margin: 0 0 16px;
            font-size: 18px;
        }

        form {
            display: grid;
            gap: 12px;
        }

        label {
            display: grid;
            gap: 6px;
            color: var(--muted);
            font-size: 14px;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 11px 12px;
            color: var(--text);
            font: inherit;
            background: #fff;
        }

        textarea {
            min-height: 96px;
            resize: vertical;
        }

        button {
            min-height: 40px;
            border: 0;
            border-radius: 6px;
            padding: 0 14px;
            color: #fff;
            background: var(--primary);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: var(--primary-dark);
        }

        button.secondary {
            border: 1px solid var(--line);
            color: var(--text);
            background: #fff;
        }

        button.secondary:hover {
            background: #f1f4f8;
        }

        button.danger {
            background: var(--danger);
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
        }

        .tasks {
            display: grid;
            gap: 10px;
        }

        .task {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
            background: #fff;
        }

        .task h3 {
            margin: 0 0 4px;
            font-size: 16px;
        }

        .task.done h3 {
            text-decoration: line-through;
            color: var(--muted);
        }

        .task-actions {
            display: flex;
            gap: 8px;
        }

        .empty {
            border: 1px dashed var(--line);
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
        }

        .api-box {
            margin-top: 20px;
        }

        code,
        pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        }

        pre {
            overflow: auto;
            min-height: 120px;
            max-height: 280px;
            margin: 12px 0 0;
            padding: 14px;
            border-radius: 8px;
            background: #101828;
            color: #d1fadf;
            font-size: 13px;
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            header,
            .layout,
            .toolbar {
                display: grid;
            }

            .task {
                grid-template-columns: 1fr;
            }

            .task-actions {
                justify-content: stretch;
            }

            .task-actions button {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <div>
                <h1>Laravel Task API TEST</h1>
                <p>Interfaz simple para probar los endpoints usados por GitHub Actions y Render.</p>
            </div>
            <span class="status" id="status">Cargando API...</span>
        </header>

        <div class="layout">
            <section>
                <h2>Nueva tarea</h2>
                <form id="task-form">
                    <label>
                        Titulo
                        <input id="title" name="title" required maxlength="255" placeholder="Ej. Probar workflow CI">
                    </label>
                    <label>
                        Descripcion
                        <textarea id="description" name="description" placeholder="Detalle opcional"></textarea>
                    </label>
                    <button type="submit">Crear tarea</button>
                </form>

                <div class="api-box">
                    <h2>Ultima respuesta API</h2>
                    <pre id="api-output">{}</pre>
                </div>
            </section>

            <section>
                <div class="toolbar">
                    <h2>Tareas</h2>
                    <button class="secondary" type="button" id="refresh">Actualizar</button>
                </div>
                <div class="tasks" id="tasks"></div>
            </section>
        </div>
    </main>

    <script>
        const tasksEl = document.querySelector('#tasks');
        const form = document.querySelector('#task-form');
        const output = document.querySelector('#api-output');
        const statusEl = document.querySelector('#status');
        const refreshBtn = document.querySelector('#refresh');

        // Muestra en pantalla la respuesta JSON que regresa la API.
        function showApiResult(data) {
            output.textContent = JSON.stringify(data, null, 2);
        }

        // Actualiza el estado visual de conexion con la API.
        function setStatus(message) {
            statusEl.textContent = message;
        }

        // Funcion reutilizable para hacer peticiones HTTP a Laravel.
        async function request(url, options = {}) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                ...options,
            });

            if (response.status === 204) {
                return null;
            }

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            return data;
        }

        // Dibuja la lista de tareas usando los datos recibidos desde /api/tasks.
        function renderTasks(tasks) {
            tasksEl.innerHTML = '';

            if (tasks.length === 0) {
                tasksEl.innerHTML = '<div class="empty">Todavia no hay tareas.</div>';
                return;
            }

            for (const task of tasks) {
                const item = document.createElement('article');
                item.className = `task ${task.is_completed ? 'done' : ''}`;
                const content = document.createElement('div');
                const title = document.createElement('h3');
                const description = document.createElement('p');
                const actions = document.createElement('div');
                const toggleButton = document.createElement('button');
                const deleteButton = document.createElement('button');

                title.textContent = task.title;
                description.textContent = task.description ?? 'Sin descripcion';
                actions.className = 'task-actions';

                toggleButton.className = 'secondary';
                toggleButton.type = 'button';
                toggleButton.textContent = task.is_completed ? 'Reabrir' : 'Completar';

                deleteButton.className = 'danger';
                deleteButton.type = 'button';
                deleteButton.textContent = 'Borrar';

                content.append(title, description);
                actions.append(toggleButton, deleteButton);
                item.append(content, actions);

                // PATCH actualiza una tarea existente.
                toggleButton.addEventListener('click', async () => {
                    const updated = await request(`/api/tasks/${task.id}`, {
                        method: 'PATCH',
                        body: JSON.stringify({ is_completed: !task.is_completed }),
                    });
                    showApiResult(updated);
                    await loadTasks();
                });

                // DELETE elimina una tarea por su id.
                deleteButton.addEventListener('click', async () => {
                    await request(`/api/tasks/${task.id}`, { method: 'DELETE' });
                    showApiResult({ deleted: task.id });
                    await loadTasks();
                });

                tasksEl.appendChild(item);
            }
        }

        // GET consulta todas las tareas guardadas.
        async function loadTasks() {
            try {
                const tasks = await request('/api/tasks');
                renderTasks(tasks);
                showApiResult(tasks);
                setStatus('API conectada');
            } catch (error) {
                setStatus('Error en API');
                showApiResult(error);
            }
        }

        // POST envia los datos del formulario para crear una tarea.
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = {
                title: form.title.value,
                description: form.description.value || null,
            };

            try {
                const task = await request('/api/tasks', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                form.reset();
                showApiResult(task);
                await loadTasks();
            } catch (error) {
                showApiResult(error);
            }
        });

        refreshBtn.addEventListener('click', loadTasks);
        loadTasks();
    </script>
</body>
</html>
