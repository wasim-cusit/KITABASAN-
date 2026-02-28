# Storage images (profile, course covers)

Uploaded images are served by Laravel via **GET /files/{path}** (e.g. `/files/profiles/avatar.png`, `/files/courses/cover.png`). This route always hits the app, so images work on admin, teacher, student, and public pages regardless of the `public/storage` symlink.

If you see broken images, ensure the route is registered (see `routes/web.php`: `storage.serve`) and that files exist under `storage/app/public/`.
