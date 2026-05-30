import Cropper from 'cropperjs';

document.addEventListener('alpine:init', () => {
    Alpine.data('imageEditor', () => ({
        open: false,
        imageUrl: '',
        originalPath: '',
        cropper: null,
        loading: false,
        saved: false,
        mode: 'crop',

        brightness: 100,
        contrast: 100,
        saturation: 100,
        sharpness: 0,

        activeFilter: 'none',
        selectedAspect: 'free',

        filters: {
            none:      { label: 'أصلي',       css: '',                                                         preview: 'brightness(1)' },
            bw:        { label: 'أبيض وأسود',  css: 'grayscale(100%)',                                         preview: 'grayscale(1)' },
            sepia:     { label: 'كلاسيكي',    css: 'sepia(80%)',                                              preview: 'sepia(0.8)' },
            warm:      { label: 'دافئ',        css: 'saturate(140%) hue-rotate(-15deg) brightness(108%)',     preview: 'saturate(1.4) hue-rotate(-15deg)' },
            cool:      { label: 'بارد',        css: 'saturate(75%) hue-rotate(25deg) brightness(95%)',        preview: 'saturate(0.75) hue-rotate(25deg)' },
            vivid:     { label: 'نابض',        css: 'saturate(180%) contrast(115%) brightness(102%)',         preview: 'saturate(1.8) contrast(1.15)' },
            fade:      { label: 'باهت',        css: 'brightness(115%) contrast(80%) saturate(65%)',           preview: 'brightness(1.15) contrast(0.8) saturate(0.65)' },
            dramatic:  { label: 'درامي',       css: 'contrast(140%) brightness(88%) saturate(120%)',          preview: 'contrast(1.4) brightness(0.88)' },
        },

        aspects: {
            free:  { label: 'حر',   value: NaN },
            '1:1': { label: '1:1',  value: 1 },
            '4:3': { label: '4:3',  value: 4/3 },
            '3:2': { label: '3:2',  value: 3/2 },
            '16:9':{ label: '16:9', value: 16/9 },
            '2:3': { label: '2:3',  value: 2/3 },
            '3:4': { label: '3:4',  value: 3/4 },
        },

        openEditor(imageUrl, originalPath) {
            this.imageUrl    = imageUrl;
            this.originalPath = originalPath;
            this.open        = true;
            this.saved       = false;
            this.resetAll();
            this.$nextTick(() => this.initCropper());
        },

        initCropper() {
            const img = this.$refs.cropImg;
            if (!img) return;
            if (this.cropper) this.cropper.destroy();
            this.cropper = new Cropper(img, {
                aspectRatio: NaN,
                viewMode: 1,
                dragMode: 'crop',
                autoCropArea: 0.85,
                responsive: true,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: true,
                ready: () => { this.applyVisualFilter(); },
            });
        },

        resetAll() {
            this.mode          = 'crop';
            this.brightness    = 100;
            this.contrast      = 100;
            this.saturation    = 100;
            this.sharpness     = 0;
            this.activeFilter  = 'none';
            this.selectedAspect = 'free';
        },

        setAspect(key) {
            this.selectedAspect = key;
            this.cropper?.setAspectRatio(this.aspects[key].value);
        },

        rotateLeft()  { this.cropper?.rotate(-90); },
        rotateRight() { this.cropper?.rotate(90); },

        flipH() {
            const d = this.cropper?.getData();
            this.cropper?.scaleX((d?.scaleX ?? 1) === -1 ? 1 : -1);
        },
        flipV() {
            const d = this.cropper?.getData();
            this.cropper?.scaleY((d?.scaleY ?? 1) === -1 ? 1 : -1);
        },

        zoomIn()    { this.cropper?.zoom(0.15); },
        zoomOut()   { this.cropper?.zoom(-0.15); },
        resetCrop() { this.cropper?.reset(); this.resetAll(); this.initCropper(); },

        setFilter(key) {
            this.activeFilter = key;
            if (key !== 'none') {
                this.brightness = 100;
                this.contrast   = 100;
                this.saturation = 100;
            }
            this.applyVisualFilter();
        },

        applyVisualFilter() {
            const img = this.$refs.cropImg;
            if (!img) return;
            img.style.filter = this.getFilterCSS();
        },

        getFilterCSS() {
            if (this.activeFilter !== 'none') {
                return this.filters[this.activeFilter].css;
            }
            let f = `brightness(${this.brightness}%) contrast(${this.contrast}%) saturate(${this.saturation}%)`;
            return f;
        },

        async saveImage() {
            if (!this.cropper || this.loading) return;
            this.loading = true;

            try {
                const canvas = this.cropper.getCroppedCanvas({
                    maxWidth: 2400,
                    maxHeight: 2400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                // Apply CSS filters onto a second canvas
                const final  = document.createElement('canvas');
                final.width  = canvas.width;
                final.height = canvas.height;
                const ctx    = final.getContext('2d');
                ctx.filter   = this.getFilterCSS();
                ctx.drawImage(canvas, 0, 0);

                await new Promise((resolve, reject) => {
                    final.toBlob(async (blob) => {
                        const fd = new FormData();
                        fd.append('image', blob, 'edited.webp');
                        fd.append('original_path', this.originalPath);

                        const res = await fetch('/admin/image-editor/save', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                            },
                            body: fd,
                        });

                        const data = await res.json();

                        if (data.success) {
                            // Force browser to reload the image preview
                            const preview = document.querySelector(`img[src*="${this.originalPath.split('/').pop()}"]`);
                            if (preview) {
                                const bust = data.url + '?t=' + Date.now();
                                preview.src = bust;
                            }
                            this.saved = true;
                            setTimeout(() => this.close(), 800);
                            resolve();
                        } else {
                            reject(new Error(data.message ?? 'فشل الحفظ'));
                        }
                    }, 'image/webp', 0.88);
                });
            } catch (e) {
                console.error(e);
                alert('حدث خطأ أثناء الحفظ: ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        close() {
            this.open = false;
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
        },
    }));
});
