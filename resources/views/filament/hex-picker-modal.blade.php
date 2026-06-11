{{-- resources/views/filament/hex-picker-modal.blade.php --}}
<div
    x-data="{
        stream: null,
        timer: null,
        hex: '#ffffff',
        status: 'init',
        videoSize: '-',

        init() {
            // stop stream global kalau ada (biar gak dobel)
            if (window.__HEX_STREAM__) {
                try { window.__HEX_STREAM__.getTracks().forEach(t => t.stop()); } catch (e) {}
                window.__HEX_STREAM__ = null;
            }

            // tunggu modal benar-benar tampil
            setTimeout(() => this.startCamera(), 200);

            // kalau modal hilang dari DOM, stop kamera
            const el = this.$el;
            const obs = new MutationObserver(() => {
                if (!document.body.contains(el)) {
                    this.stopCamera();
                    obs.disconnect();
                }
            });
            obs.observe(document.body, { childList: true, subtree: true });
        },

        async startCamera() {
            try {
                this.status = 'requesting permission...';

                const tries = [
                    { video: { facingMode: { ideal: 'environment' } }, audio: false }, // belakang ideal
                    { video: { facingMode: 'user' }, audio: false },                   // depan fallback
                    { video: true, audio: false },                                     // apapun
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
                    this.status = 'error: ' + (lastErr?.name || '') + ' ' + (lastErr?.message || '');
                    return;
                }

                this.stream = got;
                window.__HEX_STREAM__ = got;

                this.$refs.video.srcObject = this.stream;

                try { await this.$refs.video.play(); } catch (e) {}

                this.$refs.video.onloadedmetadata = () => {
                    const vw = this.$refs.video.videoWidth;
                    const vh = this.$refs.video.videoHeight;
                    this.videoSize = vw + ' x ' + vh;

                    if (!vw || !vh) {
                        this.status = 'error: video size 0';
                        return;
                    }

                    this.status = 'running';

                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => this.pickCenterAvg(), 150);
                };

            } catch (e) {
                this.status = 'error: ' + (e?.name || '') + ' ' + (e?.message || '');
            }
        },

        stopCamera() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
            if (this.stream) {
                try { this.stream.getTracks().forEach(t => t.stop()); } catch (e) {}
                this.stream = null;
            }
            window.__HEX_STREAM__ = null;
            this.status = 'stopped';
            this.videoSize = '-';
        },

        toggleCamera() {
            if (this.stream) this.stopCamera();
            else this.startCamera();
        },

        pickCenterAvg() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;

            if (!video.videoWidth || !video.videoHeight) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const cx = Math.floor(canvas.width / 2);
            const cy = Math.floor(canvas.height / 2);

            const size = 9;
            const half = Math.floor(size / 2);

            const data = ctx.getImageData(cx - half, cy - half, size, size).data;

            let r = 0, g = 0, b = 0;
            const count = size * size;

            for (let i = 0; i < data.length; i += 4) {
                r += data[i];
                g += data[i + 1];
                b += data[i + 2];
            }

            r = Math.round(r / count);
            g = Math.round(g / count);
            b = Math.round(b / count);

            this.hex = this.rgbToHex(r, g, b);
        },

        rgbToHex(r, g, b) {
            const toHex = (n) => n.toString(16).padStart(2, '0');
            return `#${toHex(r)}${toHex(g)}${toHex(b)}`.toLowerCase();
        },

        useHex() {
            // 1) kirim event global
            window.dispatchEvent(new CustomEvent('hex-picked', { detail: { hex: this.hex } }));

            // 2) fallback: isi input by id (kalau event ga ketangkap)
            const el = document.getElementById('hex_color_input');
            if (el) {
                el.value = this.hex;
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },
    }"
    x-init="init()"
    style="font-family: system-ui, Arial;"
>
    <style>
        .hx-wrap{max-width:900px}
        .hx-info{font-size:14px;color:#444;margin-bottom:12px;line-height:1.4}
        .hx-box{position:relative;border:1px solid #ddd;border-radius:14px;overflow:hidden;background:#000}
        .hx-video{width:100%;height:360px;object-fit:cover;display:block;background:#000}
        .hx-cross{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none}
        .hx-plus{width:36px;height:36px;position:relative}
        .hx-plus:before,.hx-plus:after{content:"";position:absolute;background:rgba(255,255,255,.95)}
        .hx-plus:before{width:3px;height:36px;left:50%;top:0;transform:translateX(-50%)}
        .hx-plus:after{height:3px;width:36px;top:50%;left:0;transform:translateY(-50%)}
        .hx-row{display:flex;gap:12px;align-items:center;margin-top:14px}
        .hx-swatch{width:46px;height:46px;border-radius:12px;border:1px solid #ddd}
        .hx-hex{font-weight:700}
        .hx-muted{font-size:12px;color:#666}
        .hx-btnrow{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
        .hx-btn{padding:10px 14px;border-radius:12px;border:1px solid #ddd;background:#fff;cursor:pointer}
        .hx-btn-primary{background:#f59e0b;border-color:#f59e0b;font-weight:700}
        .hx-debug{margin-top:10px;font-size:12px;color:#444;background:#f7f7f7;border:1px solid #e5e5e5;border-radius:10px;padding:10px}
    </style>

    <div class="hx-wrap">
        <div class="hx-info">
            Arahkan kamera ke katalog/swatch/kemasan, lalu letakkan warna yang mau diambil tepat di tanda <b>+</b> (tengah).
            HEX akan terbaca otomatis. Klik <b>Pakai HEX ini</b> untuk mengisi field.
        </div>

        <div class="hx-box">
            <video x-ref="video" class="hx-video" autoplay playsinline muted></video>
            <div class="hx-cross"><div class="hx-plus"></div></div>
        </div>

        <canvas x-ref="canvas" style="display:none"></canvas>

        <div class="hx-row">
            <div class="hx-swatch" :style="`background:${hex}`"></div>
            <div>
                <div class="hx-hex" x-text="hex"></div>
                <div class="hx-muted">Warna rata-rata area kecil di tengah</div>
            </div>
        </div>

        <div class="hx-btnrow">
            <button type="button" class="hx-btn hx-btn-primary" @click="useHex()">
                Pakai HEX ini
            </button>

            <button type="button" class="hx-btn" @click="toggleCamera()">
                <span x-text="stream ? 'Matikan Kamera' : 'Nyalakan Kamera'"></span>
            </button>
        </div>

        <div class="hx-debug">
            <div><b>Status:</b> <span x-text="status"></span></div>
            <div><b>Video size:</b> <span x-text="videoSize"></span></div>
            <div class="hx-muted">
                Kalau status “error”, biasanya karena: izin kamera, kamera dipakai aplikasi lain, atau butuh HTTPS saat hosting.
            </div>
        </div>
    </div>
</div>
