const CACHE = "dotprobe-v1";
const CACHE_URLS = [
    "/",
    "/assets/app.js",
    "/assets/style.css",
    "/manifest.json"
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll(CACHE_URLS))
    );
});

self.addEventListener("activate", () => self.clients.claim());

self.addEventListener("fetch", (event) => {
    const requestURL = new URL(event.request.url);

    // кэшируем только те урлы, которые в CACHE_URLS
    if (CACHE_URLS.includes(requestURL.pathname)) {
        event.respondWith(
            caches.match(event.request).then(res => res || fetch(event.request))
        );
    } else {
        // для всего остального — просто сеть, без кэша
        event.respondWith(fetch(event.request));
    }
});
