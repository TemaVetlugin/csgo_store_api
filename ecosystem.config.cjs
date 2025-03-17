module.exports = {
    apps: [
        {
            name: "queue-worker:default",
            script: "php artisan queue:listen --timeout=0",
            cwd: "/app/",
            instances: 1,
            max_memory_restart: "150M",
            out_file: "/app/storage/logs/queue-worker-default.log",
            error_file: "/app/storage/logs/queue-worker-default-errors.log",
            time: true
        },
        {
            name: "command:web-socket-listener",
            script: "php artisan market:web-socket-listener",
            cwd: "/app/",
            instances: 1,
            max_memory_restart: "150M",
            out_file: "/app/storage/logs/market-web-socket-listener.log",
            error_file: "/app/storage/logs/market-web-socket-listener-errors.log",
            time: true
        }
    ]
}
