### 📁 Root project
```
intelli-lab/
│
├── app/                      # Laravel backend logic
├── bootstrap/
├── config/
├── database/
├── public/
├── routes/
│   ├── api.php              # REST API routes (Vue talks to this)
│   └── web.php              # ONLY SPA fallback + auth redirects
│
├── resources/
│   ├── js/                  # Vue application lives here
│   │   ├── app.js           # Vue entry point
│   │   ├── bootstrap.js     # axios, libs, config
│   │   │
│   │   ├── router/          # Vue Router
│   │   │   └── index.js
│   │   │
│   │   ├── stores/          # Pinia (state management)
│   │   │   ├── authStore.js
│   │   │   ├── taskStore.js
│   │   │
│   │   ├── layouts/         # App shells
│   │   │   ├── DefaultLayout.vue
│   │   │   ├── CodingLayout.vue
│   │   │
│   │   ├── views/           # Pages (route targets)
│   │   │   ├── Dashboard.vue
│   │   │   ├── Coding.vue
│   │   │   ├── Modelling.vue
│   │   │   ├── Login.vue
│   │   │
│   │   ├── components/      # Reusable UI parts
│   │   │   ├── CodeEditor.vue
│   │   │   ├── TaskPanel.vue
│   │   │   ├── FeedbackBox.vue
│   │   │
│   │   ├── services/        # API layer (VERY important)
│   │   │   ├── api.js
│   │   │   ├── taskService.js
│   │   │   ├── authService.js
│   │
│   ├── css/
│   └── views/
│       └── app.blade.php    # SPA entry point (IMPORTANT)
│
├── vite.config.js
├── package.json
├── composer.json
└── .env
```