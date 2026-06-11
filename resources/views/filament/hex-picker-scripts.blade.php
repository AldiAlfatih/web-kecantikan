{{-- resources/views/filament/hex-picker-scripts.blade.php --}}
<script>
window.hexPickerModal = function () {
    return {
        stream: null,
        timer: null,
        hex: '#ffffff',
        status: 'idle',
        videoSize: '-',

        init() {
            this.status = 'init...';
            setTimeout(() => this.startCamera(), 250);
        },

        async startCamera() {
            try {
                this.status = 'requesting camera permission...';

                const tries = [
                    { video: { facingMode: { ideal: 'environment' } }, audio: false },
                    { video: { facingMode: 'user' }, audio: false },
                    { video: true, audio: false },
                ];

                let got = null;
                let lastErr = null;

                for (const c of tries) {
                    try {
                        got = await navigator.mediaDevices.getUserMedia(c);
                        break;
                    } catch (e) {
                        lastErr = e;
                    }
                }

                if (!got) {
                    this.status = 'ERROR getUserMedia: ' + (lastErr?.name || '') + ' ' + (lastErr?.message || '');
                    return;
                }

                this.stream = got;
                this.$refs.video.srcObject = this.stream;

                try { await this.$refs.video.play(); } catch (e) {}

                this.$refs.video.onloadedmetadata = () => {
                    const vw = this.$refs.video.videoWidth;
                    const vh = this.$refs.video.videoHeight;

                    this.videoSize = vw + ' x ' + vh;

                    if (!vw || !vh) {
                        this.status = 'WARNING: video size 0 (black). Coba browser lain / device lain.';
                        return;
                    }

                    this.status = 'camera running ✅ sampling...';
                    this.timer = setInterval(() => this.pickCenterColor(), 150);
                };

            } catch (e) {
                this.status = 'ERROR: ' + (e?.name || '') + ' ' + (e?.message || '');
                console.error(e);
            }
        },

        stopCamera() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.status = 'camera stopped';
            this.videoSize = '-';
        },

        toggleCamera() {
            if (this.stream) this.stopCamera();
            else this.startCamera();
        },

        pickCenterColor() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;

            if (!video.videoWidth || !video.videoHeight) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const x = Math.floor(canvas.width / 2);
            const y = Math.floor(canvas.height / 2);

            const pixel = ctx.getImageData(x, y, 1, 1).data;
            const [r, g, b] = pixel;

            this.hex = this.rgbToHex(r, g, b);
        },

        rgbToHex(r, g, b) {
            const toHex = (n) => n.toString(16).padStart(2, '0');
            return `#${toHex(r)}${toHex(g)}${toHex(b)}`.toLowerCase();
        },

        useHex() {
            // kirim event ke Filament
            window.dispatchEvent(new CustomEvent('hex-picked', { detail: { hex: this.hex }}));
        },
    }
}
</script>
