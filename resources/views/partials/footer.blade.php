<footer class="footer mt-auto py-3 bg-white border-top">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">

            <div class="text-muted text-center text-sm-start">
                © <span id="year"></span> <span class="fw-semibold text-dark">e-Pusara</span>
                <span class="ms-1">— All rights reserved.</span>
            </div>

            <div class="text-muted text-center text-sm-end">
                Developed for <span class="fw-semibold text-dark">Humaira Nazri</span>
            </div>

        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const y = document.getElementById('year');
        if (y) y.textContent = new Date().getFullYear();
    });
</script>