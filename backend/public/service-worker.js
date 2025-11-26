const CACHE = "dotprobe-v1";

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE).then(cache => {
            return cache.addAll([
                "/",
                "/assets/app.js",
                "/assets/style.css",
                "/manifest.json"
            ]);
        })
    );
});

self.addEventListener("activate", () => self.clients.claim());

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then(res => {
            return (
                res ||
                fetch(event.request).then(response => {
                    return caches.open(CACHE).then(cache => {
                        cache.put(event.request, response.clone());
                        return response;
                    });
                }).catch(() => res)
            );
        })
    );
});
