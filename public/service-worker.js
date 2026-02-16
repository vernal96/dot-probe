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

    if (CACHE_URLS.includes(requestURL.pathname)) {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    return caches.open(CACHE).then((cache) => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                })
                .catch(() => caches.match(event.request))
        );
    } else {
        event.respondWith(fetch(event.request));
    }
});
